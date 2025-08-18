import { createApp, nextTick } from "/test/public/assets/js/vue.esm-browser.js";

const app = createApp({
    data() {
        return {
            events: Array.isArray(window.initialEvents) ? window.initialEvents : [],
            venues: [],
            event: {
                id: null,
                name: '',
                description: '',
                start_time: '',
                end_time: '',
                venue_id: null,
                organizer_name: '',
                venue_name: ''
            },
            searchQuery: '',
            sortKey: 'name',
            sortOrder: 'asc',
            isLoading: false,
            showModal: false,
            modalTitle: '',
            modalMode: '', // 'view', 'create', or 'edit'
            csrfToken: window.csrfToken || ''
        };
    },
    computed: {
        filteredEvents() {
            const q = this.searchQuery.toLowerCase();
            const arr = this.events.filter(e =>
                (e.name || '').toLowerCase().includes(q) ||
                (e.venue_name || '').toLowerCase().includes(q) ||
                (e.organizer_name || '').toLowerCase().includes(q)
            );
            return arr.sort((a, b) => {
                const A = (a[this.sortKey] ?? '').toString();
                const B = (b[this.sortKey] ?? '').toString();
                if (A < B) return this.sortOrder === 'asc' ? -1 : 1;
                if (A > B) return this.sortOrder === 'asc' ? 1 : -1;
                return 0;
            });
        }
    },
    methods: {
        formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            return new Date(dateStr).toLocaleString('en-US', {
                month: 'short',
                day: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        toggleSort(key) {
            if (this.sortKey === key) {
                this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortKey = key;
                this.sortOrder = 'asc';
            }
        },
        async fetchEvents() {
            this.isLoading = true;
            try {
                console.log('Fetching events from:', `${window.BASE_URL}/api/events`);
                const res = await fetch(`${window.BASE_URL}/api/events`, { credentials: 'same-origin' });
                console.log('Fetch events status:', res.status);
                const json = await res.json();
                console.log('Fetch events response:', json);
                if (json.status === 'success') {
                    this.events = json.data || [];
                } else {
                    console.error('Fetch events failed:', json.message);
                    alert('Failed to fetch events: ' + (json.message || 'Unknown error'));
                }
            } catch (e) {
                console.error('Fetch events error:', e);
                alert('Network error while fetching events');
            } finally {
                this.isLoading = false;
            }
        },
        async fetchVenues() {
            try {
                console.log('Fetching venues from:', `${window.BASE_URL}/api/venues`);
                const res = await fetch(`${window.BASE_URL}/api/venues`, { credentials: 'same-origin' });
                console.log('Fetch venues status:', res.status);
                const json = await res.json();
                console.log('Fetch venues response:', json);
                if (json.status === 'success') {
                    this.venues = json.data || [];
                } else {
                    console.error('Fetch venues failed:', json.message);
                    alert('Failed to fetch venues: ' + (json.message || 'Unknown error'));
                }
            } catch (e) {
                console.error('Fetch venues error:', e);
                alert('Network error while fetching venues');
            }
        },
        async openView(id) {
            console.log('Opening view for event ID:', id);
            this.modalTitle = 'Event Details';
            this.modalMode = 'view';
            try {
                const res = await fetch(`${window.BASE_URL}/api/events/${id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                console.log('Fetch event status:', res.status);
                const json = await res.json();
                console.log('Fetch event response:', json);
                if (res.ok && json.status === 'success') {
                    this.event = {
                        id: json.data.id,
                        name: json.data.name || '',
                        description: json.data.description || 'No description available',
                        start_time: json.data.start_time || '',
                        end_time: json.data.end_time || '',
                        venue_id: json.data.venue_id || null,
                        organizer_name: json.data.organizer_name || 'N/A',
                        venue_name: json.data.venue_name || 'N/A'
                    };
                    this.showModal = true;
                    console.log('Modal opened with event:', this.event);
                } else {
                    console.error('Fetch event failed:', json.message);
                    alert(json.message || 'Failed to load event details');
                }
            } catch (e) {
                console.error('Fetch event error:', e);
                alert('Network error while loading event details');
            }
        },
        async openCreate() {
            console.log('Opening create modal');
            this.modalTitle = 'Create Event';
            this.modalMode = 'create';
            this.event = {
                id: null,
                name: '',
                description: '',
                start_time: '',
                end_time: '',
                venue_id: null,
                organizer_name: '',
                venue_name: ''
            };
            await this.fetchVenues();
            this.showModal = true;
            console.log('Create modal opened, venues:', this.venues);
        },
        async openEdit(id) {
            console.log('Opening edit modal for event ID:', id);
            this.modalTitle = 'Edit Event';
            this.modalMode = 'edit';
            try {
                const res = await fetch(`${window.BASE_URL}/api/events/${id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                console.log('Fetch event status:', res.status);
                const json = await res.json();
                console.log('Fetch event response:', json);
                if (res.ok && json.status === 'success') {
                    this.event = {
                        id: json.data.id,
                        name: json.data.name || '',
                        description: json.data.description || '',
                        start_time: json.data.start_time || '',
                        end_time: json.data.end_time || '',
                        venue_id: json.data.venue_id || null,
                        organizer_name: json.data.organizer_name || 'N/A',
                        venue_name: json.data.venue_name || 'N/A'
                    };
                    await this.fetchVenues();
                    this.showModal = true;
                    console.log('Edit modal opened with event:', this.event, 'venues:', this.venues);
                } else {
                    console.error('Fetch event failed:', json.message);
                    alert(json.message || 'Failed to load event details');
                }
            } catch (e) {
                console.error('Fetch event error:', e);
                alert('Network error while loading event details');
            }
        },
        async submitForm() {
            console.log('Submitting form, mode:', this.modalMode, 'event:', this.event);
            const payload = {
                name: this.event.name,
                description: this.event.description,
                start_time: this.event.start_time,
                end_time: this.event.end_time,
                venue_id: this.event.venue_id || null
            };
            const isEdit = this.modalMode === 'edit';
            const url = isEdit
                ? `${window.BASE_URL}/api/events/update/${this.event.id}`
                : `${window.BASE_URL}/api/events/create`;
            const method = isEdit ? 'PUT' : 'POST';
            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify(payload),
                    credentials: 'same-origin'
                });
                console.log('Submit form status:', res.status);
                const json = await res.json();
                console.log('Submit form response:', json);
                if (json.status === 'success') {
                    this.closeModal();
                    await this.fetchEvents();
                    alert(isEdit ? 'Event updated successfully' : 'Event created successfully');
                } else {
                    console.error('Submit form failed:', json.message);
                    alert(json.message || 'Operation failed');
                }
            } catch (e) {
                console.error('Submit form error:', e);
                alert('Network error');
            }
        },
        async deleteEvent(id) {
            if (!confirm('Delete this event?')) return;
            try {
                console.log('Deleting event ID:', id);
                const res = await fetch(`${window.BASE_URL}/events/delete/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken },
                    credentials: 'same-origin'
                });
                console.log('Delete event status:', res.status);
                const json = await res.json();
                console.log('Delete event response:', json);
                if (json.status === 'success') {
                    this.events = this.events.filter(e => +e.id !== +id);
                    alert('Event deleted successfully');
                } else {
                    console.error('Delete event failed:', json.message);
                    alert(json.message || 'Failed to delete event');
                }
            } catch (e) {
                console.error('Delete event error:', e);
                alert('Network error');
            }
        },
        closeModal() {
            console.log('Closing modal');
            this.showModal = false;
            this.modalTitle = '';
            this.modalMode = '';
            this.event = {
                id: null,
                name: '',
                description: '',
                start_time: '',
                end_time: '',
                venue_id: null,
                organizer_name: '',
                venue_name: ''
            };
        }
    },
    async mounted() {
        console.log('Mounting event list app');
        await this.fetchEvents();
    }
});

app.mount('#event-list-app');