<?
// rating.php
// change a rating

require_once('userconf.php');
require_once('functions.php');

$cat = $_GET['cat'];
$id = $_GET['id'];
$key = $_GET['key'];
$_REQUEST = array(NULL);

if (check_transaction_key($key)) {
$result = mysql_result(mysql_query('SELECT rating FROM '.$db_prefix.'data WHERE id = "'. $id . '"'),0);
	switch($cat) {
		case "raise":
			$rating = $result + 1;
			break;
		case "lower":
			$rating = $result - 1;
			break;
	}
	mysql_query('UPDATE '.$db_prefix.'data SET rating = "'. $rating .'" WHERE id = "'. $id . '"');
}
	
$r = mysql_query('SELECT section,commentref,id FROM '.$db_prefix.'data WHERE id = "'. $id . '"');
$result = mysql_fetch_array($r,MYSQL_BOTH);
if ($result[0] == "comments") {
	header('Location:'.$hurl.'/show/'.$result[1]);
} else { header('Location:'.$hurl.'/view/'.$result[2]); }