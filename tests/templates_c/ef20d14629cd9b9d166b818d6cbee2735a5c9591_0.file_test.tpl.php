<?php
/* Smarty version 5.5.1, created on 2025-08-11 05:55:28
  from 'file:test.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.5.1',
  'unifunc' => 'content_689985d09c22d3_47121949',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ef20d14629cd9b9d166b818d6cbee2735a5c9591' => 
    array (
      0 => 'test.tpl',
      1 => 1754830455,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_689985d09c22d3_47121949 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = 'D:\\wamp64\\www\\test\\templates';
?><html>
<head>
    <title>Smarty Test</title>
</head>
<body>
    <h1>Smarty Test Page</h1>

    <p><?php echo $_smarty_tpl->getValue('connection_status');?>
</p>

    <h3>Database Results:</h3>
    <ul>
        <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('rows'), 'row');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('row')->value) {
$foreach0DoElse = false;
?>
            <li>ID: <?php echo $_smarty_tpl->getValue('row')['id'];?>
 - Message: <?php echo $_smarty_tpl->getValue('row')['message'];?>
</li>
        <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
    </ul>
</body>
</html>
<?php }
}
