<?php
use FileUploader\File;

require_once 'classes/ImageUploader.php';
try
{
	$file = File::getUploadedFile($_FILES['image']);
	$uploader = new ImageUploader($file);
	$upload = $uploader->uploadPath('images')->upload();
} catch(Exception $e)
{
	echo 'Whoops! Something went wrong!<br /><br />';
	die($e->getMessage());
}
echo 'Image Uploaded Successfully: <a href="' . $upload . '">' . $upload . '</a>';



