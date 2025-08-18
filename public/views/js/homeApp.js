import {
  createApp,
  ref,
  nextTick,
} from "/test/public/assets/js/vue.esm-browser.js";

const app = createApp({
  data() {
    return {
      events: Array.isArray(window.initialEvents) ? window.initialEvents : [],
      event: {
        name: "",
        description: "",
        start_time: "",
        end_time: "",
        organizer_name: "",
        venue_name: "",
      },
      isLoading: false,
      showModal: false,
      modalContent: "",
      modalTitle: "",
    };
  },
  methods: {
    async fetchEvents() {
      this.isLoading = true;
      try {
        const res = await fetch(`${window.BASE_URL}/api/events`, {
          credentials: "same-origin",
        });
        if (!res.ok) {
          throw new Error(`HTTP error! Status: ${res.status}`);
        }
        const json = await res.json();
        if (json.status === "success") {
          this.events = json.data || [];
        } else {
          console.error("API error:", json.message);
          alert("Failed to load events: " + (json.message || "Unknown error"));
          this.events = [];
        }
      } catch (e) {
        console.error("Fetch error in fetchEvents:", e);
        alert("Failed to load events. Please try again later.");
        this.events = [];
      } finally {
        this.isLoading = false;
      }
    },
    async openView(id) {
      this.modalTitle = "Event Details";
      await this.loadForm(`${window.BASE_URL}/api/events/${id}`);
    },
    async loadForm(url) {
      console.log("Fetching URL:", url);
      try {
        const res = await fetch(url, {
          headers: { "X-Requested-With": "XMLHttpRequest" },
          credentials: "same-origin",
        });
        console.log("Response status:", res.status);
        const json = await res.json();
        console.log("Response JSON:", json);
        if (res.ok && json.status === "success") {
          this.event = {
            name: json.data.name || "",
            description: json.data.description || "No description available",
            start_time: json.data.start_time
              ? new Date(json.data.start_time).toLocaleString()
              : "N/A",
            end_time: json.data.end_time
              ? new Date(json.data.end_time).toLocaleString()
              : "N/A",
            organizer_name: json.data.organizer_name || "N/A",
            venue_name: json.data.venue_name || "N/A",
          };
          this.modalTitle = "Event Details";
          this.showModal = true;
          await nextTick();
          console.log("Event data set, showModal:", this.showModal);
        } else {
          console.log(
            "Response not OK or failed, status:",
            res.status,
            "message:",
            json.message
          );
          alert(
            "Failed to load event details: " + (json.message || "Unknown error")
          );
        }
      } catch (e) {
        console.error("Fetch error:", e);
        alert("Failed to load event details");
      }
    },
    closeModal() {
      this.showModal = false;
      this.modalContent = "";
      this.modalTitle = "";
    },
  },
  async mounted() {
    await this.fetchEvents();
  },
});

app.mount("#home-app");
