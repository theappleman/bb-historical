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
echo'
<html>
	<head>
		<title>
			'.get_det_var("sitename").' chatbox
		</title>
		<meta http-equiv=Content-Type content="text/html; charset=UTF-8">
		<link rel="alternate" type="application/rss+xml" href="'.$hurl.'/rss/chatbox" title="' . get_det_var("sitename") . ' chatbox feed" />
		'.styles("style3").'
		<script src="'.$hurl.'/gen_validatorv2.js" type="text/javascript"></script>
	</head>
	
	<body>
		<div id="head">
			'. get_det_var("sitename").'
		</div>
		
		<div id="content">';
			while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
				echo '<div class="entry">';
					echo '<div class="bigdate">'.get_day($line['date']).'</div>';
					echo '<div class="title"><a href="'.$hurl.'/show/'.$line['id'].'">'.html_entity_decode($line['title']).'</a></div>';
					echo '<div class="date">'.$line['date'].'</div>';
					echo '<div class="text">'.html_entity_decode($line['intro']).'</div>';
					echo '<div class="foot">';
						if ($line['main'] != "") { echo '<a href="'.$hurl.'/show/'.$line['id'].'">Read more</a>. '; }
						echo 'Posted by <a href="'.$hurl.'/user/'.$line['owner'].'">'.$line['owner'].'</a>.';
						if ($line['commentable'] != 1) { echo ' <a href="'.$hurl.'/show/'.$line['id'].'">' . comments($line['id']) . ' comment';
						if (comments($line['id']) != 1) { echo 's'; } echo '</a>.'; }
						echo '</div>';
					if ($line['ratable'] != 1) {
						echo '<div class="rate">';
							echo '<a href="'.$hurl.'/rating/lower/'.$line['id'].'/'.get_transaction_key().'">-</a>';
							echo '(' . ratings($line['id']) . ')';
							echo '<a href="'.$hurl.'/rating/raise/'.$line['id'].'/'.get_transaction_key().'">+</a>'; 
						echo '</div>';	
					}
				echo '</div>';
			}
		echo '<div class="entry">
		<form action="'.$hurl.'/addnew" name="frm_cha" method="post">
		<input type="hidden" name="cat" value="chatbox" />
		<input type="hidden" name="moderated" />
		<input type="hidden" name="commentable" />
		<input type="hidden" name="transaction_key" value="'.get_transaction_key().'" />
		<p class="name">Name: <input type="text" name="title" value="'	.	$_SESSION['name']	.	'" /></p>
		<textarea name="intro" rows="4"></textarea>
		<div class="foot"><input type="submit" value="Lets go!" /><input type="reset" value="Reset" /></div>
		</form>
		</div>
		<script type="text/javascript">
			var frmvalidator  = new Validator("frm_cha");
			frmvalidator.addValidation("title","req","Name is required");
			frmvalidator.addValidation("intro","req","Comment is required");
			frmvalidator.addValidation("title","maxlength=100","Title must be less than 100 characters");
			frmvalidator.addValidation("intro","maxlength=500","Intro must be less than 500 characters");
		</script>
		';
		echo '</div>
		'. menu() .'
	</body>
</html>';
?>