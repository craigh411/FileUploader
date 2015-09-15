<?php
namespace Humps\FileUploader\Tests;

use Humps\FileUploader\FileUploader;

class ImageUploader extends FileUploader{

	protected $allowedMimeTypes = [
		'image/jpg',
		'image/png',
		'image/gif'
	];

}