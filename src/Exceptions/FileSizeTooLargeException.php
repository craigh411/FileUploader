<?php
namespace Humps\FileUploader\Exceptions;

use Exception;

class FileSizeTooLargeException extends Exception{
	public function __construct($message=" File Size Too Large "){
		parent::__construct($message);
	}
}