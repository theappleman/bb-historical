<?
	$filename = $_REQUEST['cat'];
	$_REQUEST = array(NULL);

	require_once 'userconf.php';

	header('Content-type: image/jpeg');
	$fullfile = $uploaddir . $filename;
	list($width_orig, $height_orig) = getimagesize($fullfile);

	$ratio_orig = $width_orig/$height_orig;

	if ($width/$height > $ratio_orig) {
	   $width = $height*$ratio_orig;
	} else {
		$height = $width/$ratio_orig;
	}

	// Resample
	$image_p = imagecreatetruecolor($width, $height);

	$image_type = image_type_to_mime_type( exif_imagetype( $fullfile ) );
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

	// Output
	imagejpeg($image_p, null, 100);
?>
