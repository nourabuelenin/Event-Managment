import { createApp, ref } from "/test/public/assets/js/vue.esm-browser.js";

const app = createApp({
  setup() {
    const formData = ref({
      username: "",
      password: "",
    });
    const errors = ref({});
    const flash = ref(window.flash || null);
    const csrf_token = ref(window.csrf_token || "");

    const validate = () => {
      errors.value = {};
      if (!formData.value.username)
        errors.value.username = "Username is required";
      if (!formData.value.password)
        errors.value.password = "Password is required";
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

app.mount("#login-app");
