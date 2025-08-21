{extends file='base.tpl'}

{block name="title"}Register{/block}

{block name="content"}
<div id="register-app" v-cloak>
    <h1>Register</h1>
    <form method="post" action="{$base_url}/register">
        <input type="hidden" name="csrf_token" :value="csrf_token">
        <label>Username: <input type="text" v-model="formData.username" name="username" placeholder="John Doe"></label>
        <span v-if="errors.username" class="error">{{ errors.username }}</span><br>
        <label>Email: <input type="email" v-model="formData.email" name="email" placeholder="example@mail.com"></label>
        <span v-if="errors.email" class="error">{{ errors.email }}</span><br>
        <label>Password: <input type="password" v-model="formData.password" name="password" placeholder="Password must be at least 6 characters"></label>
        <span v-if="errors.password" class="error">{{ errors.password }}</span><br>
        <label>Confirm Password: <input type="password" v-model="formData.confirm_password" name="confirm_password" placeholder="Re-enter password"></label>
        <span v-if="errors.confirm_password" class="error">{{ errors.confirm_password }}</span><br>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="{$base_url}/login">Log in</a></p>
</div>
{/block}

{block name="scripts"}
<script>
    window.flash = {$flash|@json_encode|raw};
    window.csrf_token = '{$csrf_token|escape:"javascript"}';
</script>
<script type="module" src="{$views_url}/js/registerApp.js"></script>
{/block}