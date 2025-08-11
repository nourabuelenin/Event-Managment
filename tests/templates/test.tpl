<html>
<head>
    <title>Smarty Test</title>
</head>
<body>
    <h1>Smarty Test Page</h1>

    <p>{$connection_status}</p>

    <h3>Database Results:</h3>
    <ul>
        {foreach $rows as $row}
            <li>ID: {$row.id} - Message: {$row.message}</li>
        {/foreach}
    </ul>
</body>
</html>
