<?php
namespace FileUploader;

use Exception;
use FileUploader\Contracts\Uploader;
use FileUploader\Exceptions\DirectoryNotFoundException;
use FileUploader\Exceptions\FileSizeTooLargeException;
use FileUploader\Exceptions\InvalidFileTypeException;
use FileUploader\Exceptions\NoOverwritePermissionException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Allows file uploads via web form
 *
 * Class FileUploader
 * @package FileUploader
 */
class FileUploader implements Uploader {

	private $filename;
	protected $uploadPath;
	protected $allowedMimeTypes = [];
	protected $blockedMimeTypes = [];
	protected $maxFileSize = 1000000;
	protected $makeFilenameUnique = false;
	protected $overwrite = false;
	protected $createDirIfNotExists = false;
	/**
	 * @var UploadedFile
	 */
	private $file;

	function __construct(UploadedFile $file, $path = '/')
	{
		$this->uploadPath($path);
		$this->file($file);

		return $this;
	}

	/**
	 * Sets the uploaded file to be validated and moved
	 * @param UploadedFile $file
	 * @return $this
	 */
	public function file(UploadedFile $file)
	{
		$this->file = $file;
		$this->filename = $file->getClientOriginalName();
		$this->sanitizeFilename();

		return $this;
	}

	/**
	 * Removes any unsafe characters from the filename. Replaces any spaces with an underscore (_)
	 * @return FileUploader
	 */
	public function sanitizeFilename()
	{
		// Regex for replacing special chars
		$filename = $this->filename;
		$filename = preg_replace('/\s+/', '_', $filename);
		$filename = preg_replace('/[^a-z1-9\.\-\_]/i', '', $filename);
		// removes any dots except the last one.
		$filename = preg_replace('/\.(?=.*?\.)/', '', $filename);
		$this->filename = $filename;

		return $this;
	}

	/**
	 * Validates and moves the uploaded file to it's destination
	 * @return String
	 */
	public function move()
	{
		// Make sure the filename is unique if makeFilenameUnique is set to true
		if($this->makeFilenameUnique)
		{
			$this->getUniqueFilename();
		}
		if($this->_validate())
		{
			// Validation passed so create any directories and move the tmp file to the specified location.
			$this->_createDirs();
			// This will also perform some validations on the upload.
			$this->file->move($this->uploadPath, $this->filename);
		}

		return $this->getUploadPath();
	}

	/**
	 * Alias for the move method
	 * @return String
	 */
	public function upload()
	{
		return $this->move();
	}

	/**
	 * Returns a unique file name for the upload by appending a sequential number. Checks to make sure file doesn't exist before returning a name.
	 * @return String
	 */
	public function getUniqueFilename()
	{
		list($filename, $extension) = explode(".", $this->filename);
		$increment = 1;
		while($this->fileExists($filename . "_" . $increment, $extension))
		{
			$increment++;
		}
		$this->filename = $filename . "_" . $increment . '.' . $extension;

		return $this;
	}

	/**
	 * Validates the upload against the specified options.
	 * @return bool
	 * @throws DirectoryNotFoundException
	 * @throws FileSizeTooLargeException
	 * @throws InvalidFileTypeException
	 * @throws NoOverwritePermissionException
	 */
	private function _validate()
	{
		$this->checkOverwritePermission();
		$this->checkHasValidUploadDirectory();
		$this->checkFileSize();
		$this->checkFileTypeIsAllowed();
		$this->checkFileTypeIsNotBlocked();

		return true;
	}

	/**
	 * Recursively creates directories for the specified path if they do not exist.
	 * @return void
	 */
	private function _createDirs()
	{
		if(! is_dir($this->uploadPath))
		{
			mkdir($this->uploadPath, 0777, true);
		}
	}

	/**
	 * Returns the path of the uploaded file
	 * @return String
	 */
	public function getUploadPath()
	{
		return $this->uploadPath . $this->filename;
	}

	/**
	 * Sets the upload path, second parameter can be passed to create directory if it doesn't exist
	 * @param string $path
	 * @return FileUploader
	 */
	public function uploadPath($path)
	{
		$this->uploadPath = $path;
		if(strlen($this->uploadPath) > 0 && ! preg_match('/\/$/', $this->uploadPath))
		{
			$this->uploadPath .= "/";
		}

		return $this;
	}

	/**
	 * Checks whether the specified file exists.
	 * @param $filename
	 * @param $extension
	 * @return bool
	 */
	private function fileExists($filename, $extension)
	{
		return is_file($this->uploadPath . $filename . "." . $extension);
	}

	/**
	 * Checks that a file with the same name doesn't exist or that it has permission to overwrite
	 * @throws NoOverwritePermissionException
	 */
	private function checkOverwritePermission()
	{
		if(! $this->overwrite && is_file($this->uploadPath . $this->filename))
		{
			throw new NoOverwritePermissionException;
		}
	}

	/**
	 * Checks that the upload directory exists or has permission to be created
	 * @throws DirectoryNotFoundException
	 */
	private function checkHasValidUploadDirectory()
	{
		if(! is_dir($this->uploadPath) && ! $this->createDirIfNotExists)
		{
			throw new DirectoryNotFoundException;
		}
	}

