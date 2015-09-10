<?php
namespace FileUploader\Exceptions;

use Exception;

class InvalidFileTypeException extends Exception{
	public function __construct($message=" Invalid File Type "){
		parent::__construct($message);
	}
}