import { createApp, ref, computed } from '/test/public/assets/js/vue.esm-browser.js';

const app = createApp({
  setup() {
    // Reactive state
    const events = ref(window.initialEvents || []);
    const searchQuery = ref('');
    const sortKey = ref('start_time');
    const sortOrder = ref('desc');
    const isLoading = ref(false);

    // Computed property for filtered and sorted events
    const filteredEvents = computed(() => {
      let filtered = [...events.value];

      // Filter by search query
      if (searchQuery.value) {
        filtered = filtered.filter(event =>
          event.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        );
      }

      // Sort events
      filtered.sort((a, b) => {
        const aValue = a[sortKey.value] || '';
        const bValue = b[sortKey.value] || '';
        
        if (sortKey.value === 'start_time' || sortKey.value === 'end_time') {
          return sortOrder.value === 'asc' 
            ? new Date(aValue) - new Date(bValue)
            : new Date(bValue) - new Date(aValue);
        }
        
        return sortOrder.value === 'asc'
          ? aValue.toString().localeCompare(bValue.toString())
          : bValue.toString().localeCompare(aValue.toString());
      });

      return filtered;
    });

    // Toggle sort order
    const toggleSort = (key) => {
      if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
      } else {
        sortKey.value = key;
        sortOrder.value = 'asc';
      }
    };

    // Delete event with confirmation
    const deleteEvent = async (id) => {
      if (confirm('Are you sure you want to delete this event?')) {
        isLoading.value = true;
        try {
          // Simulate API call to delete
          window.location.href = `events.php?delete=${id}`;
        } catch (error) {
          console.error('Error deleting event:', error);
          alert('Failed to delete event');
        } finally {
          isLoading.value = false;
        }
      }
    };

    return {
      events,
      searchQuery,
      filteredEvents,
      sortKey,
      sortOrder,
      toggleSort,
      deleteEvent,
      isLoading
    };
  }
});

// Mount the app
app.mount('#event-list-app');