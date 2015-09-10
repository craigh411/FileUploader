<?php
namespace FileUploader\Exceptions;

use Exception;

class DirectoryNotFoundException extends Exception{

	public function __construct($message=" Unable to Find Upload Directory "){
		parent::__construct($message);
	}

}