<?php
//////////////////////////////////////////////////////////////////////////////////////
//
//	Name:		header_banner.php
//	Desc:		write out banner
//  Client:		LAD
//	Author:		Rob Curle
//	Date:		13 June 2005
//	Notes:
//
///////////////////////////////////////////////////////////////////////////////////////

	if ($banner_dir = opendir('media/bnrs')) {
		while (false !== ($file = readdir($banner_dir))) {
			if ($file != "." && $file != ".." && $file != ".DS_Store") {
				$banners[] = $file;
			}
		}
		closedir($banner_dir);
		$choice = rand(0, (count($banners)-1));

		echo "<img src=\"media/bnrs/" . $banners[$choice] . "\" width=\"570\" height=\"100\"  border=\"0\" usemap=\"#Map\" />";
	}
?>
