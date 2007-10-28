<?
require_once('userconf.php');
require_once('functions.php');

echo '
<html>
	<head>
		<title>
			'.get_det_var("sitename").' login
		</title>
		<link rel="stylesheet" href="'.$hurl.'/style.css" type="text/css" title="default" />
		<script src="'.$hurl.'/gen_validatorv2.js" type="text/javascript"></script>
	</head>
	
	<body>
		<div id="head">
			'.get_det_var("sitename").'
		</div>
		
		<div id="content">
			<div class="entry">
				<div class="bigdate">
					--
				</div>
				<div class="title">
					<a>login</a>
				</div>
				<div class="text">';
					echo '<div id="content">
					<form action="'.$hurl.'/status" name="frm_log" method="post">
					Username: <input type="text" name="username" value="'.$_POST['username'].'"/><br />
					Password: <input type="password" name="password" /><br />
					<input type="hidden" name="logpost" value="0" />
					<input type="Submit" value="Login" /><input type="reset" value="Reset" />
					</form>
					<script language="JavaScript">
						var frmvalidator  = new Validator("frm_log");
						frmvalidator.addValidation("username","alnum","Username must be alphanumeric");
						frmvalidator.addValidation("username","req","Username must be entered");
						frmvalidator.addValidation("username","maxlen=40","Username must less than 40 characters");
						frmvalidator.addValidation("password","req","Password must be entered");
					</script>
						<a href="'.$GLOBALS['hurl'].'/lpw">Lost your password?</a>
					</div>
					';

				echo '</div>
				<div class="foot">
					<a>Read more</a>. Posted by <a>Apple</a>. <a>No comments</a>.
				</div>
			</div>';
		echo '
		</div>
		'. menu() .'
	</body>
</html>';
?>