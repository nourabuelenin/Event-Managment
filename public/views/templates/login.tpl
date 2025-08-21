{extends file='base.tpl'}

{block name="title"}Login{/block}

{block name="content"}
<div id="login-app" v-cloak>
    <h1>Login</h1>
    <form method="post" action="{$base_url}/login">
        <input type="hidden" name="csrf_token" :value="csrf_token">
        <label>Username: <input type="text" v-model="formData.username" name="username"></label>
        <span v-if="errors.username" class="error">{{ errors.username }}</span><br>
        <label>Password: <input type="password" v-model="formData.password" name="password"></label>
        <span v-if="errors.password" class="error">{{ errors.password }}</span><br>
        <p>Don't have an account? <a href="{$base_url}/register">Register</a></p>
        <button type="submit">Login</button>
    </form>
</div>
{/block}

{block name="scripts"}
<script>
    window.flash = {$flash|@json_encode|raw};
    window.csrf_token = '{$csrf_token|escape:"javascript"}';
</script>
<script type="module" src="{$views_url}/js/loginApp.js"></script>
{/block}