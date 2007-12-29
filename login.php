<?
require_once('userconf.php');
require_once('functions.php');
$head = NULL; $body = NULL;

$head .= enclose('title',get_det_var("sitename"). ' login','');
$head .= enclose('script','','src="'.$hurl.'/gen_validatorv2.js" type="text/javascript"');
$head .= styles($css_def);
$head .= head();
$head = enclose('head',$head,'');

$top .= enclose('div',get_det_var("sitename"),'id="head"');

$body .= enclose('div','login','class="bigdate"');
if ($_GET['dmesg'] != "") {
	$body .= enclose('div',$_GET['dmesg'],'class="title"');
}
$form = enclose('form','Username: <input type="text" name="username" value="'.$_GET['username'].'" /><br />
Password: <input type="password" name="password" /><br />
<input type="hidden" name="transaction_key" value="'.get_transaction_key().'" />
<input type="Submit" value="Login" /><input type="reset" value="Reset" />','action="'.$hurl.'/status" name="frm_log" method="post"');
$script = enclose('script','var frmvalidator  = new Validator("frm_log");
frmvalidator.addValidation("username","alnum","Username must be alphanumeric");
frmvalidator.addValidation("username","req","Username must be entered");
frmvalidator.addValidation("username","maxlen=40","Username must less than 40 characters");
frmvalidator.addValidation("password","req","Password must be entered");','type="text/javascript"');

$body .= enclose('div',$form . $script,'class="text"');
$body .= enclose('div',enclose('a','lost your password?','href="'.$hurl.'/lpw"'),'class="foot"');

$body = enclose('div',$body,'class="entry"');
$body = enclose('div',$body,'id="content"') . menu();
$body = enclose('body',$top . $body,'');
$return = enclose('html',$head . $body,'xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"');

echo $return;

?>