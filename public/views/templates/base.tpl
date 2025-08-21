{* views/base.tpl *}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{block name="title"}Event Management{/block}</title>
    <link  rel="stylesheet" type="text/css" href="{$assets_url}/css/style.css">
</head>
<body>
    <nav>
        <a href="{$base_url}/home">Home</a>
        {if $current_user}
            <a href="{$base_url}/events">Events</a>
            {* {if $current_user.role == 'organizer' || $current_user.role == 'admin'}
                <a href="{$base_url}/events/create">Create Event</a>
            {/if} *}
            {if $current_user.role == 'organizer' || $current_user.role == 'admin'}
                <a href="{$base_url}/events/venues">Venues</a>
            {/if}
            <a href="{$base_url}/logout">Logout ({$current_user.username|escape})</a>
        {else}
            <a href="{$base_url}/login">Login</a>
            <a href="{$base_url}/register">Register</a>
        {/if}
    </nav>
    <div class="container">
        {if $flash}
            <div class="flash-{$flash.type|escape}">{$flash.message|escape}</div>
        {/if}
        {block name="content"}{/block}
    </div>
    <script src="{$assets_url}/js/vue.global.js"></script>
    {block name="scripts"}{/block}
</body>
</html>