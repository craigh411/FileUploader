<?php
namespace FileUploader\Tests;

use FileUploader\FileUploader;

/**
 * Mock class to set some additional variables
 * Class MockUploader
 * @package FileUploader\Tests
 */
class TestUploader extends FileUploader{

	protected $maxFileSize = 10;
	protected $makeFilenameUnique = true;
	protected $overwrite = true;
	protected $createDirIfNotExists = true;

	protected $blockedMimeTypes = [
		'application/x-msdownload'
	];



}