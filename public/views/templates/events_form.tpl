{extends file='base.tpl'}

{block name="title"}{if $event}Edit Event{else}Create Event{/block}

{block name="content"}
<div id="event-form-app" v-cloak>
    <h1>{if $event}Edit Event{else}Create Event{/if}</h1>
    <form method="post" action="{$base_url}/events/{if $event}update/{$event.id|escape}{else}create{/if}">
        <input type="hidden" name="csrf_token" value="{$csrf_token|escape}">
        <label>Name: <input type="text" name="name" value="{if $event}{$event.name|escape}{/if}" required></label><br>
        <label>Description: <textarea name="description">{if $event}{$event.description|escape}{/if}</textarea></label><br>
        <label>Start Time: <input type="datetime-local" name="start_time" value="{if $event}{$event.start_time|date_format:"%Y-%m-%dT%H:%M"}{/if}" required></label><br>
        <label>End Time: <input type="datetime-local" name="end_time" value="{if $event}{$event.end_time|date_format:"%Y-%m-%dT%H:%M"}{/if}" required></label><br>
        <label>Venue:
            <select name="venue_id">
                <option value="">Select Venue</option>
                {foreach $venues as $venue}
                    <option value="{$venue.id|escape}" {if $event && $event.venue_id == $venue.id}selected{/if}>{$venue.name|escape}</option>
                {/foreach}
            </select>
        </label><br>
        <button type="submit">{if $event}Update Event{else}Create Event{/if}</button>
    </form>
</div>
{/block}