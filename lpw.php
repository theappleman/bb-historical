<?
//lpw.php
//puts a replacement password in the database
require_once('userconf.php');
require_once('functions.php');
$head = NULL; $body = NULL;

$head .= enclose('title',get_det_var("sitename"). ' login','');
$head .= enclose('script','','src="'.$hurl.'/gen_validatorv2.js" type="text/javascript"');
$head .= styles($css_def);
$head .= head();
$head = enclose('head',$head,'');

$top .= enclose('div',get_det_var("sitename"),'id="head"');
$body .= enclose('div','lpw','class="bigdate"');
if (!isset($_POST['username'])) {
$body .= enclose('div',"If you don't know your username, go back and login with any",'class="date"');
$form = enclose('form','Username: <input type="text" name="username" /><br />
Password: <input type="password" name="password" /><br />
<input type="hidden" name="transaction_key" value="'.get_transaction_key().'" />
<input type="Submit" value="Login" /><input type="reset" value="Reset" />','action="'.$hurl.'/lpw" name="frm_log" method="post"');
$script = enclose('script','var frmvalidator  = new Validator("frm_log");
frmvalidator.addValidation("username","alnum","Username must be alphanumeric");
frmvalidator.addValidation("username","req","Username must be entered");
frmvalidator.addValidation("username","maxlen=40","Username must less than 40 characters");
frmvalidator.addValidation("password","req","Password must be entered");','type="text/javascript"');

$body .= enclose('div',$form . $script,'class="text"');

$body .= enclose('div',enclose('a','lost your password',''),'class="foot"');
} else {
$username = $_POST['username'];
$password = $_POST['password'];
mysql_query('UPDATE '.$db_prefix.'users SET pw_change = "'.crypt($password).'" WHERE username = "'.$username.'" LIMIT 1');
$body .= enclose('div','Lost Password','class="title"');
$body .= enclose('div','Your new password has been stored in the database awaiting administrator approval, this is not automatic. You may be asked to verify your identity.','class="text"');
}

$body = enclose('div',$body,'class="entry"');
$body = enclose('div',$body,'id="content"') . menu();
$body = enclose('body',$top . $body,'');
$return = enclose('html',$head . $body,'');

echo $return;

?>