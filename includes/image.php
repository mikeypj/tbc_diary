<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		image.php
//	Desc:		funcitons relating to uploading images and reszising them
//	Author:		Rob Curle
//	Date:		2 March 2006
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////
	
	define ("IMG_FILE_SIZE", 51200);
	define ("DISPLAY_IMG_FILE_SIZE", "50Kb");
	
	
	function upload_image ($path, $filename, $width=0, $height=0, $form_field='upload') {
		$imagehw = getImageSize($_FILES[$form_field]['tmp_name']);
		$msg = 0;	
		
		if ($_FILES[$form_field]['error'] > UPLOAD_ERR_OK) {
			switch ($_FILES[$form_field]['error']) {
				case UPLOAD_ERR_INI_SIZE: $msg="The uploaded image's filesize is too large, the maximum is ".ini_get('upload_max_filesize'); break;
				case UPLOAD_ERR_FORM_SIZE: $msg="The uploaded image's filesize is too large, the maximum is ".DISPLAY_IMG_FILE_SIZE; break;
				case UPLOAD_ERR_PARTIAL: $msg="The uploaded image was only partially uploaded."; break;
				case UPLOAD_ERR_NO_FILE: $msg="No file was uploaded."; break;
				case UPLOAD_ERR_NO_TMP_DIR: $msg="Missing a temporary folder, please contact the system administrator."; break;
				case UPLOAD_ERR_CANT_WRITE: $msg="Failed to write file to disk, please contact the system administrator."; break;
			}
		}
		else if (strrpos($_FILES[$form_field]['type'], "image") === false) {		// check file type
			$msg = "The file chosen for the image is not an image";
		}
		else if ($width > 0 && $imagehw[0] != $width) {
			$msg = "The image width is incorrect, it should be {$width}.";
		}
		else if ($height > 0 && $imagehw[1] != $height) {
			$msg = "The image is height is incorrect, it should be {$height}.";
		}
		else if (!is_uploaded_file($_FILES[$form_field]['tmp_name'])) {		// check file has been uploaded
			$msg = "There has been a problem uploading your file.<br/>Please make sure the file is smaller than " . DISPLAY_IMG_FILE_SIZE;
		}
		else if (!move_uploaded_file($_FILES[$form_field]['tmp_name'], $path . $filename)) {		// check if upload was successful
			$msg = "There has been a problem uploading your file.";
		}
		else {
			chmod($path . $filename, 0644);
		}
	
		return $msg;
	}
	
	function upload_image_max_size ($path, $filename, $width=0, $height=0, $form_field='upload') {
		$imagehw = getImageSize($_FILES[$form_field]['tmp_name']);
		$msg = 0;	
		
		if ($_FILES[$form_field]['error'] > UPLOAD_ERR_OK) {
			switch ($_FILES[$form_field]['error']) {
				case UPLOAD_ERR_INI_SIZE: $msg="The uploaded image's filesize is too large, the maximum is ".ini_get('upload_max_filesize'); break;
				case UPLOAD_ERR_FORM_SIZE: $msg="The uploaded image's filesize is too large, the maximum is ".DISPLAY_IMG_FILE_SIZE; break;
				case UPLOAD_ERR_PARTIAL: $msg="The uploaded image was only partially uploaded."; break;
				case UPLOAD_ERR_NO_FILE: $msg="No file was uploaded."; break;
				case UPLOAD_ERR_NO_TMP_DIR: $msg="Missing a temporary folder, please contact the system administrator."; break;
				case UPLOAD_ERR_CANT_WRITE: $msg="Failed to write file to disk, please contact the system administrator."; break;
			}
		}
		else if (strrpos($_FILES[$form_field]['type'], "image") === false) {		// check file type
			$msg = "The file chosen for the image is not an image";
		}
		else if ($width > 0 && $imagehw[0] > $width) {
			$msg = "The image width is too large, the maximum is {$width}px.";
		}
		else if ($height > 0 && $imagehw[1] > $height) {
			$msg = "The image is height is to large, it maximum is {$height}px.";
		}
		else if (!is_uploaded_file($_FILES[$form_field]['tmp_name'])) {		// check file has been uploaded
			$msg = "There has been a problem uploading your file.<br/>Please make sure the file is smaller than " . DISPLAY_IMG_FILE_SIZE;
		}
		else if (!move_uploaded_file($_FILES[$form_field]['tmp_name'], $path . $filename)) {		// check if upload was successful
			$msg = "There has been a problem uploading your file.";
		}
		else {
			chmod($path . $filename, 0644);
		}
	
		return $msg;
	}
	
	function delete_image($src) {
		$path = get_image_path();
		
		if (is_file($path . $src)) {
			unlink($path . $src);
		}
	}
?>