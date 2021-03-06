<?
// show.php
// show a post

$cat = $_REQUEST['cat'];
$id = $_REQUEST['id'];
$_REQUEST = array(NULL);

require_once('functions.php');
$opts = array(':id' => $id);
$getpost = $db->prepare('SELECT title,date,intro,commentable,image
		FROM "'. $db->quote("${db_prefix}data") .'"
		WHERE id = :id
		LIMIT 1');
$getpostcom = $db->prepare('SELECT id,title,date,intro,image
		FROM "'. $db->quote("${db_prefix}data") .'"
		WHERE commentref = :id
		ORDER BY date ASC');
$result = $getpost->execute($opts);
$result2 = $getpostcom->execute($opts);

if (!$result or !$result2)
	die("Queries failed.");

$result = $getpost->fetchAll();
$result2 = $getpostcom->fetchAll();

$com_num = 0;
$show = $link ? '/' : '/show.php?id=';
$return = $head = $body = $comments = $bot = $box = $script = NULL;
$head .= enclose(
	'title',
	$sitename
	);
$head .= head(
	"comments",
	$id
	);

$body .= enclose(
	'div',
	$sitename,
	'id="head"'
	);

if ($result) {
	foreach($result as $line) {
		$commnum = comments($id);
		$entry .= enclose('div',get_day($line['date']),'class="bigdate"');
		$entry .= enclose('div',enclose('a',$line['title'],'href="'.$hurl.$show.$id.'"'),'class="title"');
		$entry .= enclose('div',fixup(show_pic($line['image'])),'class="image"');
		$entry .= enclose('div',get_age($line['date']),'class="date"');
		if (preg_match("/^PRIVATE\s?/",$line['intro'])) {
			$line['intro'] = preg_replace("/^PRIVATE\s?/","",$line['intro']);
			$PRIVATE = TRUE;
		}
		$entry .= enclose('div',fixup(nl2br($line['intro'])),'class="text"');
		if ($line['commentable'] >= 1) {
			$comment = $commnum != 1 ? 's' : NULL;
			$foot .= enclose('a',$commnum. ' comment'.$comment,'href="'.$hurl.$show.$id.'"');
		}
		$entry .= enclose('div',$foot,'class="foot"');
		$body .= enclose('div',$entry,'class="entry"');

		if ($line['commentable'] >= 1) {
			if ($result2) {
				foreach($result2 as $line2) {
				$loop = $foot = NULL;
				$com_num += 1;
				$loop .= enclose('div',get_day($line2['date']),'class="bigdate"');
				$loop .= enclose('div',enclose('a',$line2['title'],'href="#'.$com_num.'"'),'class="title"');
				$loop .= enclose('div',fixup(show_pic($line2['image'])),'class="image"');
				$loop .= enclose('div',nl2br(fixup(preg_replace("/^PRIVATE\s?/","",$line2['intro']))),'class="text"');
				$loop .= enclose('div',get_age($line2['date']),'class="foot"');
				$comments .= enclose('a','','name="'.$com_num.'"');
				$comments .= enclose('div',$loop,'class="entry"');
				}
			}
			$body .= enclose('div',$comments,'id="comments"');
			if ($line['commentable'] == 2) {
				if ($PRIVATE===TRUE) {
					$bot .= enclose('div', postbox("comments_private",$id),'class="foot"');
				} else {
					$bot .= enclose('div', postbox("comments",$id) ,'class="foot"');
				}
			} else {
				$bot .= enclose('div',enclose('a','No more comments',''),'class="foot"');
			}
			$body .= enclose('div',$bot,'class="entry"');
		}
	}
}
$return = finish_up($head,$body,true);
//$return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . $return;
echo $return;
?>
