<?
// functions.php
// contains function definitions and essential stuff

require_once('userconf.php');

$db = NULL;
try {
	$db = new PDO($connstr, $db_user, $db_pass);
	$db->exec('CREATE TABLE IF NOT EXISTS "'. $db->quote("${db_prefix}data") .'" (
		"id" integer primary key,
		"title" varchar(100),
		"date" datetime,
		"lastupd" datetime,
		"intro" text,
		"image" varchar(256),
		"section" varchar(100),
		"commentable" tinyint(1),
		"commentref" bigint(4),
		"sticky" tinyint(1) default 1,
		UNIQUE ("id")
		)');
	$db->exec('CREATE TABLE IF NOT EXISTS "'. $db->quote("${db_prefix}transactions") .'" (
		"transaction_key" varchar(24),
		UNIQUE ("transaction_key")
	)');
} catch (PDOException $e) {
	die("Error: " . $e->getMessage() . "<br />");
}

function hurl($secure, $cat, $rhurl)
{
	$hurl = $secure ? "https://" : "http://";
	$hurl .= $cat;
	$hurl .= $rhurl;
	return $hurl;
}

$hurl = hurl($secure, $cat, $rhurl);

function get_age($date, $fuzzy=true)
{
	if (!$fuzzy)
		return $date;

	$age = time() - strtotime($date);

	if ($age > 60*60*24*365*2) {
		$age_str = intval($age/60/60/24/365);
		$age_str .= " years ago";
	} elseif ($age > 60*60*24*(365/12)*2) {
		$age_str = intval($age/60/60/24/(365/12));
		$age_str .= " months ago";
	} elseif ($age > 60*60*24*7*2) {
		$age_str = intval($age/60/60/24/7);
		$age_str .= " weeks ago";
	} elseif ($age > 60*60*24*2) {
		$age_str = intval($age/60/60/24);
		$age_str .= " days ago";
	} elseif ($age > 60*60*2) {
		$age_str = intval($age/60/60);
		$age_str .= " hours ago";
	} elseif ($age > 60*2) {
		$age_str = intval($age/60);
		$age_str .= " min ago";
	} elseif ($age > 2) {
		$age_str = $age;
		$age_str .= " sec ago";
	} else {
		$age_str = "right now";
	}
	return $age_str;
}

function postbox($cat,$id,$message="")
{
global $hurl, $accept;
  $box = NULL;
  if ($cat == "comments") { $ct = 0; } else { $ct = 2; }
  if ($id != 0) { $box = enclo_s('input','type="hidden" name="commentref" value="'.$id.'"'); }
    $box .= enclo_s('input','type="hidden" name="cat" value="'.$cat.'"');
    $box .= enclo_s('input','type="hidden" name="moderated"');
    $box .= enclose('p','I implore you! Keep this text box empty:'.enclo_s('input','type="text" name="honeybun"'),'class="hidden"');
    $box .= enclo_s('input','type="hidden" name="transaction_key" value="'.get_transaction_key().'"');
    $box .= enclo_s('input','type="hidden" name="commentable" value="'.$ct.'"');
    $box .= enclo_s('input','type="hidden" name="MAX_FILE_SIZE" value="2097152" ');
		$box .= enclose('p','Name: '.enclo_s('input','name="title" tabindex="1" value="Anonymous" accesskey="q"').'&nbsp;'.enclo_s('input','type="file" accesskey="s" name="userfile" tabindex="3"'),'class="name"');
		$box .= enclose('textarea',$message,'name="intro" rows="5" cols="100" tabindex="2" accesskey="w"');
		$box .= enclose('div',enclo_s('input','type="submit" tabindex=4" value="Lets go!"'),'class="foot"');
		$box = enclose('form',$box,'action="'.$hurl.'/addnew.php" method="post" enctype="multipart/form-data"');
    return $box;
}

