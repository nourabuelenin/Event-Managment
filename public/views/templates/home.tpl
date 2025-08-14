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
    {if $events}
        <ul>
            {foreach $events as $event}
                <li>
                    <a href="{$base_url}/events/{$event.id|escape}">{$event.name|escape}</a>
                    - {$event.start_time|date_format:"%b %d, %Y %H:%M"}
                    {if $event.venue_name} at {$event.venue_name|escape}{/if}
                </li>
            {/foreach}
        </ul>
    {else}
        <p>No upcoming events.</p>
    {/if}
</div>
{/block}