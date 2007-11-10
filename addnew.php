<?
// addnew.php
// adds new items to the database

require_once('userconf.php');
require_once('functions.php');

if (isset($_POST['reset'])) { $date = date(get_det_var("datefmt")); }
else { 
	if ($_POST['date'] != "") { $date = htmlspecialchars($_POST['date']); }
	else { $date = date(get_det_var("datefmt")); } 
}

if (isset($_POST['moderated'])) { $moderated = 0; }	else { $moderated = 1; }
if (isset($_POST['sticky'])) { $sticky = 0; } else { $sticky = 1; }
$commentable = $_POST['commentable'];

if (isset($_POST['ratable'])) { $ratable = 0; } else { $ratable = 1; }
if ($_POST['rating'] != "") { $rating = $_POST['rating']; } else { $rating = 0; }

if (isset($_POST['commentref']) && $_POST['commentref'] != 0) { $commentref = $_POST['commentref']; } else { $commentref=0; }

if ($_POST['cat'] == "other") {
	if (isset($_POST['section']) && $_POST['section'] != "") {
		$cat = $_POST['section']; $cm = TRUE;
	} else { $cat = $get_det_var("default"); }
} else { $cat = $_POST['cat']; }

$title = strip_tags(htmlentities($_POST['title']));
$intro = strip_tags(htmlentities($_POST['intro']),"<a><br>");

if (isset($_POST['owner']) && $_POST['owner'] != "") {
	$owner = $_POST['owner'];
} else { $owner = $title; }
$main = htmlentities($_POST['main']);
$transaction_key = $_POST['transaction_key'];
$_REQUEST = array(NULL);

if ($cat != "comments" && $cat != "chatbox") {
	$site_usr_lvls = get_det_array("usertype");
	if ($_SESSION['auth'] < $site_usr_lvls['writer']) {
		die("Not enough privilage to post");
	}
}

if (check_transaction_key($transaction_key)) {
	mysql_query('INSERT INTO '.$db_prefix.'data
		(title,section, date, intro, main, moderated, commentable, commentref,owner,sticky,ratable) 
		VALUES ("' . $title . '", 
			"'. $cat .'",
			"'.$date.'", 
			"' . $intro . '",
			"'. $main .'",
			"' . $moderated . '",
			"' . $commentable . '",
			"' . $commentref . '",
			"'.$owner.'",
			"'.$sticky.'",
			"'.$ratable.'"
			)') or die('Sorry, there was a problem and your post could not be completed. ' .mysql_error() );
} else { die("Double post detected!"); };

if ($commentref == 0) {
	if ($cm == TRUE) { $cm = explode(",",$cat,2); header('Location:'.$hurl.'/view/'.$cm[0]); }
	else { 
	if ($cat == "chatbox") {
		header('Location:'.$hurl.'/chatbox');
	} else { header('Location:'.$hurl.'/view/'.$cat); }
	}
} else { header('Location:'.$hurl.'/show/'.$commentref); }


?>