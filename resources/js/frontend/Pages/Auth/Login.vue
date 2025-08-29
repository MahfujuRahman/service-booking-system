<template>
  <Layout>
    <div class="row justify-content-center align-items-center gap-2 py-5">
      <div class="col-md-6">
        <form @submit.prevent="LoginSubmitHandler">
          <h3>Login Here</h3>
          <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" type="email" placeholder="email" name="email" v-model="email" />
          </div>
          <div class="form-group password-icon">
            <label for="password">Password</label>
            <div class="password-icon">
              <input class="form-control" :type="showPassword ? 'text' : 'password'" placeholder="password"
                name="password" v-model="password" />
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

    // Check if user is already authenticated and redirect accordingly
    await this.checkAndRedirect();
  },

  methods: {
    async checkAndRedirect() {
      const token = localStorage.getItem('admin_token');
      if (!token) {
        console.log('No token found, staying on login page');
        return; // No token, stay on login page
      }

      console.log('Token found, validating:', token.substring(0, 20) + '...');

      // Set auth header and validate token
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

      try {
        const resp = await axios.get('/check_user');
        if (resp.data?.status === 'success') {
          const user = resp.data?.data?.user || resp.data?.user || resp.data?.data;

          // Clear any previous URL to prevent loops
          window.sessionStorage.removeItem('prevurl');

          // Redirect based on role
          if (user?.role_id == 1) {
            window.location.href = window.location.origin + '/super-admin#/dashboard';
          } else if (user?.role_id == 2) {
            window.location.href = window.location.origin + '/';
          } else {
            window.location.href = window.location.origin + '/login';
          }
        } else {
          console.log('Invalid token response:', resp.data);
          // Invalid token, remove it
          localStorage.removeItem('admin_token');
        }
      } catch (err) {
        console.warn('Token validation failed:', err);
        localStorage.removeItem('admin_token');
      } finally {
        // Clean up auth header
        delete axios.defaults.headers.common['Authorization'];
      }
    },

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
              window.location.href = window.location.origin + "/super-admin#/dashboard";
            } else if (data.user?.role_id == 2) {
              // Customer - redirect to home page
              window.location.href = window.location.origin + "/";
            } else {
              window.location.href = window.location.origin + "/login";
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
  },
};
</script>

<style scoped>
.top-33 {
  top: 33% !important;
}
</style>
