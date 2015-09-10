<?php
use FileUploader\File;

require_once 'classes/ImageUploader.php';
try
{
	var_dump($_FILES['image']);

	$file = File::getInstance($_FILES['image']);
	$uploader = new ImageUploader($file);
	$uploader->setPath('images');
	$upload = $uploader->uploadFile();

} catch(Exception $e)
{
	echo 'Whoops! Something went wrong!<br /><br />';
	die($e->getMessage());
}
echo 'Image Uploaded Successfully: <a href="' . $upload . '">' . $upload . '</a>';



