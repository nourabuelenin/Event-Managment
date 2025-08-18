{extends file='base.tpl'}

{block name="title"}Events{/block}

{block name="content"}
<div id="event-list-app" v-cloak :class="{ 'loading': isLoading }">
    <h1>Event List</h1>

    <div style="margin: 10px 0;">
        <input v-model="searchQuery" placeholder="Search events..." style="padding: 6px; width: 250px;">
        {if $current_user && ($current_user.role == 'organizer' || $current_user.role == 'admin')}
            <button @click="openCreate" style="margin-left: 10px;">Create New Event</button>
        {/if}
    </div>

    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th class="sortable" @click="toggleSort('name')">Name</th>
                <th class="sortable" @click="toggleSort('venue_name')">Venue</th>
                <th class="sortable" @click="toggleSort('organizer_name')">Organizer</th>
                <th class="sortable" @click="toggleSort('start_time')">Start</th>
                <th class="sortable" @click="toggleSort('end_time')">End</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="event in filteredEvents" :key="event.id">
                <td>{{ event.name }}</td>
                <td>{{ event.venue_name || 'N/A' }}</td>
                <td>{{ event.organizer_name }}</td>
                <td>{{ event.start_time }}</td>
                <td>{{ event.end_time }}</td>
                <td>
                    {if $current_user && ($current_user.role == 'organizer' || $current_user.role == 'admin')}
                        <a href="#" @click.prevent="openEdit(event.id)">Edit</a>
                        &nbsp;|&nbsp;
                        <a href="#" @click.prevent="deleteEvent(event.id)">Delete</a>
                        &nbsp;|&nbsp;
                    {/if}
                        <a href="#" @click.prevent="openView(event.id)">View</a>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Modal -->
    <div v-if="showModal" class="modal-backdrop">
        <div class="modal-card">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0;">{{ modalTitle }}</h3>
                <button @click="closeModal">X</button>
            </div>
            <div style="margin-top: 10px;">
                <!-- View Mode -->
                <div v-if="modalMode === 'view'">
                    <h3>{{ event.name }}</h3>
                    <p><strong>Description:</strong> {{ event.description || 'No description available' }}</p>
                    <p><strong>Start:</strong> {{ event.start_time ? formatDate(event.start_time) : 'N/A' }}</p>
                    <p><strong>End:</strong> {{ event.end_time ? formatDate(event.end_time) : 'N/A' }}</p>
                    <p><strong>Organizer:</strong> {{ event.organizer_name || 'N/A' }}</p>
                    <p><strong>Venue:</strong> {{ event.venue_name || 'N/A' }}</p>
                </div>
                <!-- Create/Edit Mode -->
                <div v-if="modalMode === 'create' || modalMode === 'edit'">
                    <form id="event-form" @submit.prevent="submitForm">
                        <input type="hidden" name="csrf_token" :value="csrfToken">
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <label>Name</label><br>
                                <input type="text" v-model="event.name" required>
                            </div>
                            <div>
                                <label>Venue</label><br>
                                <select v-model="event.venue_id">
                                    <option value="">-- None --</option>
                                    <option v-for="venue in venues" :value="venue.id" :key="venue.id">{{ venue.name }}</option>
                                </select>
                            </div>
                            <div style="grid-column: span 2;">
                                <label>Description</label><br>
                                <textarea v-model="event.description" rows="4"></textarea>
                            </div>
                            <div>
                                <label>Start Time</label><br>
                                <input type="datetime-local" v-model="event.start_time">
                            </div>
                            <div>
                                <label>End Time</label><br>
                                <input type="datetime-local" v-model="event.end_time">
                            </div>
                        </div>
                        <div style="margin-top: 12px; display:flex; gap: 8px;">
                            <button type="submit">{{ modalMode === 'edit' ? 'Update Event' : 'Create Event' }}</button>
                            <button type="button" @click="closeModal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}

{block name="scripts"}
<script>
    window.BASE_URL = '{$base_url|escape:"javascript"}';
    window.csrfToken = '{$csrf_token|escape:"javascript"}';
    window.initialEvents = {$events|json_encode nofilter};
</script>
<script type="module" src="{$views_url}/js/eventListApp.js"></script>
{/block}