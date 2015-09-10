<?php
namespace FileUploader\Exceptions;

use Exception;

class NoOverwritePermissionException extends Exception{

	public function __construct($message=" File Already Exists! No Permission to overwrite. "){
		parent::__construct($message);
	}
}