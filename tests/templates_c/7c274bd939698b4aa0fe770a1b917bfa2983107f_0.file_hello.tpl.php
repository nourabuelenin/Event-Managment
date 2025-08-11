<?php
/* Smarty version 5.5.1, created on 2025-08-10 10:29:38
  from 'file:hello.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.5.1',
  'unifunc' => 'content_689874924f3176_51837879',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7c274bd939698b4aa0fe770a1b917bfa2983107f' => 
    array (
      0 => 'hello.tpl',
      1 => 1754819862,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
))) {
function content_689874924f3176_51837879 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = 'C:\\Users\\naboelenin\\Desktop\\test\\templates';
?><!DOCTYPE html>
<html>
<head><title>Hello Smarty</title></head>
<body>
    <h1>Hello, <?php echo $_smarty_tpl->getValue('name');?>
!</h1>
</body>
</html>
<?php }
}
