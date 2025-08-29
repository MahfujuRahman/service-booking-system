<template>
  <Layout>
    <div class="row justify-content-center align-items-center gap-2 py-5">
      <div class="col-md-5">
        <form @submit.prevent="LoginSubmitHandler">
          <h3>Login Here</h3>
          <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" type="email" placeholder="  email" name="email" />
          </div>
          <div class="form-group password-icon">
            <label for="password">Password</label>
            <div class="password-icon">
              <input class="form-control" :type="showPassword ? 'text' : 'password'" placeholder="  password"
                name="password" value="@12345678" />
              <i class="fa-solid fa-eye-slash" :class="[
                { 'fa-eye': showPassword },
                passwordError ? 'top-33' : '',
              ]" @click="showPassword = !showPassword"></i>
            </div>
          </div>

          <button class="my-3 btn btn-outline-success" type="submit" id="spiner">
            <span v-if="!loading">Log In</span>
            <template v-if="loading">
              <span class="spinner-border spinner-border-sm mx-2" role="status" aria-hidden="true"></span>
              <span class="">Loading...</span>
            </template>
          </button>
          <span>Dont have an account ?</span>
          <Link href="/register" class="font-size-12 text-primary">
          Register</Link>
          <br />
          <Link href="/forgot-password" class="text-info">Forgot Password ?</Link>
        </form>
      </div>
      <div class="col-md-5">
        <div id="userList">
          <table class="table table-dark table-striped table-hover table-bordered">
            <thead class="sticky-top">
              <tr>
                <th scope="col">Email</th>
                <!-- <th scope="col">Password</th> -->
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Super Admin</td>
                <!-- <td>@12345678</td> -->
                <td>
                  <button @click="setPassword('superadmin@gmail.com')" class="btn btn-outline-info">
                    Login
                  </button>
                </td>
              </tr>

              <tr>
                <td>Admin</td>
                <!-- <td>@12345678</td> -->
                <td>
                  <button @click="setPassword('admin@gmail.com')" class="btn btn-outline-info">
                    Login
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </Layout>
</template>
<script>
import Layout from "./Layout/Layout.vue";
import { Link } from "@inertiajs/vue3";

export default {
  components: { Layout, Link },

  data() {
    return {
      loading: false,
      showPassword: false,
      passwordError: false,
      email: "",
      password: "",
      rememberMe: false,
    };
  },

  async mounted() {
    this.loadRememberedCredentials();
    // If an admin token exists, validate it and redirect to conversation
    const token = localStorage.getItem('admin_token');
    if (token) {
      // set temporary auth header for validation
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      try {
        const resp = await axios.get('/check_user');
        if (resp.data?.status === 'success') {
          const prev_url = window.sessionStorage.getItem('prevurl');
          let redirectTo;
          if (prev_url && !prev_url.includes('/login')) {
            // normalize prev_url to an absolute URL when possible
            if (prev_url.startsWith('http')) {
              redirectTo = prev_url;
            } else if (prev_url.startsWith('/')) {
              // already a root path
              redirectTo = window.location.origin + prev_url;
            } else if (prev_url.startsWith('#')) {
              // hash-only routes should be resolved under /super-admin
              redirectTo = window.location.origin + '/super-admin' + prev_url;
            } else {
              redirectTo = window.location.origin + '/' + prev_url;
            }
          } else {
            redirectTo = window.location.origin + '/super-admin#/dashboard';
          }
          window.location.href = redirectTo;
          return;
        } else {
          // invalid token — remove and continue to login
          localStorage.removeItem('admin_token');
        }
      } catch (err) {
        console.warn('Token validation failed, clearing token', err);
        localStorage.removeItem('admin_token');
      } finally {
        // clear temporary header
        delete axios.defaults.headers.common['Authorization'];
      }
    }

    // If app store indicates authenticated and auth_info is loaded, redirect
    if (this.is_auth && this.auth_info) {
      const prev_url = window.sessionStorage.getItem('prevurl');
      let redirectTo;
      if (prev_url && !prev_url.includes('/login')) {
        if (prev_url.startsWith('http')) {
          redirectTo = prev_url;
        } else if (prev_url.startsWith('/')) {
          redirectTo = window.location.origin + prev_url;
        } else if (prev_url.startsWith('#')) {
          redirectTo = window.location.origin + '/super-admin' + prev_url;
        } else {
          redirectTo = window.location.origin + '/' + prev_url;
        }
      } else {
        redirectTo = window.location.origin + '/super-admin#/message/conversation';
      }
      window.location.href = redirectTo;
    }
  },

  methods: {
    LoginSubmitHandler: async function () {
      try {
        this.loading = true;

        // Handle remember me functionality
        if (this.rememberMe) {
          this.saveCredentials();
        } else {
          this.clearSavedCredentials();
        }

        let formData = new FormData();
        formData.append("email", this.email);
        formData.append("password", this.password);
        formData.append("remember", this.rememberMe);

        let response = await axios.post("/login", formData);
        if (response.data?.status === "success") {
          let data = response.data?.data;
          if (data.access_token) {
            window.s_alert("Login Successfully");
            localStorage.setItem("admin_token", data.access_token);
            if (data.user?.role_id == 1) {
              window.location.href = "super-admin#/dashboard";
            } else if (data.user?.role_id == 2) {
              window.location.href = "customer#/dashboard";
            } else {
              window.location.href = "login";
            }
          }
        }
      } catch (error) {
        console.error("Login error", error);
        window.s_alert("Login failed. Please check your credentials.", "error");
      } finally {
        this.loading = false;
      }
    },

    saveCredentials() {
      const credentials = {
        email: this.email,
        password: this.password,
        rememberMe: this.rememberMe,
      };
      localStorage.setItem("rememberedCredentials", JSON.stringify(credentials));
    },

    clearSavedCredentials() {
      localStorage.removeItem("rememberedCredentials");
    },

    loadRememberedCredentials() {
      const savedCredentials = localStorage.getItem("rememberedCredentials");
      if (savedCredentials) {
        try {
          const credentials = JSON.parse(savedCredentials);
          this.email = credentials.email || "";
          this.password = credentials.password || "";
          this.rememberMe = credentials.rememberMe || false;
        } catch (error) {
          console.error("Error loading remembered credentials:", error);
          this.clearSavedCredentials();
        }
      }
    },

    setPassword(email) {
      this.email = email;
    },
  },
};
</script>

<style scoped>
.top-33 {
  top: 33% !important;
}
</style>