function fixup($text)
{
  global $patterns;
  foreach($patterns as $key=>$value) {
    $text = preg_replace($key,$value,$text);
  }
  $textrep = array(
    '%#(.*?)@(irc\.[^\s]+)%i'=>'[[irc://$2/$1|#$1@$2]]',
    '%\[\[([^\|]*?)\]\]%'=>'[[$1|-link-]]',
    '%\[\[(.*?)\|(.*?)\]\]%'=>'<a href="$1" title="$1" >$2</a>',
    '%\s\s+%'=>' ',
    '%\*([^\s]*?)\*%'=>'<b>*$1*</b>'
    );
  $text = preg_replace(array_keys($textrep),array_values($textrep),$text);
  return $text;
 }

function finish_up($head,$body,$show=false) {
  $body = enclose('div',$body,'id="content"') . menu($show);
  $head = enclose('head',$head,'');
  $body = enclose('body',$body,'');
  $finish = enclose('html',$head . $body,'xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"');
  return $finish;
}

function show_pic($image) {
  if (!$image) { return ''; }
  global $uploaddir, $hurl, $snapcode;
  if ( is_image($uploaddir.$image)) {
    list($thumb,$rand) = explode('-',$image,2);
    if($thumb == "thumb" && is_image($uploaddir."thumb-" . $rand)) {
      $thumbname = "thumb-" . $rand;
      $filename = $rand;
    } else {
      $thumbname = $image;
      $filename = $image;
    }
	  list($width,$height) = img_size($filename);
    return "<a href=\"".$hurl."/uploaded/".$filename."\" rel=\"iv:${width}x${height}\"><img src=\"".$hurl."/uploaded/".$thumbname."\" /></a>";
  } else {
    if ( file_exists($uploaddir.$image) ) {
      if ($snapcode) { $snap = "class=\"snap_shots\"";}
      return "<a href=\"$hurl/uploaded/$image\" $snap >$image</a>"; }
    }
}

function img_size($image) {
	global $uploaddir;
	list($width,$height) = @getimagesize($uploaddir.$image);
	return array($width,$height);
}

function is_image($filename) {
	$type=@getimagesize($filename);
	$image_type = $type['mime'];
	if ($image_type == "image/gif" || $image_type == "image/jpeg" || $image_type == "image/png") {
		return $image_type;
	} else { return false; }
}

function make_thumb($filename) {
  global $width, $height, $uploaddir, $filtertype;
  $fullfile = $uploaddir . $filename;
  $thumbfile = $uploaddir . 'thumb-' . $filename;
	if ($image_type = is_image($fullfile)) {

    list($width_orig, $height_orig) = getimagesize($fullfile);

    if ($width > $width_orig && $height > $height_orig) { return false; }

    $ratio_orig = $width_orig/$height_orig;
		if ($width/$height > $ratio_orig) { $width = $height*$ratio_orig; }
    else { $height = $width/$ratio_orig; }

    $image_p = imagecreatetruecolor($width, $height);

		switch($image_type) {
			case 'image/gif': $image = imagecreatefromgif($fullfile);
				break;
			case 'image/jpeg': $image = imagecreatefromjpeg($fullfile);
				break;
			case 'image/png': $image = imagecreatefrompng($fullfile);
				break;
			default: exit("Somehow, there is an error");
		}

    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

    switch($image_type) {
    case 'image/png': $image = imagepng($image_p,$thumbfile);
      break;
    case 'image/jpeg': $image = imagejpeg($image_p,$thumbfile);
      break;
    case 'image/gif': $image = imagegif($image_p,$thumbfile);
      break;
    default: exit("Somehow, there is an error");
    }
	} else { return false; }
  return true;
}

