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
// $hurl = get_det_var("hurl");
echo '
<html>
	<head>
		<title>
			'. get_det_var("sitename") .' '.html_entity_decode($line['title']).'
		</title>
		<link rel="alternate" type="application/rss+xml" href="'.$hurl.'/rss/comments/'.$id.'" title="' . get_det_var("sitename") . ' '.html_entity_decode($line['title']).' feed" />
		<link rel="stylesheet" href="'. $hurl .'/style.css" type="text/css" title="default" />
	</head>
	
	<body>
		<div id="head">
			'. get_det_var("sitename") .'
		</div>
		
		<div id="content">';
			
			echo '
				<div class="entry">
			';
			echo '
				<div class="bigdate">'.$com_num.'</div>
			';
			echo '
				<div class="title">
					<a>'.html_entity_decode($line['title']).'</a>
				</div>
			';
			echo '
				<div class="date">
					'.$line['date'].'
				</div>
			';
			echo '
				<div class="text">
					'.html_entity_decode($line['intro']).'
				</div>
			';
			echo '
				<div class="text">
					' . html_entity_decode($line['main']) . '
				</div>
			';
			echo '
				<div class="foot">';
					echo 'Posted by <a href="'.$hurl.'/user/'.$line['owner'].'">'.$line['owner'].'</a>.';
					if ($line['commentable'] != 1) { echo ' <a>' . comments($id) . ' comment';
					if (comments($id) != 1) { echo 's'; } echo '</a>.'; }
			echo '</div>';
			if ($line['ratable'] != 1) {
				echo '<div class="rate">';
					echo '<a href="'.$hurl.'/rating/lower/'.$id.'/'.get_transaction_key().'">-</a>';
					echo ' ' . ratings($id) . ' ';
					echo '<a href="'.$hurl.'/rating/raise/'.$id.'/'.get_transaction_key().'">+</a>'; 
				echo '</div>';	
				}
			echo '
			</div>
			';
			if ($line['commentable'] != 1) {
				echo '<div id="comments">';
				while ($line2 = mysql_fetch_array($result2, MYSQL_ASSOC)) {
					$com_num += 1;
					echo '<div class="entry">';
						echo '
							<div class="bigdate">'.$com_num.'</div>
						';
						echo '<div class="title">';
							echo '<a href="'.$hurl.'/user/'. $line2['title'] . '">'. $line2['title'] . '</a>';
						echo '</div>';
						echo '<div class="date">' . $line2['date'] . '</div>';
						echo '<div class="text">' . html_entity_decode($line2['intro']) . '</div>';
						echo '
							<div class="foot">';
							echo 'Posted by <a href="'.$hurl.'/user/'. $line2['title'] . '">'.$line2['title'].'</a>.';
							echo '</div>';
							if ($line2['ratable'] != 1) {
								echo '<div class="rate">';
									echo '<a href="'.$hurl.'/rating/lower/'.$line2['id'].'/'.get_transaction_key().'">-</a>';
									echo ' ' . ratings($line2['id']) . ' ';
									echo '<a href="'.$hurl.'/rating/raise/'.$line2['id'].'/'.get_transaction_key().'">+</a>'; 
								echo '</div>';	
							}
					echo '</div>';
					}
				echo '</div>';
				
				echo '<div class="entry">
					<div class="bigdate">
						'.$com_num.'
					</div>
					<div class="title">comments</div>
					<div class="text"><a href="'.$hurl.'/rss/comments/'.$id.'">Comments through RSS feed</a></div>
					<div class="foot"><a>Post a new comment</a></div>
					</div>';
				echo '<div class="entry">
					<form name="frm_com" action="'.$GLOBALS['hurl'].'/addnew.php" method="post">
					<input type="hidden" name="cat" value="comments" />
					<input type="hidden" name="commentref" value="'.$id.'" />
					<input type="hidden" name="moderated" />
					<input type="hidden" name="commentable" />
					<input type="hidden" name="transaction_key" value="'.get_transaction_key().'" />
					<p class="name">Name: <input type="text" name="title" value="'	.	$_SESSION['name']	.	'" /></p>
					<textarea name="intro" rows="4"></textarea>
					<div class="foot"><input type="submit" value="Lets go!" /><input type="reset" value="Reset" /></div>
					</form>
					</div>
					<script language="JavaScript">
						var frmvalidator  = new Validator("frm_com");
						frmvalidator.addValidation("title","req","Name is required");
						frmvalidator.addValidation("intro","req","Comment is required");
						frmvalidator.addValidation("title","maxlength=100","Title must be less than 100 characters");
						frmvalidator.addValidation("intro","maxlength=500","Comment must be less than 500 characters");
					</script>
					';
			}
		echo '
		</div>
		'. menu() .'
	</body>
</html>';
?>