	/**
	 * Checks that the file size is not too large
	 * @throws FileSizeTooLargeException
	 */
	private function checkFileSize()
	{
		if($this->file->getSize() > $this->maxFileSize)
		{
			throw new FileSizeTooLargeException;
		}
	}

	/**
	 * Checks that the file type is allowed, if none specified then all non-blocked file types will be accepted
	 * @throws InvalidFileTypeException
	 */
	private function checkFileTypeIsAllowed()
	{
		if(count($this->allowedMimeTypes) > 0)
		{
			if(! in_array($this->file->getMimeType(), $this->allowedMimeTypes))
			{
				throw new InvalidFileTypeException("Invalid File Type: " . $this->file->getMimeType() . " has not been allowed");
			}
		}
	}

	/**
	 * Validate blocked MIME types, this can be used instead of allowed_mime_types,
	 * to block uploads of specific file types.
	 *
	 * @throws InvalidFileTypeException
	 */
	private function checkFileTypeIsNotBlocked()
	{
		if(in_array($this->file->getMimeType(), $this->blockedMimeTypes))
		{
			throw new InvalidFileTypeException("Invalid File Type: " . $this->file->getMimeType() . " type has been blocked");
		}
	}

	/**
	 * Defines whether an uploaded file can overwrite a file with the same name (false by default)
	 * @param bool $overwrite
	 * @return FileUploader
	 */
	public function overwrite($overwrite)
	{
		$this->overwrite = $overwrite;

		return $this;
	}

	/**
	 * Returns true if a file can overwrite a file with the same name
	 * @return bool
	 */
	public function canOverwrite()
	{
		return $this->overwrite;
	}

	/**
	 * Returns the array of allowed MIME Types
	 * @return array
	 */
	public function getAllowedMimeTypes()
	{
		return $this->allowedMimeTypes;
	}

	/**
	 * Accepts an array of allowed MIME Types
	 * @param array $allowedMimeTypes
	 * @return FileUploader
	 */
	public function allowedMimeTypes($allowedMimeTypes)
	{
		$this->allowedMimeTypes = $allowedMimeTypes;

		return $this;
	}

	/**
	 * Gets the array of blocked MIME Types
	 * @return array
	 */
	public function getBlockedMimeTypes()
	{
		return $this->blockedMimeTypes;
	}

	/**
	 * Accepts an array of mime types to block.
	 * Blocking occurs after allowing, so blocked types will take precedence if they appear in both lists.
	 * @param array $blockedMimeTypes
	 * @return FileUploader
	 */
	public function blockedMimeTypes($blockedMimeTypes)
	{
		$this->blockedMimeTypes = $blockedMimeTypes;

		return $this;
	}

	/**
	 * Returns the maximum filesize allowed
	 * @return number
	 */
	public function getMaxFileSize()
	{
		return $this->maxFileSize;
	}

	/**
	 *  Sets the maximum file size allowed $unit can be B = bytes, KB = Kilobytes, MB = Megabytes
	 * or the words themselves
	 * @param int $size
	 * @param string $unit
	 * @throws Exception
	 * @return FileUploader
	 */
	public function maxFileSize($size, $unit = "B")
	{
		$unit = strtoupper($unit);
		if(! is_numeric($size))
		{
			throw new Exception("Invalid file size: expects integer");
		}
		if($unit === "B" || preg_match("/^BYTE(S)?$/", $unit))
		{
			$this->maxFileSize = $size;
		}
		elseif($unit === "KB" || preg_match("/^KILOBYTE(S)?$/", $unit))
		{
			$this->maxFileSize = ($size * 1000);
		}
		elseif($unit === "MB" || preg_match("/^MEGABYTE(S)?$/", $unit))
		{
			$this->maxFileSize = ($size * 1000000);
		}
		else
		{
			throw new Exception("Invalid unit in setMaxFileSize: Expects 'B', 'KB' or 'MB'.");
		}

		return $this;
	}

	/**
	 * Defines whether a directory should be created if it doesn't exist.
	 * @param bool $createDir
	 * @return FileUploader
	 */
	public function createDirs($createDir)
	{
		$this->createDirIfNotExists = $createDir;

		return $this;
	}

	/**
	 * Returns the value of createDirIfNotExists for directory creation.
	 * @return bool
	 */
	public function canCreateDirs()
	{
		return $this->createDirIfNotExists;
	}

	/**
	 * If set to true this will make sure the file name is unique
	 * @param bool $makeUnique
	 * @return FileUploader
	 */
	public function makeFilenameUnique($makeUnique)
	{
		$this->makeFilenameUnique = $makeUnique;

		return $this;
	}

	/**
	 * Returns the value of $makeFilenameUnique
	 * @return bool
	 */
	public function getMakeFilenameUnique()
	{
		return $this->makeFilenameUnique;
	}

	/**
	 * returns the file details
	 * @return UploadedFile
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * Returns the filename
	 * @return string
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * Sets the output filename.
	 * A second boolean parameter can be passed if you do not want to make the filename safe.
	 * @param $filename
	 * @return FileUploader
	 */
	public function filename($filename)
	{
		$this->filename = $filename;

		return $this;
	}
}