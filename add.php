<?
//add.php
//full interface to add a new item
require_once('userconf.php');
require_once('functions.php');

$return = NULL;$body = NULL;$head = NULL;$rry = NULL;$commentable = NULL;
$head .= enclose("title",get_det_var("sitename").' add',"");
$head .= '<link rel="alternate" type="application/rss+xml" href="'.$hurl.'/rss/'.$cat.'" title="' . get_det_var("sitename") . ' '.$cat.' feed" />';
$head .= styles($css_def);
$head .= enclose('script','','src="'.$hurl.'/gen_validatorv2.js" type="text/javascript"');
$head .= head();
$head = enclose('head',$head,'');

$top .= enclose('div',get_det_var("sitename"),'id="head"');
$rslt = mysql_query('SELECT section FROM '.$db_prefix.'data');
while($ln = mysql_fetch_assoc($rslt)) { $rry .= $ln['section'] .','; }
$rry .= 'other';
$array2 = explode(",",$rry);
$tables = array_unique($array2);
foreach ($tables as $table) { $form .= enclose('option',$table,'value="'.$table.'"'); }
$form = 'Table: ' . enclose('select',$form,'name="cat"');
$form .= '<input type="text" name="section" maxlength="100" />
	<br />
	Title: <input type="text" name="title" maxlength="100" />
	Date: <input type="text" name="date" value="' . date(get_det_var('datefmt')) . '" /><input type="checkbox" name="reset" /><br />';
$form .= 'Intro: <br />' . enclose('textarea','','name="intro" rows="5" columns="100"');
$form .= 'Main: <br />' . enclose('textarea','','name="main" rows="20" columns="100"');
	foreach(comment_types() as $value => $comdesc) {
		$commentable .= enclose('option',$comdesc,'value="'.$value.'"');
	}
$form .= enclose('select',$commentable,'name="commentable"') . '<br />';
$form .= 'Moderate: <input type="checkbox" name="moderated" /><br />
	Sticky: <input type="checkbox" name="sticky" /><br />
	Ratable: <input type="checkbox" name="ratable" /><br />
	<input type="hidden" name="session_id" value="'. session_id() .'" />
	<input type="hidden" name="transaction_key" value="'.get_transaction_key().'" />
	<input type="hidden" name="owner" value="'.$_SESSION['name'].'" />
	<input type="submit" value="Lets go!" /><input type="reset" value="Reset" />';
$body .= enclose('form',$form,'action="'.$GLOBALS['hurl'].'/addnew" name="frm_add" method="post"');
$body .= enclose('script','var frmvalidator  = new Validator("frm_add");
	frmvalidator.addValidation("title","req","Title is a required field");
	frmvalidator.addValidation("intro","req","Intro is a required field");
	frmvalidator.addValidation("title","maxlength=100","Title must be less than 100 characters");
	frmvalidator.addValidation("section","maxlength=100","Section must be less than 100 characters");
	frmvalidator.addValidation("intro","maxlength=500","Intro must be less than 500 characters");
	frmvalidator.addValidation("main","maxlen=2000","Main must be less than 2000 characters");'
	,'type="text/javascript"');
$body = enclose('div',$body,'class="entry"');
$body = enclose('div',$top . $body,'id="content"') . menu();
$body = enclose('body',$body,'');
$return = enclose('html',$head . $body,'xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"');
//$return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . $return;
echo $return;
?>