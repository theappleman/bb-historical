<?
require_once('userconf.php');
session_start();
$link = mysql_connect($db_host, $db_user, $db_pass) or die('Could not connect: ' . mysql_error());
mysql_select_db($db_data) or die('Could not select database: ' . mysql_error() );

function head() {
	$meta .= '<meta http-equiv=Content-Type content="text/html; charset=UTF-8">';
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
	mysql_query('UPDATE '.$GLOBALS['db_prefix'].'data SET date = '.date($GLOBALS['date_fmt']).' WHERE id = "'.$id.' LIMIT 1') or die('Could not reset date');
}

function styles($default) {
	$styles = array('style','style2',"style3");
	while ($first != $default) {
		foreach ($styles as $style) {
			if ($style == $default) {
				$first = $default;
				break;
			}
		}
	}
	$return .= '<link rel="stylesheet" title="default" href="'.$GLOBALS['hurl'].'/'.$default.'.css" />';
	$stylenum = 1;
	foreach ($styles as $style) {
		if ($style != $default) {
			$return .= '<link rel="alternate stylesheet" title="alternate '.$stylenum.'" href="'.$GLOBALS['hurl'].'/'.$style.'.css" />';
			$stylenum++;
		}
	}
	return $return;
}

function mod_change($cat, $id) { 
	$result = mysql_fetch_array(mysql_query('SELECT '.$cat.',section FROM '.$GLOBALS['db_prefix'].'data WHERE id = "'.$id.'"'), MYSQL_ASSOC);
	if ($result[$cat] == 1) { $nr = 0; } else { $nr = 1; }
	mysql_query('UPDATE '.$GLOBALS['db_prefix'].'data
		SET '.$cat.' = "'.$nr.'"
		WHERE id = '.$id.'
		LIMIT 1') or die('Change failed. ' . mysql_error() );
	$cm = explode(",",$result['section'],2); 
	return($cm[0]); 
}

function comments($id) { 
	$query = 'SELECT COUNT(*) FROM '.$GLOBALS['db_prefix'].'data WHERE commentref = "'.$id.'" AND moderated != "1" AND section = "comments"';
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
	$return = NULL;
	$site_menu = get_det_array("menu");
	$site_adm_menu = get_det_array("admmenu");
	foreach ($site_menu as $key => $link) {
		$sitemenu .= enclose('a',ucwords($key),'href="'.$link.'"');
		}
	$return .= enclose("div",$sitemenu,'class="mainmenu"');
	
	$pages_q = 'SELECT id, title FROM '.$GLOBALS['db_prefix'].'data WHERE section LIKE "%pages%" AND moderated != 1 ORDER BY sticky ASC, date ASC';
	$pages_r = mysql_query($pages_q);
	while ($pages = mysql_fetch_array($pages_r, MYSQL_ASSOC)) {
		$pagelist .= enclose('a',ucwords($pages['title']),'href="'.$GLOBALS['hurl'].'/show/'.$pages['id'].'"');
		}
	$return .= enclose("div",$pagelist,'class="mainmenu"');
	
	if (chkauth($_SESSION['auth']) <= -1) {
	$auth .= enclose("a","Login",'href="'.$GLOBALS['hurl'].'/login"');
	}
	
	if (chkauth($_SESSION['auth']) >= 0) {
		foreach ($site_adm_menu as $key => $lvl) {
			if ($_SESSION['auth'] >= $lvl) { 
				$auth .= enclose('a',ucwords($key),'href="'.$GLOBALS['hurl'].'/'.$key.'"');
				}
		}
	}
	$return .= enclose('div',$auth,'class="mainmenu"');
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
    $return_value = mysql_query('INSERT INTO '.$GLOBALS['db_prefix'].'transactions (transaction_key) VALUES ("'.$key.'")');
    if ($return_value === false) { return false; }
    else { return true; }
}

function get_det_var($var) {
 $query = 'SELECT value FROM '.$GLOBALS['db_prefix'].'webdet WHERE type = "'.$var.'" LIMIT 1';
 $result = mysql_query($query);
 $final = mysql_result($result,0);
 return $final;
}

function get_det_vn($type,$name) {
	$query = 'SELECT value FROM '.$GLOBALS['db_prefix'].'webdet WHERE type = "'.$type.'" AND name="'.$name.'" LIMIT 1';
	$result = mysql_query($query);
	$final = mysql_result($result,0);
	return $final;
}

function get_det_array($type) {
	$key = NULL;
	$value = NULL;
	$query = 'SELECT name,value 
	FROM '.$GLOBALS['db_prefix'].'webdet 
	WHERE type = "'.$type.'"';
	$result = mysql_query($query);
	while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$key .= $line['name'].',';
	$value .= $line['value'].','; }
	$keys = explode(",",$key);
	$values = explode(",",$value);
	$array = array_combine($keys, $values);
	return $array;
}

function login($username, $password) {
	$result = mysql_query('SELECT username, password,type FROM '.$GLOBALS['db_prefix'].'users');
	while ($user = mysql_fetch_array($result, MYSQL_ASSOC)) { 
		if ($username == $user['username']) {
			if (crypt($password,$user['password']) == $user['password']) {
				$_SESSION['auth'] = $user['type'];
				$_SESSION['name'] = $user['username'];
				return $_SESSION['auth']; 
			} else { return -2; } 
		}
	}
	return -3;
}

function chkauth($auth) {
	if ($auth != "") {	return $auth; }
	return -1; 
}

?>
