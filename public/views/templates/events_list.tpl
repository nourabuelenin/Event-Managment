<html>
<head>
    <title>Events</title>
    
    <style>
        .sortable:hover { cursor: pointer; background-color: #f0f0f0; }
        .sort-asc::after { content: ' ↑'; }
        .sort-desc::after { content: ' ↓'; }
        .loading { opacity: 0.5; pointer-events: none; }
    </style>
</head>
<body>
<div id="event-list-app" v-cloak :class="{ 'loading': isLoading }">
    <h1>Event List</h1>
    <div>
        <input v-model="searchQuery" placeholder="Search events..." style="margin: 10px; padding: 5px;">
        <a href="events_form.php">Create New Event</a>
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
                <a :href="'events_form.php?id=' + event.id">Edit</a>
                <a href="#" @click.prevent="deleteEvent(event.id)">Delete</a>
            </td>
        </tr>
    </table>
</div>

<script>
    window.initialEvents = {$events|json_encode nofilter} || [];
</script>
<script type="module" src="https://unpkg.com/vue@3/dist/vue.esm-browser.js"></script>
<script type="module" src="/test/public/assets/js/eventListApp.js"></script>
</body>
</html>