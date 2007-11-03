<?
// user.php
// view a series of posts

require_once('userconf.php');
require_once('functions.php');

$cat = $_REQUEST['cat'];
$id = $_REQUEST['id'];
$_REQUEST = array(NULL);

$link = mysql_connect($db_host, $db_user, $db_pass) or die('Could not connect: ' . mysql_error());
mysql_select_db($db_data) or die('Could not select database: ' . mysql_error() );

if ($id == "") { $id = "10"; }
$query = 'SELECT id,title,date,intro,commentable,main,owner,ratable,rating 
	FROM '.$db_prefix.'data 
	WHERE owner = "' . $cat . '" 
		AND moderated != 1 
		AND date <= "'.date(get_det_var("datefmt")).'" 
		AND rating >= -50
	ORDER BY sticky ASC, date DESC ';
if ($id != "0") { $query .= ' LIMIT '.$id; }
$result = mysql_query($query);
echo '
<html>
	<head>
		<title>
			'. get_det_var("sitename") .' '.$cat.'
		</title>
		<meta http-equiv=Content-Type content="text/html; charset=UTF-8">
		<link rel="alternate" type="application/rss+xml" href="'.$hurl.'/rss/'.$cat.'" title="'.$cat.' feed" />
		'.styles("style").'
	</head>
	
	<body>
		<div id="head">
			'. get_det_var("sitename").'
		</div>
		
		<div id="content">';
		while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
			echo '<div class="entry">';
			echo '<div class="bigdate">'.get_day($line['date']).'</div>';
			echo '
				<div class="title">
					<a href="'.$hurl.'/show/'.$line['id'].'">'.html_entity_decode($line['title']).'</a>
				</div>
			';
			echo '<div class="date">'.$line['date'].'</div>';
			echo '
				<div class="text">
					'.html_entity_decode($line['intro']).'
				</div>
			';
			echo '
				<div class="foot">';
			if ($line['main'] != "") { echo '<a href="'.$hurl.'/show/'.$line['id'].'">Read more</a>. '; }
			echo 'Posted by <a href="'.$hurl.'/user/'.$line['owner'].'">'.$line['owner'].'</a>.';
			if ($line['commentable'] != 1) { echo ' <a href="'.$hurl.'/show/'.$line['id'].'">' . comments($line['id']) . ' comment';
			if (comments($line['id']) != 1) { echo 's'; } echo '</a>.'; }
			echo '
			</div>
			';
			if ($line['ratable'] != 1) {
				echo '<div class="rate">';
					echo '<a href="'.$hurl.'/rating/lower/'.$line['id'].'/'.get_transaction_key().'">-</a>';
					echo '(' . ratings($line['id']) . ')';
					echo '<a href="'.$hurl.'/rating/raise/'.$line['id'].'/'.get_transaction_key().'">+</a>'; 
				echo '</div>';	
				}
			echo '
			</div>
			';
			} // while
		echo '
		</div>
		'. menu() .'
	</body>
</html>';
?>