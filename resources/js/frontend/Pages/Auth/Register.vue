<template>
  <Layout>
    <div class="row justify-content-center align-items-center py-5">
      <div class="col-md-6">
        <form @submit.prevent="RegisterSubmitHandler">
          <h3>Register Here</h3>
          <div class="form-group">
            <label for="name">Name</label>
            <input
              class="form-control"
              type="name"
              placeholder="  name"
              name="name"
              onchange="errorReset(event)"
            />
            <p class="alert-danger" id="name"></p>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input
              class="form-control"
              type="email"
              placeholder="  email"
              name="email"
              onchange="errorReset(event)"
            />
            <p class="alert-danger" id="email"></p>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <div class="password-icon">
              <input
                class="form-control"
                :type="showPassword ? 'text' : 'password'"
                placeholder="  password"
                name="password"
                value="@12345678"
                onchange="errorReset(event)"
              />
              <i
                class="fa-solid fa-eye-slash"
                :class="{ 'fa-eye': showPassword }"
                @click="showPassword = !showPassword"
              ></i>
            </div>

            <p class="alert-danger" id="password"></p>
          </div>

          <button
            class="my-4 btn btn-outline-success"
            type="submit"
            id="spiner"
          >
            <span v-if="!loading">Register</span>
            <template v-if="loading">
              <span
                class="spinner-border spinner-border-sm mx-2"
                role="status"
                aria-hidden="true"
              ></span>
              <span class="">Loading...</span>
            </template>
          </button>
          <span>Dont have an account ?</span>
          <Link href="/login" class="font-size-12 text-primary"> Login</Link>
          <br />
          <Link href="/forgot-password" class="text-info"
            >Forgot Password ?</Link
          >
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
      name: "",
      phone_number: "",
      email: "",
      password: "",
      showPassword: false,
      loading: false,
    };
  },
  methods: {
    RegisterSubmitHandler: async function () {
      try {
        this.loading = true;
        let formData = new FormData(event.target);
        let response = await axios.post("/register", formData);
        console.log(response);

        if (response.data?.status === "success") {
          let data = response.data?.data;
          if (data.access_token) {
            window.s_alert("Register Successfully");
            localStorage.setItem("admin_token", data.access_token);
            if (data.user) {
              window.location.href = "super-admin#/message/conversation";
            } else {
              window.location.href = "login";
            }
          }
        }
      } catch (error) {
        console.error("Login error", error);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>