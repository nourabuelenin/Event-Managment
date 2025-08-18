{extends file='base.tpl'}

{block name="title"}Home{/block}

{block name="content"}
<div id="home-app" v-cloak>
    <h1>Welcome to Event Management</h1>
    {if $current_user}
        <p>Hello, {$current_user.username|escape}! Explore or manage events below.</p>
    {else}
        <p>Welcome! Please <a href="{$base_url}/login">log in</a> or <a href="{$base_url}/register">register</a> to manage events.</p>
    {/if}
    <h2>Upcoming Events</h2>
    {* {debug} Dump all Smarty variables for debugging *}
    <div v-if="isLoading">Loading...</div>
    <div v-else>
        {if $events}
            <ul id="event-grid">
                {foreach $events as $event}
                    <li class="event-item">
                        <a href="#" @click.prevent="openView({$event->id|escape})">{$event->name|escape}</a>
                        - {$event->start_time|date_format:"%b %d, %Y %H:%M"}
                        {if $event->venue_name} at {$event->venue_name|escape}{/if}
                    </li>
                {/foreach}
            </ul>
        {else}
            <p>No upcoming events.</p>
        {/if}
    </div>
    <!-- Modal -->
    <div v-if="showModal" class="modal-backdrop">
        <div class="modal-card">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0;">{{ modalTitle }}</h3>
                <button @click="closeModal">X</button>
            </div>
            {* <div id="event-view-app">
                <h3>{$event->name|escape}</h3>
                <p><strong>Description:</strong> {{ $event->description}}{$event->description|escape|nl2br}{else}No description available{/if}</p>
                <p><strong>Start:</strong> {{ $event->start_time}}{$event->start_time|date_format:"%b %d, %Y %H:%M"}{else}N/A{/if}</p>
                <p><strong>End:</strong> {{ $event->end_time}}{$event->end_time|date_format:"%b %d, %Y %H:%M"}{else}N/A{/if}</p>
                <p><strong>Organizer:</strong> {{$event->organizer_name}|escape|default:'N/A'}</p>
                <p><strong>Venue:</strong> {{ $event->venue_name}}{$event->venue_name|escape}{else}N/A{/if}</p>
            </div> *}
            <div id="event-view-app">
                <h3>{{ event.name }}</h3>
                <p><strong>Description:</strong> {{ event.description}}</p>
                <p><strong>Start:</strong> {{ event.start_time}}</p>
                <p><strong>End:</strong> {{ event.end_time}}</p>
                <p><strong>Organizer:</strong> {{ event.organizer_name }}</p>
                <p><strong>Venue:</strong> {{ event.venue_name}}</p>
            </div>

            {* <div id="modal-body" v-html="modalContent" style="margin-top: 10px;"></div> *}
        </div>
    </div>
</div>
{/block}

{block name="scripts"}
<script>
    window.BASE_URL = '{$base_url|escape:"javascript"}';
    window.currentUser = {$current_user|json_encode nofilter};
    window.initialEvents = {$events|json_encode nofilter};
</script>
<script type="module" src="{$views_url}/js/homeApp.js"></script>
{/block}