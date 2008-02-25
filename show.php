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
list($title,$date,$section,$intro,$commentable,$rateable,$rating) = mysql_fetch_array($result, MYSQL_ASSOC);

$return = NULL;$head = NULL;$body = NULL;$comments = NULL;$bot = NULL;$box = NULL;$script=NULL;
$head .= enclose('title',$sitename,'');
$head .= head();

$body .= enclose('div',$sitename,'id="head"');
$entry .= enclose('div',get_day($date),'class="bigdate"');
$entry .= enclose('div',enclose('a',html_entity_decode($title),'href="'.$hurl.'/show/'.$id.'"'),'class="title"');
$entry .= enclose('div',$date,'class="date"');
if ($rateable != 1) { $entry .= enclose('div',chrate($id),'class="rate"'); }
$entry .= enclose('div',html_entity_decode($intro),'class="text"');
if ($commentable >= 1) { 
	if (comments($id) != 1) { $comment = 's'; } else { $comment = NULL; }
	$foot .= enclose('a',comments($id). ' comment'.$comment,'href="'.$hurl.'/show/'.$id.'"');
}
$entry .= enclose('div',$foot,'class="foot"');
$body .= enclose('div',$entry,'class="entry"');

if ($commentable >= 1) {
	while (list($uid2,$title2,$date2,$intro2,$rateable2,$rating2,$commentable2) = mysql_fetch_array($result2, MYSQL_ASSOC)) {
		$loop = NULL;
		$foot = NULL;
		$rate = NULL;
		$com_num += 1;
		$loop .= enclose('div',get_day($date2),'class="bigdate"');
		$loop .= enclose('div',$enclose('a',html_entity_decode($title2),''),'class="title"');
		$loop .= enclose('div',$date2,'class="date"');
		if ($rateable2 != 1) { $loop .= enclose('div',chrate($uid2),'class="rate"'); }
		$loop .= enclose('div',html_entity_decode($intro2),'class="text"');
		if ($commentable2 >= 1) { 
			if (comments($uid2) != 1) { $comment = 's'; } else { $comment = NULL; }
			$foot .= enclose('a',comments($uid2). ' comment'.$comment,'href="'.$hurl.'/show/'.$uid2.'"');
		}
		$loop .= enclose('div',$foot,'class="foot"');
		$comments .= enclose('div',$loop,'class="entry"');
	} 
	$body .= enclose('div',$comments,'id="comments"');
	$bot .= enclose('div',$com_num,'class="bigdate"');
  if ($com_num != 1) { $pl = 's'; } else { $pl = NULL; }
	$bot .= enclose('div','comment'.$pl,'class="title"');
	$bot .= enclose('div',enclose('a','Comments through RSS feed','href="'.$hurl.'/rss/comments/'.$id.'"'),'class="text"');
	if ($commentable == 2) { $bot .= enclose('div',enclose('a','Post a comment',''),'class="foot"'); }
	else { $bot .= enclose('div',enclose('a','No more comments',''),'class="foot"'); }
	$body .= enclose('div',$bot,'class="entry"');
}
if ($line['commentable'] == 2) { $body .= postbox('comments',$id); }

$return = finish_up($head,$body);
//$return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . $return;
echo $return;
?>