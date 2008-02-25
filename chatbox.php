<?
// comment.php
// view the chatbox and post

require_once('functions.php');

$cat = $_REQUEST['cat'];
$id = $_REQUEST['id'];
$_REQUEST = array(NULL);

if ($id == "") { $id = "10"; }
$query = 'SELECT id,title,date,intro,commentable,rateable,rating 
	FROM '.$db_prefix.'data 
	WHERE section = "'.$cat.'" 
		AND moderated != 1 
		AND date <= "'.date($datefmt).'" 
		AND rating >= -50
	ORDER BY sticky ASC,lastupd DESC, date DESC ';
if ($id != "0") { $query .= ' LIMIT '.$id; }

$result = mysql_query($query);

$return = NULL;$body = NULL;$head = NULL;
$head .= enclose("title",$sitename.' '. $cat,"");
$head .= head();

$body .= enclose('div',$sitename,'id="head"');

while (list($uid,$title,$date,$intro,$commentable,$rateable,$rating) = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$loop = NULL;
	$foot = NULL;
	$comments = NULL;
	$rate = NULL;
	
	$loop .= enclose('div',get_day($date),'class="bigdate"');
	$loop .= enclose('div',enclose('a',html_entity_decode($title),'href="'.$hurl.'/show/'.$uid.'"'),'class="title"');
	$loop .= enclose('div',$date,'class="date"');
	if ($ratable == 0) { $loop .= enclose('div',chrate($uid),'class="rate"'); }
	$loop .= enclose('div',html_entity_decode($intro),'class="text"');
	
	if ($commentable >= 1) { 
		if (comments($uid) != 1) { $comment = 's'; } else { $comment = NULL; }
		$foot .= enclose('a',comments($uid). ' comment'.$comment,'href="'.$hurl.'/show/'.$uid.'"');
	}
	$loop .= enclose('div',$foot,'class="foot"');
if (comments($uid) >= 1) {
	$query2 = 'SELECT id,title,date,intro,rateable,rating 
		FROM '.$db_prefix.'data  
		WHERE moderated != 1 
			AND date <= "'.date($datefmt).'" 
			AND commentref="'.$uid.'" 
			AND rating >= -50
		ORDER BY sticky ASC, date DESC
		LIMIT 1';
	$result2 = mysql_query($query2);
	while (list($uid2,$title2,$date2,$intro2,$rateable2,$rating2) = mysql_fetch_array($result2, MYSQL_ASSOC)) {
		$nloop = NULL;
		$foot = NULL;
		$rate = NULL;
		$nloop .= enclose('div',get_day($date2),'class="bigdate"');
		$title = ;
		$nloop .= enclose('div',enclose('a',html_entity_decode($title2),'href="'.$hurl.'/show/'.$uid.'"'),'class="title"');
		$nloop .= enclose('div',$date2,'class="date"');
		if ($rateable2 != 1) { $nloop .= enclose('div',chrate($uid2),'class="rate"'); }
		$nloop .= enclose('div',html_entity_decode($intro2),'class="text"');
    if ($commentable == 2) { $nloop .= enclose('div',enclose('a','Post a comment','href="'.$hurl.'/show/'.$uid.'"'),'class="foot"'); }
    else { $nloop .= enclose('div',enclose('a','No more comments','href="'.$hurl.'/show/'.$uid.'"'),'class="foot"'); }
		$comments .= enclose('div',$nloop,'class="entry"');
	}
	$loop .= enclose('div',$comments,'id="comments"');
}	 
	$body .= enclose('div',$loop,'class="entry"');
}
if ( !in_array($cat, $nochat) ) { $body .= postbox($cat,0); }
$return = finish_up($head,$body);
//$return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . $return;
echo $return;
?>