{extends file='base.tpl'}

{block name="title"}Events{/block}

{block name="content"}
<div id="event-list-app" v-cloak :class="{ 'loading': isLoading }">
    <h1>Event List</h1>
    <div>
        <input v-model="searchQuery" placeholder="Search events..." style="margin: 10px; padding: 5px;">
        {if $current_user.role == 'organizer' || $current_user.role == 'admin'}
            <a href="{$base_url}/events/create">Create New Event</a>
        {/if}
    </div>
    <table border="1">
        <tr>
            <th class="sortable" @click="toggleSort('name')" :class="{ 'sort-asc': sortKey === 'name' && sortOrder === 'asc', 'sort-desc': sortKey === 'name' && sortOrder === 'desc' }">Name</th>
            <th class="sortable" @click="toggleSort('venue_name')">Venue</th>
            <th class="sortable" @click="toggleSort('organizer_name')">Organizer</th>
            <th class="sortable" @click="toggleSort('start_time')">Start</th>
            <th class="sortable" @click="toggleSort('end_time')">End</th>
            <th>Actions</th>
        </tr>
        <tr v-for="event in filteredEvents" :key="event.id">
            <td>{{ event.name }}</td>
            <td>{{ event.venue_name || 'N/A' }}</td>
            <td>{{ event.organizer_name }}</td>
            <td>{{ event.start_time }}</td>
            <td>{{ event.end_time }}</td>
            <td>
                <a :href="'{$base_url}/events/update/' + event.id">Edit</a>
                <a href="#" @click.prevent="deleteEvent(event.id)">Delete</a>
            </td>
        </tr>
    </table>
</div>
{/block}

{block name="scripts"}
<script>
    window.initialEvents = {$events|json_encode nofilter} || [];
</script>
<script type="module" src="{$views_url}/js/eventListApp.js"></script>
{/block}