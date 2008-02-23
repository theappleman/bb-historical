<?
// comment.php
// view the chatbox and post
require_once('userconf.php');
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
$head .= '<link rel="alternate" type="application/rss+xml" href="'.$hurl.'/rss/'.$cat.'" title="' . $sitename . ' '.$cat.' feed" />';
$head .= styles();
$head .= enclose('script','','src="'.$hurl.'/gen_validatorv2.js" type="text/javascript"');
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
	if ($line['rateable'] == 0) {
		$rate .= enclose('a','-','href="'.$hurl.'/rating/lower/'.$line['id'].'/'.get_transaction_key().'"');
		$rate .= '(' . ratings($line['id']) . ')';
		$rate .= enclose('a','+','href="'.$hurl.'/rating/raise/'.$line['id'].'/'.get_transaction_key().'"');
	}
	$loop .= enclose('div',$rate,'class="rate"');
	$loop .= enclose('div',html_entity_decode($line['intro']),'class="text"');

	// $foot .= 'Tags: ' . enclose('a',$line['owner'],'href="'.$hurl.'/user/'.$line['owner'].'"') . ' ';
	
	if ($line['commentable'] >= 1) { 
		if (comments($line['id']) != 1) { 
			$comment = 's'; 
		} else { $comment = NULL; }
		$foot .= enclose('a',comments($line['id']). ' comment'.$comment,'href="'.$hurl.'/show/'.$line['id'].'"');
	}

	$loop .= enclose('div',$foot,'class="foot"');

if (comments($line['id']) >= 1) {
	$query2 = 'SELECT id,title,date,intro,rateable,rating 
		FROM '.$db_prefix.'data  
		WHERE moderated != 1 
			AND date <= "'.date($datefmt).'" 
			AND commentref="'.$line['id'].'" 
			AND rating >= -50
		ORDER BY sticky ASC, date DESC
		LIMIT 1';
	$result2 = mysql_query($query2);
	while ($line2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
		$nloop = NULL;
		$foot = NULL;
		$rate = NULL;
		$nloop .= enclose('div','LC','class="bigdate"');
		$title = enclose('a',html_entity_decode($line2['title']),'href="'.$hurl.'/show/'.$line['id'].'"');
		$nloop .= enclose('div',$title,'class="title"');
		$nloop .= enclose('div',$line2['date'],'class="date"');
		if ($line2['rateable'] != 1) {
			$rate .= enclose('a','-','href="'.$hurl.'/rating/lower/'.$line2['id'].'/'.get_transaction_key().'"');
			$rate .= '(' . ratings($line2['id']) . ')';
			$rate .= enclose('a','+','href="'.$hurl.'/rating/raise/'.$line2['id'].'/'.get_transaction_key().'"');
		}
		$nloop .= enclose('div',$rate,'class="rate"');
		$nloop .= enclose('div',html_entity_decode($line2['intro']),'class="text"');
		$comments .= enclose('div',$nloop,'class="entry"');
	}
	$loop .= enclose('div',$comments,'id="comments"');
}	 
	$body .= enclose('div',$loop,'class="entry"');
}
if ( !in_array($cat, $nochat) ) {
	$box .= '<input type="hidden" name="cat" value="comments" />
		<input type="hidden" name="cat" value="'.$cat.'" />
		<input type="hidden" name="moderated" />
		<input type="hidden" name="transaction_key" value="'.get_transaction_key().'" />
		<input type="hidden" name="commentable" value="2" />';
		$box .= enclose('p','Name: <input type="text" name="title" value="'.$_SESSION['name'].'" />','class="name"');
		$box .= enclose('textarea','','name="intro" rows="5" columns="100"');
		$box .= enclose('p','<input type="file" name="userfile" value="Upload .jpg,.gif or .png" />','class="name"');
		$box .= enclose('div','<input type="submit" value="Lets go!" /><input type="reset" value="Reset" />','class="foot"');
		$box = enclose('form',$box,'name="frm_cha" action="'.$GLOBALS['hurl'].'/addnew.php" method="post" enctype="multipart/form-data"');
		$box = enclose('div',$box,'class="entry"');
		$script .= enclose('script','var frmvalidator  = new Validator("frm_cha");
				frmvalidator.addValidation("title","req","Name is required");
				frmvalidator.addValidation("intro","req","Comment is required");
				frmvalidator.addValidation("title","maxlength=100","Name must be less than 100 characters");','type="text/javascript"');
		$box = $box . $script;
		$body .= $box; }

$body = enclose('div',$body,'id="content"') . menu();
$body = enclose('body',$body,'');
$return = enclose('html',$head . $body,'xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"');
//$return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . $return;
echo $return;
?>
