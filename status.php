<?
//status.php
//confirm login
require_once('userconf.php');
require_once('functions.php');

$username = $_POST['username'];
$password = $_POST['password'];
$transaction_key = $_POST['transaction_key'];
$_REQUEST = array(NULL);

if (check_transaction_key($transaction_key)) {
	switch(login($username,$password)) {
	default:	// login successful
				header('Location:'.$hurl.'/view/news');
				exit;
				break;
	case -2:	$dmesg = 'Password does not match user: ' . $username;
				header('Location:'.$hurl.'/login?dmesg='.$dmesg.'&username='.$username);
				exit;
				break;
	case -3:	mysql_query('INSERT INTO '.$db_prefix.'users (username, password, type) 
				VALUES ("'.$username.'","'.crypt($password).'",0)');
				$dmesg = $username . ' has been created';
				header('Location:'.$hurl.'/login?dmesg='.$dmesg.'&username='.$username);
				exit;
				break;
	}
}
header('Location:'.$hurl.'/login');
?>