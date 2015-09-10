<?php
namespace FileUploader\Contracts;

use Exception;
use FileUploader\File;

/**
 * Allows file uploads via web form
 *
 * Class FileUploader
 * @package FileUploader
 */
interface Uploader {

	/**
	 * Uploads the file
	 * @throws Exception
	 * @return String
	 */
	public function uploadFile();

	/**
	 * Sets the upload path, second parameter can be passed to create directory if it doesn't exist
	 * @param string $path
	 * @param bool $createDirIfNotExists
	 * @return void
	 */
	public function setPath($path);

	/**
	 * Defines whether an uploaded file can overwrite a file with the same name (false by default)
	 * @param bool $overwrite
	 * @return void
	 */
	public function overwrite($overwrite);

	/**
	 * Returns true if a file can overwrite a file with the same name
	 * @return bool
	 */
	public function canOverwrite();

	/**
	 * Accepts an array of allowed MIME Types
	 * @param array $allowedMimeTypes
	 * @return void
	 */
	public function setAllowedMimeTypes($allowedMimeTypes);

	/**
	 * Returns the array of allowed MIME Types
	 * @return array
	 */
	public function getAllowedMimeTypes();

	/**
	 * Accepts an array of mime types to block.
	 * Blocking occurs after allowing, so blocked types will take precedence if they appear in both lists.
	 * @param array $blockedMimeTypes
	 * @return void
	 */
	public function setBlockedMimeTypes($blockedMimeTypes);

	/**
	 * Gets the array of blocked MIME Types
	 * @return array
	 */
	public function getBlockedMimeTypes();

	/**
	 *  Sets the maximum file size allowed $unit can be B = bytes, KB = Kilobytes, MB = Megabytes
	 * @param int $size
	 * @param string $unit
	 * @throws Exception
	 * @return void
	 */
	public function setMaxFileSize($size, $unit);

	public function getMaxFileSize();

	/**
	 * Defines whether a directory should be created if it doesn't exist
	 * @param bool $createDir
	 * @return void
	 */
	public function createDirIfNotExists($createDir);

	/**
	 * Returns the value of createDirIfNotExists for directory creation.
	 * @return bool
	 */
	public function canCreateDirIfNotExists();

	/**
	 * If set to true this will make sure the file name is unique
	 * @param bool $makeUnique
	 * @return void
	 */
	public function makeFilenameUnique($makeUnique);

	public function getMakeFileNameUnique();


	/**
	 * Used to set the uploaded file. Accepts a php $_FILE request.
	 * @param $file
	 * @return void
	 */
	public function setFile(File $file);

	/**
	 * returns the file details
	 * @return array
	 */
	public function getFile();

	public function setFilename($filename);

	public function getFilename();

	/**
	 * Returns the path of the uploaded file
	 * @return String
	 */
	public function getPath();

	/**
	 * Returns a unique file name for the upload.
	 * @return String
	 */
	public function createUniqueFilename();

	/**
	 * Makes the given filename safe for uploading
	 * @return string
	 */
	public function makeFilenameSafe();
}