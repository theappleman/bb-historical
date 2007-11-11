<?
// show.php
// show a post

require_once('userconf.php');
require_once('functions.php');

$id = $_REQUEST['id'];
$_REQUEST = array(NULL);

$link = mysql_connect($db_host, $db_user, $db_pass) or die('Could not connect: ' . mysql_error());
mysql_select_db($db_data) or die('Could not select database: ' . mysql_error() );

$query = 'SELECT title,date,section,intro,main,commentable,owner,ratable,rating 
	FROM '.$db_prefix.'data 
	WHERE id ="' . $id . '"
	LIMIT 1';
$result = mysql_query($query);
$query2 = 'SELECT id,title,date,intro,ratable,rating 
	FROM '.$db_prefix.'data  
	WHERE moderated != 1 
		AND date <= "'.date(get_det_var('datefmt')).'" 
		AND commentref="'.$id.'" 
		AND rating >= -50
	ORDER BY date ASC';
$result2 = mysql_query($query2);
$com_num = 0;
$line = mysql_fetch_array($result, MYSQL_ASSOC);

$return = NULL;$head = NULL;$body = NULL;$comments = NULL;$bot = NULL;$box = NULL;$script=NULL;
$head .= enclose('title',get_det_var("sitename") .' '.html_entity_decode($line['title']),'');
$head .= styles($css_def);
$head .= enclose('script','','src="'.$hurl.'/gen_validatorv2.js" type="text/javascript"');
$head .= '<link rel="alternate" type="application/rss+xml" href="'.$hurl.'/rss/comments/'.$id.'" title="'.html_entity_decode($line['title']).' comments feed" />';
$head .= head();

$body .= enclose('div',get_det_var("sitename"),'id="head"');
$entry .= enclose('div',$com_num,'class="bigdate"');
$title = enclose('a',html_entity_decode($line['title']),'href="'.$hurl.'/show/'.$id.'"');
$entry .= enclose('div',$title,'class="title"');
$entry .= enclose('div',$line['date'],'class="date"');
$entry .= enclose('div',html_entity_decode($line['intro']),'class="text"');
$entry .= enclose('div',html_entity_decode($line['main']),'class="text"');
$foot .= ' Posted by ' . enclose('a',$line['owner'],'href="'.$hurl.'/user/'.$line['owner'].'"') . ' ';
if ($line['commentable'] >= 1) { 
	if (comments($id) != 1) { $comment = 's'; }
	$foot .= enclose('a',comments($id). ' comment'.$comment,'href="'.$hurl.'/show/'.$id.'"');
}
$entry .= enclose('div',$foot,'class="foot"');
if ($line['ratable'] != 1) {
	$rate .= enclose('a','-','href="'.$hurl.'/rating/lower/'.$id.'/'.get_transaction_key().'"');
	$rate .= '(' . ratings($id) . ')';
	$rate .= enclose('a','+','href="'.$hurl.'/rating/raise/'.$id.'/'.get_transaction_key().'"');
}
$entry .= enclose('div',$rate,'class="rate"');
$body .= enclose('div',$entry,'class="entry"');

if ($line['commentable'] >= 1) {
	while ($line2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
		$loop = NULL;
		$foot = NULL;
		$rate = NULL;
		$com_num += 1;
		$loop .= enclose('div',$com_num,'class="bigdate"');
		$title = enclose('a',html_entity_decode($line2['title']),'href="'.$hurl.'/user/'.$line2['title'].'"');
		$loop .= enclose('div',$title,'class="title"');
		$loop .= enclose('div',$line2['date'],'class="date"');
		$loop .= enclose('div',html_entity_decode($line2['intro']),'class="text"');
		$foot .= ' Posted by ' . enclose('a',$line2['title'],'href="'.$hurl.'/user/'.$line2['title'].'"') . ' ';
		$loop .= enclose('div',$foot,'class="foot"');
		if ($line2['ratable'] != 1) {
			$rate .= enclose('a','-','href="'.$hurl.'/rating/lower/'.$line2['id'].'/'.get_transaction_key().'"');
			$rate .= '(' . ratings($line2['id']) . ')';
			$rate .= enclose('a','+','href="'.$hurl.'/rating/raise/'.$line2['id'].'/'.get_transaction_key().'"');
		}
		$loop .= enclose('div',$rate,'class="rate"');
		$comments .= enclose('div',$loop,'class="entry"');
	} 
	$body .= enclose('div',$comments,'id="comments"');
	if ($com_num != 1) { $pl = 's'; } else { $pl = NULL; }
	$bot .= enclose('div',$com_num,'class="bigdate"');
	$bot .= enclose('div','comment'.$pl,'class="title"');
	$bot .= enclose('div',enclose('a','Comments through RSS feed','href="'.$hurl.'/rss/comments/'.$id.'"'),'class="text"');
	if ($line['commentable'] == 2) { $bot .= enclose('div',enclose('a','Post a new comment',''),'class="foot"'); }
	else { $bot .= enclose('div',enclose('a','No more comments',''),'class="foot"'); }
	$body .= enclose('div',$bot,'class="entry"');
}
if ($line['commentable'] == 2) {
	$box .= '<input type="hidden" name="cat" value="comments" />
		<input type="hidden" name="commentref" value="'.$id.'" />
		<input type="hidden" name="moderated" />
		<input type="hidden" name="transaction_key" value="'.get_transaction_key().'" />
		<input type="hidden" name="commentable" value="2" />';
	$box .= enclose('p','Name: <input type="text" name="title" value="'.$_SESSION['name'].'" />','class="name"');
	$box .= enclose('textarea','','name="intro" rows="4"');
	$box .= enclose('div','<input type="submit" value="Lets go!" /><input type="reset" value="Reset" />','class="foot"');
	$box = enclose('form',$box,'name="frm_com" action="'.$GLOBALS['hurl'].'/addnew.php" method="post"');
	$box = enclose('div',$box,'class="entry"');
	$script .= enclose('script','var frmvalidator  = new Validator("frm_com");
			frmvalidator.addValidation("title","req","Name is required");
			frmvalidator.addValidation("intro","req","Comment is required");
			frmvalidator.addValidation("title","maxlength=100","Title must be less than 100 characters");
			frmvalidator.addValidation("intro","maxlength=500","Comment must be less than 500 characters");','type="text/javascript"');
	$box = $box . $script;
	$body .= $box;
}

$body = enclose('div',$body,'id="content"') . menu();
$head = enclose('head',$head,'');
$body = enclose('body',$body,'');
$return = enclose('html',$head . $body,'');

echo $return;
?>