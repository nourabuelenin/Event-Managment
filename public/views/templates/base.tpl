{* Base template for all pages *}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{block name="title"}Event Management{/block}</title>
    <link rel="stylesheet" href="{$assets_url}/css/style.css">
    <style>
        .error { color: red; font-size: 0.8em; }
        .flash-success { color: green; }
        .flash-error { color: red; }
        nav { background: #333; padding: 1em; }
        nav a { color: white; margin-right: 1em; text-decoration: none; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
    </style>
</head>
<body>
    <nav>
        <a href="{$base_url}/home">Home</a>
        {if $current_user}
            <a href="{$base_url}/events">Events</a>
            {if $current_user.role == 'organizer' || $current_user.role == 'admin'}
                <a href="{$base_url}/events/create">Create Event</a>
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