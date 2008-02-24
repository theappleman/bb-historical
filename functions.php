<?
require_once('userconf.php');
global $hurl;
global $datefmt;
global $db_prefix;

$link = mysql_pconnect($db_host, $db_user, $db_pass) or die('Could not connect. Have you read the installation instructions?');
mysql_select_db($db_data) or die('Could not select database. Have you read the installation instructions? ' );

function is_image($filename) {
	$type=getimagesize($filename) or die("Not an image (gif/jpeg/png)");
	$image_type = $type['mime'];
	if ($image_type == "image/gif" || $image_type == "image/jpeg" || $image_type == "image/png") {
		return $image_type;
	} else { return false; }
}

function make_thumb($filename) {
  global $width, $height, $uploaddir;
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
          imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
          case 'image/gif': $image = imagegif($image_p,$thumbfile);
				break;
			case 'image/jpeg': $image = imagecreatefromjpeg($fullfile);
          imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
          case 'image/jpeg': $image = imagejpeg($image_p,$thumbfile);
				break;
			case 'image/png': $image = imagecreatefrompng($fullfile);
          imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
          case 'image/png': $image = imagepng($image_p,$thumbfile);
				break;
			default: exit("Somehow, there is an error");
		}
	}
  return true;
}

function head() {
	$meta .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
	$meta .= '<script src="'.$hurl.'/ie7-standard-p.js" type="text/javascript"></script>';
	return $meta;
}

function comment_types() {
	$comments = array(2=>'Commentable',1=>'Show current',0=>'Not commentable');
	return $comments;
}

function get_day($id) {
	list($p,$l) = explode(" ",$id,2);
	list($year,$month,$day) = explode("-",$p,4);
	return $day;
}

function single_section($cat) {
	$cm = explode(",",$cat,2);
	return $cm[0];
}

function date_reset($id){
	mysql_query('UPDATE '.$db_prefix.'data SET date = '.date($datefmt).' WHERE id = "'.$id.' LIMIT 1') or die('Could not reset date');
}

function styles() {
	global $hurl, $style;
	$return = enclose('link','','rel="stylesheet" href="'.$hurl.'/'.$style.'.css" type="text/css" title="default"');
	return $return;
}

function mod_change($cat, $id) { 
	$result = mysql_fetch_array(mysql_query('SELECT '.$cat.',section FROM '.$db_prefix.'data WHERE id = "'.$id.'"'), MYSQL_ASSOC);
	if ($result[$cat] == 1) { $nr = 0; } else { $nr = 1; }
	mysql_query('UPDATE '.$db_prefix.'data
		SET '.$cat.' = "'.$nr.'"
		WHERE id = '.$id.'
		LIMIT 1') or die('Change failed. ' . mysql_error() );
	$cm = explode(",",$result['section'],2); 
	return($cm[0]); 
}

function comments($id) { 
	global $db_prefix;
	$query = 'SELECT COUNT(*) FROM '.$db_prefix.'data WHERE commentref = "'.$id.'" AND moderated != "1" AND section = "comments"';
	$result = mysql_result(mysql_query($query),0);
	return $result;
} 

function ratings($id) { 
	global $db_prefix;
	$query = 'SELECT rating FROM '.$db_prefix.'data WHERE id = "'.$id.'"';
	$result = mysql_result(mysql_query($query),0);
	return $result;
} 

if (!function_exists('array_combine')) { function array_combine($keys, $values) {
		$result = array() ;
		while( ($k=each($keys)) && ($v=each($values)) ) $result[$k[1]] = $v[1] ;
		return $result ;
	}
}

function menu() {
	global $menu, $hurl, $db_prefix;
	$return = NULL;
	foreach ($menu as $key=>$link) {
		$sitemenu .= enclose('a',ucwords($key),'href="'.$link.'"');
		}
	$return .= enclose("div",$sitemenu,'class="mainmenu"');

	$rslt = mysql_query('SELECT section FROM '.$db_prefix.'data');

	while($ln = mysql_fetch_assoc($rslt)) {
	$rry .= $ln['section'] .',';
	}
	$array = explode(",",$rry);

	$rslt = NULL;
	foreach (array_unique($array) as $table) { $rslt .= enclose('a',$table,'href="'.$hurl.'/'.$table.'"'); }
	$return .= enclose('div',$rslt,'class="mainmenu"');
	global $snapcode;
	if ($snapcode != "") {
		$return .= enclose('script','','type="text/javascript" src="http://shots.snap.com/ss/'.$snapcode.'/snap_shots.js"');
	}
	return $return;
}

function get_transaction_key() {
    return uniqid('', true);
}

function enclose($type,$content,$opts) {
	$return = NULL;
	$return .= '<'.$type;
	if ($opts != "") { $return .= ' '.$opts; }
	$return .= '>';
	$return .= $content;
	$return .= '</'.$type.'>';
	return $return;
}

function check_transaction_key($key) {
    $return_value = mysql_query('INSERT INTO transactions (transaction_key) VALUES ("'.$key.'")');
    if ($return_value === false) { return false; }
    else { return true; }
}

?>
