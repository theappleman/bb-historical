<?
// rss.php
// displays rss feeds

require_once('functions.php');

$cat = $_GET['cat'];
$id = $_GET['id'];
$_REQUEST = array(NULL);

$query = 'SELECT id,title,date,intro FROM '.$db_prefix.'data ';

if ($id != "" && $cat == "comments" && $id != "0")
	{
	$query .= 'WHERE section = "comments" 
				AND commentref = "'.$id.'" 
				AND moderated != "1" 
				AND rating >= -50
				AND date <= "'.date($datefmt).'"
			ORDER BY sticky ASC, date DESC';
	}
	else {
		if($cat != "") {
			$query .= '
			WHERE section LIKE "%'.$cat.'%" 
				AND moderated != 1 
				AND date <= "'.date($datefmt).'" 
				AND rating >= -50
			ORDER BY sticky ASC, date DESC ';
			 
		}
		else {
			$query .= 'WHERE moderated != 1 
				AND date <= "'.date($datefmt).'" 
				AND rating >= -50
			ORDER BY sticky ASC, date DESC ';
		}
	if ($id >= 1) { $query .='LIMIT '.$id; }
		else { $query .='LIMIT 20'; }
	}
$result = mysql_query($query);
	
$return = NULL;
$channel = NULL;

$channel .= enclose('title',$sitename.' '.$cat,'');
$channel .= enclose('link',$hurl,'');
$channel .= enclose('description',$meta_desc,'');
$channel .= enclose('language','en-gb','');
$channel .= enclose('pubDate',date($datefmt),'');

$channel = enclose('channel',$channel,'');

while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$item = NULL;
	$item .= enclose('title',html_entity_decode($line['title']),'');
	$item .= enclose('description',html_entity_decode($line['intro']),'');
	$item .= enclose('pubDate',$line['date'],'');
	$item .= enclose('author',$line['owner'],'');
	if ($cat == "comments") { $perm = $id; }
		else {	$perm = $line['id']; }
	$item .= enclose('guid',$hurl.'/show/'.$perm,'');
	$items .= enclose('item',$item,'');
}

$return = enclose('rss',$channel.$items,'version="2.0"');

echo $return;
?>