function head($cat="",$id="") {
  global $style, $hurl, $link;
	$meta = enclose('link','','rel="stylesheet" href="'.$hurl.'/'.$style.'.css" type="text/css" title="default"');
	if( $link ) { $rss = '/rss'; $di = '/'; } else { $rss = '/rss.php?cat='; $di = '&id='; }
	$meta .= enclose('link','','rel="alternate" type="application/rss+xml" href="'.$hurl.$rss.$di.$id.'" title="' . $cat. ' feed"');
	$meta .= enclose('link','','rel="stylesheet" href="'.$hurl.'/iv.css" type="text/css"'); // image viewer
	$meta .= enclose('script','','src="'.$hurl.'/iv.js" type="text/javascript"');
	$meta .= '<meta name="robots" content="noindex, nofollow">';
	return $meta;
}

function get_day($id) {
	list($p,$l) = explode(" ",$id,2);
	list($year,$month,$day) = explode("-",$p,4);
	return $day;
}

function single_section($cat) {
	list($cm) = explode(",",$cat,2);
	return $cm;
}

function comments($id) {
	global $db,$db_prefix,$cache_time;

	$com_num = 0;
	$query = $db->prepare('SELECT id FROM "'. $db->quote("${db_prefix}data") .'" WHERE commentref = ?');
	$result = $query->execute(array("$id"));
	if (!$result)
		die("Comments failed.");
	$result = $query->fetchAll();
	if ($result) { foreach ($result as $r) { $com_num += 1; } }
	return $com_num;
}

function menu($show=false) {
	global $page, $cat, $menu, $hurl, $db, $snapcode, $cache_time, $db_prefix, $nochat, $link;
	global $secure, $rhurl;
	$return = NULL;
	foreach ($menu as $key=>$knil) {
		$sitemenu .= enclose('a',ucwords($key),'href="'.$knil.'"');
		}
	$return .= enclose("div",$sitemenu,'class="mainmenu"');

	$rslt = $db->query('SELECT DISTINCT section FROM "'. $db->quote("${db_prefix}data").'"');
	if ($rslt)
		foreach($rslt as $ln)
			$rry .= $ln['section'] .',';
	$array = explode(",",$rry);

	$rslt = NULL;
	foreach (array_unique($array) as $table) {
		if (!preg_match("/_private$/",$table)) {
			if($secure == true) {
				$thurl = "https://";
			} else {
				$thurl = "http://";
			}
			$thurl .= $table;
			$thurl .= $rhurl;
			$rslt .= enclose('a',$table,'href="'.$thurl.'"');
			}
	}
	$return .= enclose('div',$rslt,'class="mainmenu"');

  if ($show==false) {
	if( $link ) { $egap = '/-'; } else { $egap = '&page='; }
    if ( $page != "" && $page != "0" ) {
    	if($page == "1") {
   	 $pages .= enclose('a','Previous','href="'.$hurl.'/"');
	}else{
   	 $pages .= enclose('a','Previous','href="'.$hurl.$egap.($page-1).'"'); }
    }
   $pages .= enclose('a','Next','href="'.$hurl.$egap.($page+1).'"');
   /* always show next link, this function has no way of knowing if there are more */
  }

  if ($pages) { $return .= enclose('div',$pages,'class=mainmenu'); }

	if ($snapcode != "") {
		$return .= enclose('script','','type="text/javascript" src="http://shots.snap.com/ss/'.$snapcode.'/snap_shots.js"');
	}
	return $return;
}

function get_transaction_key() { return uniqid('', true); }

function enclose($type,$content,$opts = "") {
	$return = NULL;
	$return .= '<'.$type;
	if ($opts != "") { $return .= ' '.$opts; }
	$return .= '>';
	$return .= $content;
	$return .= '</'.$type.'>';
	return $return;
}

function enclo_s($type,$opts) { return '<' . $type . ' ' . $opts . ' />'; }

function check_transaction_key($key) {
	global $db_prefix, $db;
	$q = $db->prepare('INSERT INTO "'. $db->quote("${db_prefix}transactions") .'" (transaction_key) VALUES (?)');
	if (false === $q->execute(array("$key"))) { return false; }
	else { return true; }
}

?>
