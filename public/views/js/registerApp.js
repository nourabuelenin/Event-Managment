import { createApp, ref } from "/test/public/assets/js/vue.esm-browser.js";

const app = createApp({
  setup() {
    const formData = ref({
      username: "",
      email: "",
      password: "",
      confirm_password: "",
    });
    const errors = ref({});
    const flash = ref(window.flash || null);
    const csrf_token = ref(window.csrf_token || "");

    const validate = () => {
      errors.value = {};
      if (!formData.value.username)
        errors.value.username = "Username is required";
      if (!formData.value.email) {
        errors.value.email = "Email is required";
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.value.email)) {
        errors.value.email = "Invalid email format";
      }
      if (!formData.value.password) {
        errors.value.password = "Password is required";
      } else if (formData.value.password.length < 6) {
        errors.value.password = "Password must be at least 6 characters";
      }
      if (formData.value.password !== formData.value.confirm_password) {
        errors.value.confirm_password = "Passwords do not match";
      }
      return Object.keys(errors.value).length === 0;
    };

    const submitForm = (e) => {
      if (!validate()) {
        e.preventDefault();
      }
    };

    return {
      formData,
      errors,
      flash,
      csrf_token,
      submitForm,
    };
  },
});

app.mount("#register-app");
