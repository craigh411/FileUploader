<?php
namespace Humps\FileUploader\Tests;

use Humps\FileUploader\FileUploader;
use Humps\FileUploader\Tests;

/**
 * Mock class to set some additional variables
 * Class MockUploader
 * @package FileUploader\Tests
 */
class TestUploader extends FileUploader{

	protected $uploadDir = 'uploads/';
	protected $maxFileSize = 10;
	protected $makeFilenameUnique = true;
	protected $overwrite = true;
	protected $createDirs = true;

	protected $blockedMimeTypes = [
		'application/x-msdownload'
	];



}