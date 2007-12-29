<?
// user.php
// view a series of posts from a single user
require_once('userconf.php');
require_once('functions.php');

$cat = $_REQUEST['cat'];
$id = $_REQUEST['id'];
$_REQUEST = array(NULL);

$link = mysql_connect($db_host, $db_user, $db_pass) or die('Could not connect: ' . mysql_error());
mysql_select_db($db_data) or die('Could not select database: ' . mysql_error() );

if ($id == "") { $id = "10"; }
$query = 'SELECT id,title,date,intro,commentable,main,owner,ratable,rating 
	FROM '.$db_prefix.'data 
	WHERE owner = "' . $cat . '" 
		AND moderated != 1 
		AND date <= "'.date(get_det_var("datefmt")).'" 
		AND rating >= -50
	ORDER BY sticky ASC, date DESC ';
if ($id != "0") { $query .= ' LIMIT '.$id; }

$result = mysql_query($query);
// $hurl = get_det_var("hurl");
$return = NULL;$body = NULL;$head = NULL;
$head .= enclose("title",get_det_var("sitename").' '. $cat,"");
$head .= '<link rel="alternate" type="application/rss+xml" href="'.$hurl.'/rss/'.$cat.'" title="' . get_det_var("sitename") . ' '.$cat.' feed" />';
$head .= styles($css_def);
$head .= head();
$head = enclose('head',$head,'');

$body .= enclose('div',get_det_var("sitename"),'id="head"');

while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$loop = NULL;
	$foot = NULL;
	$comments = NULL;
	$rate = NULL;
	
	$loop .= enclose('div',get_day($line['date']),'class="bigdate"');

	$title = enclose('a',html_entity_decode($line['title']),'href="'.$hurl.'/show/'.$line['id'].'"');
	$loop .= enclose('div',$title,'class="title"');
	
	$loop .= enclose('div',$line['date'],'class="date"');
	$loop .= enclose('div',html_entity_decode($line['intro']),'class="text"');
	
	if ($line['main'] != "") { 
		$foot .= enclose('a','Read more','href="'.$hurl.'/show/'.$line['id'].'"'); 
	}

	$foot .= ' Posted by ' . enclose('a',$line['owner'],'href="'.$hurl.'/user/'.$line['owner'].'"') . ' ';
	
	if ($line['commentable'] >= 1) { 
		if (comments($line['id']) != 1) { 
			$comment = 's'; 
		}
		$foot .= enclose('a',comments($line['id']). ' comment'.$comment,'href="'.$hurl.'/show/'.$line['id'].'"');
	}

	$loop .= enclose('div',$foot,'class="foot"');

	if ($line['ratable'] == 0) {
		$rate .= enclose('a','-','href="'.$hurl.'/rating/lower/'.$line['id'].'/'.get_transaction_key().'"');
		$rate .= '(' . ratings($line['id']) . ')';
		$rate .= enclose('a','+','href="'.$hurl.'/rating/raise/'.$line['id'].'/'.get_transaction_key().'"');
	}

	$loop .= enclose('div',$rate,'class="rate"');

	$body .= enclose('div',$loop,'class="entry"');
}
$body = enclose('div',$body,'id="content"') . menu();
$body = enclose('body',$body,'');
$return = enclose('html',$head . $body,'xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"');
//$return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . $return;
echo $return;
?>
