{extends file='base.tpl'}

{block name="title"}Event: {$event.name|escape}{/block}

{block name="content"}
<div id="event-view-app" v-cloak>
    <h1>{$event.name|escape}</h1>
    <p><strong>Description:</strong> {$event.description|escape|nl2br}</p>
    <p><strong>Start:</strong> {$event.start_time|date_format:"%b %d, %Y %H:%M"}</p>
    <p><strong>End:</strong> {$event.end_time|date_format:"%b %d, %Y %H:%M"}</p>
    <p><strong>Organizer:</strong> {$event.organizer_name|escape}</p>
    {if $event.venue_name}
        <p><strong>Venue:</strong> {$event.venue_name|escape}</p>
    {/if}
    <a href="{$base_url}/events">Back to Events</a>
</div>
{/block}