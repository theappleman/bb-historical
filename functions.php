<?
// functions.php
// contains function definitions and essential stuff

require_once('userconf.php');

$link = mysql_pconnect($db_host, $db_user, $db_pass) or die('Could not connect. Have you read the installation instructions?');
mysql_select_db($db_data) or die('Could not select database. Have you read the installation instructions? ' );

function postbox($cat,$id) {
  global $hurl, $accept;
  $box = NULL;
  $name = "form_form";
  if ($id != 0) { $box = enclo_s('input','type="hidden" name="commentref" value="'.$id.'"'); }
    $box .= enclo_s('input','type="hidden" name="cat" value="'.$cat.'"');
    $box .= enclo_s('input','type="hidden" name="moderated"');
    $box .= enclo_s('input','type="hidden" name="transaction_key" value="'.get_transaction_key().'"');
    $box .= enclo_s('input','type="hidden" name="commentable" value="2"');
		$box .= enclose('p','Name: '.enclo_s('input','name="title" tabindex="1" accesskey="q"').'&nbsp;'.enclo_s('input','type="file" accesskey="s" name="userfile" tabindex="3" accept="'.$accept.'"'),'class="name"');
		$box .= enclose('textarea','','name="intro" rows="5" columns="100" tabindex="2" accesskey="w"');
		$box .= enclose('div',enclo_s('input','type="submit" value="Lets go!"').enclo_s('input','type="reset" value="Reset"'),'class="foot"');
		$box = enclose('form',$box,'name="'.$name.'" action="'.$hurl.'/addnew.php" method="post" enctype="multipart/form-data"');
		$box = enclose('div',$box,'class="entry"');
		$script .= enclose('script','var frmvalidator  = new Validator("'.$name.'");
				frmvalidator.addValidation("title","req","Name is required");
        frmvalidator.addValidation("title","maxlength=100","Name must be less than 100 characters");
				frmvalidator.addValidation("intro","req","Comment is required");
        frmvalidator.addValidation("intro","maxlength=1000","Comment must be less than 1000 characters");','type="text/javascript"');
		$box = $box . $script;
		return $box;
 }

 function finish_up($head,$body) {
  $body = enclose('div',$body,'id="content"') . menu();
  $head = enclose('head',$head,'');
  $body = enclose('body',$body,'');
  $finish = enclose('html',$head . $body,'xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"');
  return $finish;
  }

 function chrate($id) {
   global $hurl;
   $rate = NULL;
   $rate .= enclose('a','-','href="'.$hurl.'/rating/lower/'.$id.'/'.get_transaction_key().'"');
   $rate .= '(' . ratings($id) . ')';
   $rate .= enclose('a','+','href="'.$hurl.'/rating/raise/'.$id.'/'.get_transaction_key().'"');
   return $rate;
  }

function show_pic($image) {
  global $uploaddir;
  if ($image != "" && is_image($uploaddir.$image)) {
    list($thumb,$rand,$filename) = explode('-',$image,3);
    if($thumb == "thumb" && is_image($uploaddir."thumb-" . $rand . '-' . $filename)) {
      $thumbname = "thumb-" . $rand . '-' . $filename;
      $filename = $rand . '-' . $filename;
    } else {
      $thumbname = $image;
      $filename = $image;
    }
    return htmlentities('<br />'.enclose('a',enclo_s('img','src="'.$hurl.'/uploaded/'.$thumbname.'" '. implode( array_splice( getimagesize( $uploaddir.$thumbname ), 3, 1 ) ) ),'href="'.$hurl.'/uploaded/'.$filename.'"'));
  } else { return ''; }
}

function is_image($filename) {
	$type=@getimagesize($filename);
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

function head($cat,$id) {
  global $hurl, $style, $sitename;
	$meta = enclose('link','','rel="stylesheet" href="'.$hurl.'/'.$style.'.css" type="text/css" title="default"');
  $meta .= enclose('link','','rel="alternate" type="application/rss+xml" href="'.$hurl.'/rss/'.$cat.'/'.$id.'" title="' . $sitename . ' feed"');
	$meta .= enclo_s('meta','http-equiv="Content-Type" content="text/html; charset=UTF-8"');
  $meta .= enclose('script','','src="'.$hurl.'/ie7-standard-p.js" type="text/javascript"');
  $meta .= enclose('script','','src="'.$hurl.'/gen_validatorv2.js" type="text/javascript"');
	return $meta;
}

function comment_types() {
	$comments = array(2=>'Commentable',1=>'Show current',0=>'Not commentable');
	return $comments;
} // currently unused

function get_day($id) {
	list($p,$l) = explode(" ",$id,2);
	list($year,$month,$day) = explode("-",$p,4);
	return $day;
}

function single_section($cat) {
	list($cm) = explode(",",$cat,2);
	return $cm;
}

function date_reset($id){
  global $datefmt, $db_prefix;
	mysql_unbuffered_query('UPDATE '.$db_prefix.'data SET date = '.date($datefmt).' WHERE id = "'.$id.' LIMIT 1') or die('Could not reset date');
} // currently unused

function mod_change($cat, $id) {
  global $db_prefix;
	list($cat,$section) = mysql_fetch_array(mysql_query('SELECT '.$cat.',section FROM '.$db_prefix.'data WHERE id = "'.$id.'"'), MYSQL_ASSOC);
	if ($result[$cat] == 1) { $nr = 0; } else { $nr = 1; }
	mysql_unbuffered_query('UPDATE '.$db_prefix.'data
		SET '.$cat.' = "'.$nr.'"
		WHERE id = '.$id.'
		LIMIT 1') or die('Change failed. ' . mysql_error() );
	return(single_section($section));
} //currently unused

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
} // currently unused

function menu() {
	global $menu, $hurl, $db_prefix, $snapcode;
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
	if ($snapcode != "") {
		$return .= enclose('script','','type="text/javascript" src="http://shots.snap.com/ss/'.$snapcode.'/snap_shots.js"');
	}
	return $return;
}

function get_transaction_key() { return uniqid('', true); }

function enclose($type,$content,$opts) {
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
    if (false === mysql_unbuffered_query('INSERT INTO transactions (transaction_key) VALUES ("'.$key.'")')) { return false; }
    else { return true; }
}

?>
