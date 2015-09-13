<?php
namespace FileUploader;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class File {

	/**
	 * Returns an Uploaded file instance from the $_FILE global
	 * @param $file
	 * @return UploadedFile
	 */
	public static function getUploadedFile($file)
	{
		return new UploadedFile($file['tmp_name'], $file['name'], $file['type'], $file['size']);
	}
}