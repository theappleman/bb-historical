<?
// rss.php
// displays rss feeds

require_once('userconf.php');
require_once('functions.php');

$cat = $_GET['cat'];
$id = $_GET['id'];
$_REQUEST = array(NULL);

echo '
<rss version="2.0">
  <channel>
    <title>
		'. get_det_var("sitename").' '.$cat.'
	</title>
    <link>
		'.$hurl.'
	</link>
    <description>
		'. get_det_vn("meta","desc").'
	</description>
    <language>
		en-gb
	</language>
    <pubDate>
		'. date(get_det_var("datefmt")) .'
	</pubDate>
    <lastBuildDate>
		'. date(get_det_var("datefmt")) .'
	</lastBuildDate>
    <docs>
		http://blogs.law.harvard.edu/tech/rss
	</docs>
    <generator>
		notepad++ and php
	</generator>
    <managingEditor>
		'.get_det_var("email").'
	</managingEditor>
    <webMaster>
		'.get_det_var("email").'
	</webMaster>
';
	if ($id != "" && $cat == "comments" && $id != "0")
	{
	$query = 'SELECT id, title, date, intro, owner
			FROM '.$db_prefix.'data 
			WHERE section = "comments" 
				AND commentref = "'.$id.'" 
				AND moderated != "1" 
				AND rating >= -50
				AND date <= "'.date(get_det_var("datefmt")).'"
			ORDER BY sticky ASC, date DESC';
	}
	else {
		$query = 'SELECT id,title,date,intro,owner 
		FROM '.$db_prefix.'data 
		WHERE section LIKE "%'.$cat.'%" 
			AND moderated != 1 
			AND date <= "'.date(get_det_var("datefmt")).'" 
			AND rating >= -50
		ORDER BY date DESC ';
		if ($id >= 1) { $query .='LIMIT '.$id; }
		else { $query .='LIMIT 20'; } 
	}
	$result = mysql_query($query); 
	while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
   		echo '<item>';
			echo '<title>'	.	html_entity_decode($line['title'])	.	'</title>';
			echo '<description>'	.	html_entity_decode($line['intro'])	 .	 '</description>';
			echo '<pubDate>'	. 	$line['date']	 .	 '</pubDate>';
			echo '<author>'	.	$line['owner']	.	'</author>';
			echo '<guid>'.$hurl.'/show/';
				if ($cat == "comments") { echo $id; }
				else {	echo $line['id']; }
			echo '</guid>';
		echo '</item>';
	}
echo '
	</channel>
</rss>
';
?>