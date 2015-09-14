<?php
namespace FileUploader\Contracts;

use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Allows file uploads via web form
 *
 * Class Uploader
 * @package Uploader
 */
interface Uploader {

	/**
	 * Used to set the uploaded file.
	 * @param UploadedFile $file
	 * @return Uploader
	 */
	public function file(UploadedFile $file);

	/**
	 * Removes any unsafe characters from the filename. Replaces any spaces with an underscore (_)
	 * @return Uploader
	 */
	public function sanitizeFilename();

	/**
	 * Validates the file against the given rules and uploads the file
	 * @throws Exception
	 * @return String
	 */
	public function move();

	/**
	 * Alias of move()
	 * @return String
	 * @throws Exception
	 */
	public function upload();

	/**
	 * Returns a unique file name for the upload by appending a sequential number. Checks to make sure file doesn't exist before returning a name.
	 * @return String
	 */
	public function getUniqueFilename();

	/**
	 * Returns the path of the uploaded file
	 * @return String
	 */
	public function getUploadPath();

	/**
	 * Sets the upload path, second parameter can be passed to create directory if it doesn't exist
	 * @param string $path
	 * @return Uploader
	 */
	public function uploadPath($path);

	/**
	 * Defines whether an uploaded file can overwrite a file with the same name (false by default)
	 * @param bool $overwrite
	 * @return Uploader
	 */
	public function overwrite($overwrite);

	/**
	 * Returns true if a file can overwrite a file with the same name
	 * @return bool
	 */
	public function canOverwrite();

	/**
	 * Returns the array of allowed MIME Types
	 * @return array
	 */
	public function getAllowedMimeTypes();

	/**
	 * Accepts an array of allowed MIME Types
	 * @param array $allowedMimeTypes
	 * @return Uploader
	 */
	public function allowedMimeTypes($allowedMimeTypes);

	/**
	 * Gets the array of blocked MIME Types
	 * @return array
	 */
	public function getBlockedMimeTypes();

	/**
	 * Accepts an array of mime types to block.
	 * Blocking occurs after allowing, so blocked types will take precedence if they appear in both lists.
	 * @param array $blockedMimeTypes
	 * @return Uploader
	 */
	public function blockedMimeTypes($blockedMimeTypes);

	/**
	 * Returns the maximum file size allowed
	 * @return number
	 */
	public function getMaxFileSize();

	/**
	 *  Sets the maximum file size allowed $unit can be B = bytes, KB = Kilobytes, MB = Megabytes
	 * or the words themselves
	 * @param int $size
	 * @param string $unit
	 * @throws Exception
	 * @return Uploader
	 */
	public function maxFileSize($size, $unit = "B");

	/**
	 * Defines whether a directory should be created if it doesn't exist.
	 * @param bool $createDir
	 * @return Uploader
	 */
	public function createDirs($createDir);

	/**
	 * Returns the value of createDirIfNotExists for directory creation.
	 * @return bool
	 */
	public function canCreateDirs();

	/**
	 * If set to true this will make sure the file name is unique
	 * @param bool $makeUnique
	 * @return Uploader
	 */
	public function makeFilenameUnique($makeUnique);

	/**
	 * Returns the value of $makeFilenameUnique
	 * @return bool
	 */
	public function getMakeFilenameUnique();

	/**
	 * returns the file details
	 * @return UploadedFile
	 */
	public function getFile();

	/**
	 * Returns the filename
	 * @return string
	 */
	public function getFilename();

	/**
	 * Sets the output filename.
	 * A second boolean parameter can be passed if you do not want to make the filename safe.
	 * @param $filename
	 * @return Uploader
	 */
	public function filename($filename);
}