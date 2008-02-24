<?
// show.php
// show a post

require_once('functions.php');

$id = $_REQUEST['id'];
$_REQUEST = array(NULL);

$query = 'SELECT title,date,section,intro,commentable,rateable,rating 
	FROM '.$db_prefix.'data 
	WHERE id ="' . $id . '"
	LIMIT 1';
$result = mysql_query($query);
$query2 = 'SELECT id,title,date,intro,rateable,rating,commentable 
	FROM '.$db_prefix.'data  
	WHERE moderated != 1 
		AND date <= "'.date($datefmt).'" 
		AND commentref="'.$id.'" 
		AND rating >= -50
	ORDER BY date ASC';
$result2 = mysql_query($query2);
$com_num = 0;
$line = mysql_fetch_array($result, MYSQL_ASSOC);

$return = NULL;$head = NULL;$body = NULL;$comments = NULL;$bot = NULL;$box = NULL;$script=NULL;
$head .= enclo_s('title',$sitename,'');
$head .= head();

$body .= enclose('div',$sitename,'id="head"');
$entry .= enclose('div',$com_num,'class="bigdate"');
$title = enclose('a',html_entity_decode($line['title']),'href="'.$hurl.'/show/'.$id.'"');
$entry .= enclose('div',$title,'class="title"');
$entry .= enclose('div',$line['date'],'class="date"');
if ($line['rateable'] != 1) { $entry .= enclose('div',chrate($id),'class="rate"'); }
$entry .= enclose('div',html_entity_decode($line['intro']),'class="text"');
if ($line['commentable'] >= 1) { 
	if (comments($id) != 1) { $comment = 's'; } else { $comment = NULL; }
	$foot .= enclose('a',comments($id). ' comment'.$comment,'href="'.$hurl.'/show/'.$id.'"');
}
$entry .= enclose('div',$foot,'class="foot"');
$body .= enclose('div',$entry,'class="entry"');

if ($line['commentable'] >= 1) {
	while ($line2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
		$loop = NULL;
		$foot = NULL;
		$rate = NULL;
		$com_num += 1;
		$loop .= enclose('div',$com_num,'class="bigdate"');
		$title = enclose('a',html_entity_decode($line2['title']),'');
		$loop .= enclose('div',$title,'class="title"');
		$loop .= enclose('div',$line2['date'],'class="date"');
		if ($line2['rateable'] != 1) { $loop .= enclose('div',chrate($line2['id']),'class="rate"'); }
		$loop .= enclose('div',html_entity_decode($line2['intro']),'class="text"');
		if ($line2['commentable'] >= 1) { 
			if (comments($line2['id']) != 1) { $comment = 's'; } else { $comment = NULL; }
			$foot .= enclose('a',comments($line2['id']). ' comment'.$comment,'href="'.$hurl.'/show/'.$line2['id'].'"');
		}
		$loop .= enclose('div',$foot,'class="foot"');
		$comments .= enclose('div',$loop,'class="entry"');
	} 
	$body .= enclose('div',$comments,'id="comments"');
	if ($com_num != 1) { $pl = 's'; } else { $pl = NULL; }
	$bot .= enclose('div',$com_num,'class="bigdate"');
	$bot .= enclose('div','comment'.$pl,'class="title"');
	$bot .= enclose('div',enclose('a','Comments through RSS feed','href="'.$hurl.'/rss/comments/'.$id.'"'),'class="text"');
	if ($line['commentable'] == 2) { $bot .= enclose('div',enclose('a','Post new comment',''),'class="foot"'); }
	else { $bot .= enclose('div',enclose('a','No more comments',''),'class="foot"'); }
	$body .= enclose('div',$bot,'class="entry"');
}
if ($line['commentable'] == 2) { $body .= postbox('comments',$id); }

$body = enclose('div',$body,'id="content"') . menu();
$head = enclose('head',$head,'');
$body = enclose('body',$body,'');
$return = enclose('html',$head . $body,'xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"');
//$return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . $return;
echo $return;
?>
