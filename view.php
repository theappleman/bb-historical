<?
// view.php
// view a series of posts
require_once('userconf.php');
require_once('functions.php');

$cat = $_REQUEST['cat'];
$id = $_REQUEST['id'];
$_REQUEST = array(NULL);

if ($id == "") { $id = "10"; }
$query = 'SELECT id,title,date,intro,commentable,ratable,rating 
	FROM '.$db_prefix.'data 
	WHERE section LIKE "%' . $cat . '%" 
		AND moderated != 1 
		AND date <= "'.date($datefmt).'" 
		AND rating >= -50
	ORDER BY sticky ASC, lastupd DESC,date DESC ';
if ($id != "0") { $query .= ' LIMIT '.$id; }

$result = mysql_query($query);
$return = NULL;$body = NULL;$head = NULL;
$head .= enclose("title",$sitename.' '. $cat,"");
$head .= '<link rel="alternate" type="application/rss+xml" href="'.$hurl.'/rss/'.$cat.'" title="' . $sitename . ' '.$cat.' feed" />';
$head .= styles();
$head .= head();
$head = enclose('head',$head,'');

$body .= enclose('div',$sitename,'id="head"');

while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$loop = NULL;
	$foot = NULL;
	$comments = NULL;
	$rate = NULL;
	
	$loop .= enclose('div',get_day($line['date']),'class="bigdate"');

	$title = enclose('a',html_entity_decode($line['title']),'href="'.$hurl.'/show/'.$line['id'].'"');
	$loop .= enclose('div',$title,'class="title"');
	
	$loop .= enclose('div',$line['date'],'class="date"');
	if ($line['ratable'] == 0) {
		$rate .= enclose('a','-','href="'.$hurl.'/rating/lower/'.$line['id'].'/'.get_transaction_key().'"');
		$rate .= '(' . ratings($line['id']) . ')';
		$rate .= enclose('a','+','href="'.$hurl.'/rating/raise/'.$line['id'].'/'.get_transaction_key().'"');
	}

	$loop .= enclose('div',$rate,'class="rate"');
	$loop .= enclose('div',html_entity_decode($line['intro']),'class="text"');

	// $foot .= ' Posted by ' . enclose('a',$line['owner'],'href="'.$hurl.'/user/'.$line['owner'].'"') . ' ';
	
	if ($line['commentable'] >= 1) { 
		if (comments($line['id']) != 1) { 
			$comment = 's'; 
		}
		$foot .= enclose('a',comments($line['id']). ' comment'.$comment,'href="'.$hurl.'/show/'.$line['id'].'"');
	}

	$loop .= enclose('div',$foot,'class="foot"');

	$body .= enclose('div',$loop,'class="entry"');
}
$body = enclose('div',$body,'id="content"') . menu();
$body = enclose('body',$body,'');
$return = enclose('html',$head . $body,'xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"');
echo $return;
?>
