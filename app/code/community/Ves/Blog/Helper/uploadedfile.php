<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Blog Extension
 *
 * @category   Ves
 * @package    Ves_Blog
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
/**
* Handle file uploads via XMLHttpRequest
*/
class qqUploadedFileXhr {
/**
* Save the file to the specified path
* @return boolean TRUE on success
*/
function save($path) {
	$input = fopen("php://input", "r");
	$temp = tmpfile();
	$realSize = stream_copy_to_stream($input, $temp);
	fclose($input);

	if ($realSize != $this->getSize()){
		return false;
	}

	$target = fopen($path, “w”);
	fseek($temp, 0, SEEK_SET);
	stream_copy_to_stream($temp, $target);
	fclose($target);

	return true;
}
function getName() {
	return $_GET['qqfile'];
}
function getSize() {
	if (isset($_SERVER["CONTENT_LENGTH"])){
		return (int)$_SERVER["CONTENT_LENGTH"];
	} else {
		throw new Exception("Getting content length is not supported.");
	}
}
}

/**
* Handle file uploads via regular form post (uses the $_FILES array)
*/
class qqUploadedFileForm {
/**
* Save the file to the specified path
* @return boolean TRUE on success
*/
function save($path) {
	if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
		return false;
	}
	return true;
}
function getName() {
	return $_FILES['qqfile']['name'];
}
function getSize() {
	return $_FILES['qqfile']['size'];
}
}