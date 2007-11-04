<?
//logout.php
//removes a user's privileges
require_once('functions.php');
$_SESSION = array();
session_destroy();
header('Location:'.$hurl); 
exit;
?>