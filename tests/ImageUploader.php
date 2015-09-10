<?php
namespace FileUploader\Tests;

use FileUploader\FileUploader;

class ImageUploader extends FileUploader{

	protected $allowedMimeTypes = [
		'image/jpg',
		'image/png',
		'image/gif'
	];

}