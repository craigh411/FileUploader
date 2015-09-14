<?php
require_once '/../../vendor/autoload.php';
use FileUploader\FileUploader;

class ImageUploader extends FileUploader {

	protected $maxFileSize = 5e+6;
	protected $allowedMimeTypes = [
		'image/jpeg',
		'image/png',
		'image/gif'
	];
	protected $createDirs = true;
}