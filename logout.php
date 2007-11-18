<?
//logout.php
//removes a user's privileges
require_once('functions.php');
mysql_query('UPDATE '.$db_prefix.'users SET session = "" WHERE username = "'.$_SESSION['name'].'" LIMIT 1');
$_SESSION = array();
session_destroy();

header('Location:'.$hurl); 
exit;
?>