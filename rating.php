<?
// rating.php
// change a rating

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
	
$r = mysql_query('SELECT section,commentref FROM '.$db_prefix.'data WHERE id = "'. $id . '"');
list($section,$commentref) = mysql_fetch_array($r,MYSQL_BOTH);
if ($section == "comments") {
	header('Location:'.$hurl.'/show/'.$commentref);
} else { header('Location:'.$hurl.'/show/'.$id); }
