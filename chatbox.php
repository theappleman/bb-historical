<?
// comment.php
// view the chatbox and post
require_once('userconf.php');
require_once('functions.php');

$id = $_REQUEST['id'];
$_REQUEST = array(NULL);

if ($id == "") { $id = "10"; }
$query = 'SELECT id,title,date,intro,commentable,main,owner,ratable,rating 
	FROM '.$db_prefix.'data 
	WHERE section = "chatbox" 
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

$box .= '<input type="hidden" name="cat" value="comments" />
	<input type="hidden" name="cat" value="chatbox" />
	<input type="hidden" name="moderated" />
	<input type="hidden" name="transaction_key" value="'.get_transaction_key().'" />
	<input type="hidden" name="commentable" value="2" />';
	$box .= enclose('p','Name: <input type="text" name="title" value="'.$_SESSION['name'].'" />','class="name"');
	$box .= enclose('textarea','','name="intro" rows="4"');
	$box .= enclose('div','<input type="submit" value="Lets go!" /><input type="reset" value="Reset" />','class="foot"');
	$box = enclose('form',$box,'name="frm_cha" action="'.$GLOBALS['hurl'].'/addnew.php" method="post"');
	$box = enclose('div',$box,'class="entry"');
	$script .= enclose('script','var frmvalidator  = new Validator("frm_cha");
			frmvalidator.addValidation("title","req","Name is required");
			frmvalidator.addValidation("intro","req","Comment is required");
			frmvalidator.addValidation("title","maxlength=100","Title must be less than 100 characters");
			frmvalidator.addValidation("intro","maxlength=500","Comment must be less than 500 characters");','type="text/javascript"');
	$box = $box . $script;
	$body .= $box;

$body = enclose('div',$body,'id="content"') . menu();
$body = enclose('body',$body,'');
$return = enclose('html',$head . $body,'');

echo $return;
?>