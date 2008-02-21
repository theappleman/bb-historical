<?php
// edit.php
// user interface to edit a post

require_once('userconf.php');
require_once('functions.php');

$id = $_REQUEST['id'];
$cat = $_REQUEST['cat'];
$_REQUEST = array(NULL);

$query = 'SELECT title,date,section,intro,main,commentable,owner,ratable,rating 
	FROM '.$db_prefix.'data 
	WHERE id ="' . $id . '"
	LIMIT 1';
$result = mysql_query($query);

$return = NULL;$body = NULL;$head = NULL;
$head .= styles($css_def);
$head .= enclose('script','','src="'.$hurl.'/gen_validatorv2.js" type="text/javascript"');
$head .= head();
$head .= enclose("title",get_det_var("sitename").' edit',"");
$head = enclose('head',$head,'');


?>