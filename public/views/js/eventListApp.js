import { createApp, ref, computed, nextTick } from "/test/public/assets/js/vue.esm-browser.js";

const App = {
  setup() {
    // Reactive state
    const events = ref(Array.isArray(window.initialEvents) ? window.initialEvents : []);
    const venues = ref([]);
    const event = ref({
      id: null,
      name: '',
      description: '',
      start_time: '',
      end_time: '',
      venue_id: null,
      organizer_name: '',
      venue_name: ''
    });
    const searchQuery = ref('');
    const sortKey = ref('name');
    const sortOrder = ref('asc');
    const isLoading = ref(false);
    const showModal = ref(false);
    const modalTitle = ref('');
    const modalMode = ref(''); // 'view', 'create', or 'edit'
    const csrfToken = ref(window.csrfToken || '');

    // Computed property for filtered and sorted events
    const filteredEvents = computed(() => {
      const q = searchQuery.value.toLowerCase();
      const arr = events.value.filter(e =>
        (e.name || '').toLowerCase().includes(q) ||
        (e.venue_name || '').toLowerCase().includes(q) ||
        (e.organizer_name || '').toLowerCase().includes(q)
      );
      return arr.sort((a, b) => {
        const A = (a[sortKey.value] ?? '').toString();
        const B = (b[sortKey.value] ?? '').toString();
        if (A < B) return sortOrder.value === 'asc' ? -1 : 1;
        if (A > B) return sortOrder.value === 'asc' ? 1 : -1;
        return 0;
      });
    });

    // Format date for display
    const formatDate = (dateStr) => {
      if (!dateStr) return 'N/A';
      return new Date(dateStr).toLocaleString('en-US', {
        month: 'short',
        day: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    };

    // Toggle sort key and order
    const toggleSort = (key) => {
      if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
      } else {
        sortKey.value = key;
        sortOrder.value = 'asc';
      }
    };

    // Fetch all events
    const fetchEvents = async () => {
      isLoading.value = true;
      try {
        console.log('Fetching events from:', `${window.BASE_URL}/api/events`);
        const res = await fetch(`${window.BASE_URL}/api/events`, { credentials: 'same-origin' });
        console.log('Fetch events status:', res.status);
        const json = await res.json();
        console.log('Fetch events response:', json);
        if (json.status === 'success') {
          events.value = json.data || [];
        } else {
          console.error('Fetch events failed:', json.message);
          alert('Failed to fetch events: ' + (json.message || 'Unknown error'));
        }
      } catch (e) {
        console.error('Fetch events error:', e);
        alert('Network error while fetching events');
      } finally {
        isLoading.value = false;
      }
    };

    // Fetch venues for create/edit form
    const fetchVenues = async () => {
      try {
        console.log('Fetching venues from:', `${window.BASE_URL}/api/venues`);
        const res = await fetch(`${window.BASE_URL}/api/venues`, { credentials: 'same-origin' });
        console.log('Fetch venues status:', res.status);
        const json = await res.json();
        console.log('Fetch venues response:', json);
        if (json.status === 'success') {
          venues.value = json.data || [];
        } else {
          console.error('Fetch venues failed:', json.message);
          alert('Failed to fetch venues: ' + (json.message || 'Unknown error'));
        }
      } catch (e) {
        console.error('Fetch venues error:', e);
        alert('Network error while fetching venues');
      }
    };

    // Open event details modal
    const openView = async (id) => {
      console.log('Opening view for event ID:', id);
      modalTitle.value = 'Event Details';
      modalMode.value = 'view';
      try {
        const res = await fetch(`${window.BASE_URL}/api/events/${id}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin'
        });
        console.log('Fetch event status:', res.status);
        const json = await res.json();
        console.log('Fetch event response:', json);
        if (res.ok && json.status === 'success') {
          event.value = {
            id: json.data.id,
            name: json.data.name || '',
            description: json.data.description || 'No description available',
            start_time: json.data.start_time || '',
            end_time: json.data.end_time || '',
            venue_id: json.data.venue_id || null,
            organizer_name: json.data.organizer_name || 'N/A',
            venue_name: json.data.venue_name || 'N/A'
          };
          showModal.value = true;
          console.log('Modal opened with event:', event.value);
        } else {
          console.error('Fetch event failed:', json.message);
          alert(json.message || 'Failed to load event details');
        }
      } catch (e) {
        console.error('Fetch event error:', e);
        alert('Network error while loading event details');
      }
    };

    // Open create event modal
    const openCreate = async () => {
      console.log('Opening create modal');
      modalTitle.value = 'Create Event';
      modalMode.value = 'create';
      event.value = {
        id: null,
        name: '',
        description: '',
        start_time: '',
        end_time: '',
        venue_id: null,
        organizer_name: '',
        venue_name: ''
      };
      await fetchVenues();
      showModal.value = true;
      console.log('Create modal opened, venues:', venues.value);
    };

    // Open edit event modal
    const openEdit = async (id) => {
      console.log('Opening edit modal for event ID:', id);
      modalTitle.value = 'Edit Event';
      modalMode.value = 'edit';
      try {
        const res = await fetch(`${window.BASE_URL}/api/events/${id}`, {
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin'
        });
        console.log('Fetch event status:', res.status);
        const json = await res.json();
        console.log('Fetch event response:', json);
        if (res.ok && json.status === 'success') {
          event.value = {
            id: json.data.id,
            name: json.data.name || '',
            description: json.data.description || '',
            start_time: json.data.start_time || '',
            end_time: json.data.end_time || '',
            venue_id: json.data.venue_id || null,
            organizer_name: json.data.organizer_name || 'N/A',
            venue_name: json.data.venue_name || 'N/A'
          };
          await fetchVenues();
          showModal.value = true;
          console.log('Edit modal opened with event:', event.value, 'venues:', venues.value);
        } else {
          console.error('Fetch event failed:', json.message);
          alert(json.message || 'Failed to load event details');
        }
      } catch (e) {
        console.error('Fetch event error:', e);
        alert('Network error while loading event details');
      }
    };

    // Submit create/edit form
    const submitForm = async () => {
      console.log('Submitting form, mode:', modalMode.value, 'event:', event.value);
      const payload = {
        name: event.value.name,
        description: event.value.description,
        start_time: event.value.start_time,
        end_time: event.value.end_time,
        venue_id: event.value.venue_id || null
      };
      const isEdit = modalMode.value === 'edit';
      const url = isEdit
        ? `${window.BASE_URL}/api/events/update/${event.value.id}`
        : `${window.BASE_URL}/api/events/create`;
      const method = isEdit ? 'PUT' : 'POST';
      try {
        const res = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.value
          },
          body: JSON.stringify(payload),
          credentials: 'same-origin'
        });
        console.log('Submit form status:', res.status);
        const json = await res.json();
        console.log('Submit form response:', json);
        if (json.status === 'success') {
          closeModal();
          await fetchEvents();
          alert(isEdit ? 'Event updated successfully' : 'Event created successfully');
        } else {
          console.error('Submit form failed:', json.message);
          alert(json.message || 'Operation failed');
        }
      } catch (e) {
        console.error('Submit form error:', e);
        alert('Network error');
      }
    };

    // Delete event
    const deleteEvent = async (id) => {
      if (!confirm('Delete this event?')) return;
      try {
        console.log('Deleting event ID:', id);
        const res = await fetch(`${window.BASE_URL}/events/delete/${id}`, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': csrfToken.value },
          credentials: 'same-origin'
        });
        console.log('Delete event status:', res.status);
        const json = await res.json();
        console.log('Delete event response:', json);
        if (json.status === 'success') {
          events.value = events.value.filter(e => +e.id !== +id);
          alert('Event deleted successfully');
        } else {
          console.error('Delete event failed:', json.message);
          alert(json.message || 'Failed to delete event');
        }
      } catch (e) {
        console.error('Delete event error:', e);
        alert('Network error');
      }
    };

    // Register event
    const registerEvent = async (id) => {
        if (!confirm('Register to this event?')) return;
        if (!csrfToken.value) {
            alert('CSRF token missing. Please refresh the page or log in.');
            return;
        }

        try {
            console.log('Registering to event ID:', id);
            const res = await fetch(`${window.BASE_URL}/api/events/register/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken.value,
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin',
        });
            console.log('Register event status:', res.status);
            if (!res.ok) {
                if (res.status === 403) {
                    alert('Session expired or invalid. Please log in.');
                    window.location.href = `${window.BASE_URL}/login`;
                    return;
                }
                if (res.status === 404) {
                    alert('Event not found.');
                    return;
                }
                throw new Error(`HTTP error: ${res.status}`);
            }
            const json = await res.json();
            console.log('Register event response:', json);

            if (json.status === 'success') {
                events.value = events.value.map(e =>
                    +e.id === +id ? { ...e, registered: true } : e
                );
                alert('Event registered successfully'); 
            } else {
                console.error('Register event failed:', json.message);
                alert(json.message || 'Failed to register to event');
            }
        } catch (e) {
            console.error('Register event error:', e);
            alert('Network error or server issue. Please try again.');
        }
    };

    // Close modal
    const closeModal = () => {
      console.log('Closing modal');
      showModal.value = false;
      modalTitle.value = '';
      modalMode.value = '';
      event.value = {
        id: null,
        name: '',
        description: '',
        start_time: '',
        end_time: '',
        venue_id: null,
        organizer_name: '',
        venue_name: ''
      };
    };

    // Fetch events on component mount
    fetchEvents();

    // Return reactive state, computed properties, and methods
    return {
      events,
      venues,
      event,
      searchQuery,
      sortKey,
      sortOrder,
      isLoading,
      showModal,
      modalTitle,
      modalMode,
      csrfToken,
      filteredEvents,
      formatDate,
      toggleSort,
      fetchEvents,
      fetchVenues,
      openView,
      openCreate,
      openEdit,
      submitForm,
      deleteEvent,
      registerEvent,
      closeModal
    };
  }
};

// Mount the app
createApp(App).mount('#event-list-app');