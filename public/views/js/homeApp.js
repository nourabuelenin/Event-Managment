import { createApp, ref, nextTick } from "/test/public/assets/js/vue.esm-browser.js";

const App = {
  setup() {
    // Reactive state
    const events = ref(Array.isArray(window.initialEvents) ? window.initialEvents : []);
    const event = ref({
      name: "",
      description: "",
      start_time: "",
      end_time: "",
      organizer_name: "",
      venue_name: "",
    });
    const isLoading = ref(false);
    const showModal = ref(false);
    const modalTitle = ref("");
    const currentUser = ref(window.currentUser || null);

    // Fetch all events
    const fetchEvents = async () => {
      isLoading.value = true;
      try {
        const res = await fetch(`${window.BASE_URL}/api/home`, {
          credentials: "same-origin",
        });
        if (!res.ok) {
          throw new Error(`HTTP error! Status: ${res.status}`);
        }
        const json = await res.json();
        if (json.status === "success") {
          events.value = json.data || [];
        } else {
          console.error("API error:", json.message);
          alert("Failed to load events: " + (json.message || "Unknown error"));
          events.value = [];
        }
      } catch (e) {
        console.error("Fetch error in fetchEvents:", e);
        alert("Failed to load events. Please try again later.");
        events.value = [];
      } finally {
        isLoading.value = false;
      }
    };

    // Load event details for modal
    const loadForm = async (url) => {
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
          event.value = {
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
          modalTitle.value = "Event Details";
          showModal.value = true;
          await nextTick();
          console.log("Event data set, showModal:", showModal.value);
        } else {
          console.error(
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
    };

    // Open event details modal
    const openView = async (id) => {
      modalTitle.value = "Event Details";
      await loadForm(`${window.BASE_URL}/api/events/${id}`);
    };

    // Close modal
    const closeModal = () => {
      showModal.value = false;
      event.value = {
        name: "",
        description: "",
        start_time: "",
        end_time: "",
        organizer_name: "",
        venue_name: "",
      };
      modalTitle.value = "";
    };

    // Fetch events on component mount
    fetchEvents();

    // Return reactive state and methods
    return {
      events,
      event,
      isLoading,
      showModal,
      modalTitle,
      currentUser,
      fetchEvents,
      openView,
      loadForm,
      closeModal,
    };
  },
};

// Mount the app
createApp(App).mount("#home-app");