<?php
namespace FileUploader;

class File {

	private $filename;
	private $size;
	private $tmpName;
	private $type;

	/**
	 * @param $filename
	 * @param $size
	 * @param $type
	 * @param $tmpName
	 */
	public function __construct($filename, $size, $type, $tmpName)
	{
		$this->filename = $filename;
		$this->size = $size;
		$this->type = $type;
		$this->tmpName = $tmpName;
	}

	/**
	 * @return mixed
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * @param $filename
	 */
	public function setFilename($filename)
	{
		$this->filename = $filename;
	}

	/**
	 * @return mixed
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @param $size
	 */
	public function setSize($size)
	{
		$this->size = $size;
	}

	/**
	 * @return mixed
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getTmpName()
	{
		return $this->tmpName;
	}

	/**
	 * @param $tmpName
	 */
	public function setTmpName($tmpName)
	{
		$this->tmpName = $tmpName;
	}

	public static function getInstance($file){
		return new File($file['name'], $file['size'], $file['type'], $file['tmp_name']);
	}
}