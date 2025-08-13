<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="{$assets_url}/css/style.css">
    <style>
        .error { color: red; font-size: 0.8em; }
        .flash-success { color: green; }
        .flash-error { color: red; }
    </style>
</head>
<body>
<div id="login-app" v-cloak>
    <h1>Login</h1>
    <div v-if="flash" :class="'flash-' + flash.type">{{ flash.message }}</div>
    <form method="post" action="login.php" @submit="submitForm">
        <input type="hidden" name="csrf_token" :value="csrf_token">
        <label>Username: <input type="text" v-model="formData.username" name="username"></label>
        <span v-if="errors.username" class="error">{{ errors.username }}</span><br>
        
        <label>Password: <input type="password" v-model="formData.password" name="password"></label>
        <span v-if="errors.password" class="error">{{ errors.password }}</span><br>
        
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>

<script>
    window.flash = {$flash|@json_encode|raw};
    window.csrf_token = '{$csrf_token|escape:"javascript"}';
</script>
<script type="module" src="{$assets_url}/js/vue.esm-browser.js"></script>
<script type="module" src="{$assets_url}/js/loginApp.js"></script>
</body>
</html>