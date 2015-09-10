<?php
namespace FileUploader\Tests;

use FileUploader\File;
use FileUploader\FileUploader;
use PHPUnit_Framework_TestCase;

class FileUploaderTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var FileUploader $uploader
	 */
	protected $uploader;

	protected function setUp()
	{
		$this->uploader = new FileUploader($this->getFile());
	}

	protected function getFile()
	{
		return new File('test.txt', 100, 'text/plain', 'tmp_name');
	}

	/**
	 * @test
	 */
	public function it_sets_the_name_of_the_file_to_be_uploaded()
	{
		$file = new File('testFile.jpg', 100, 'image/jpeg', 'tmp_name');
		$this->uploader->setFile($file);
		$this->assertEquals('testFile.jpg', $this->uploader->getFile()->getFilename());
	}

	/**
	 * @test
	 */
	public function it_sets_the_size_of_the_file_to_be_uploaded()
	{
		$file = new File('testFile.jpg', 20000, 'images/jpeg', 'tmp_name');
		$this->uploader->setFile($file);
		$this->assertEquals(20000, $this->uploader->getFile()->getSize());
	}

	/**
	 * @test
	 */
	public function it_sets_the_mime_type_of_the_file_to_be_uploaded()
	{
		$file = new File('testFile.jpg', '2MB', 'image/png', 'tmp_name');
		$this->uploader->setFile($file);
		$this->assertEquals('image/png', $this->uploader->getFile()->getType());
	}

	/**
	 * @test
	 */
	public function it_sets_the_tmp_name_of_the_file_to_be_uploaded()
	{
		$file = new File('testFile.jpg', '2MB', 'image/png', 'tmp_name_test');
		$this->uploader->setFile($file);
		$this->assertEquals('tmp_name_test', $this->uploader->getFile()->getTmpName());
	}

	/**
	 * @test
	 */
	public function it_returns_an_instance_of_file()
	{
		$uploadedFile = ['name' => '', 'type' => '', 'size' => '', 'tmp_name' => ''];
		$file = File::getInstance($uploadedFile);
		$this->assertInstanceOf('FileUploader\File', $file);
	}

	/**
	 * @test
	 */
	public function it_replaces_spaces_with_underscores()
	{
		$filename = 'my test txt file.txt';
		$this->uploader->setFilename($filename);
		$this->uploader->makeFilenameSafe();
		$this->assertEquals('my_test_txt_file.txt', $this->uploader->getFilename());
	}

	/**
	 * @test
	 */
	public function it_removes_all_non_safe_characters()
	{
		$filename = 'my $&?test_txt.\@*" file 1.txt';
		$this->uploader->setFilename($filename);
		$this->uploader->makeFilenameSafe();
		$this->assertEquals('my_test_txt_file_1.txt', $this->uploader->getFilename());
	}

	/**
	 * @test
	 */
	public function it_removes_all_dots_except_the_one_for_the_extension()
	{
		$filename = 'my... test_txt. file. 1..txt';
		$this->uploader->setFilename($filename);
		$this->uploader->makeFilenameSafe();
		$this->assertEquals('my_test_txt_file_1.txt', $this->uploader->getFilename());
	}

	/**
	 * @test
	 */
	public function it_sets_allowed_file_types()
	{
		$allow = ['image/jpg', 'image/png'];
		$this->uploader->setAllowedMimeTypes($allow);
		$allowed = $this->uploader->getAllowedMimeTypes();
		$this->assertEquals('image/jpg', $allowed[0]);
		$this->assertEquals('image/png', $allowed[1]);
	}

	/**
	 * @test
	 */
	public function it_allows_file_types_to_be_set_from_child_class()
	{
		$uploader = new ImageUploader($this->getFile());
		$allowed = $uploader->getAllowedMimeTypes();
		$this->assertEquals('image/jpg', $allowed[0]);
		$this->assertEquals('image/png', $allowed[1]);
		$this->assertEquals('image/gif', $allowed[2]);
	}

	/**
	 * @test
	 */
	public function it_allows_protected_variables_to_be_set_from_child_class()
	{
		$uploader = new TestUploader($this->getFile());
		$maxFileSize = $uploader->getMaxFileSize();
		$makeFilenameUnique = $uploader->getMakeFilenameUnique();
		$overwrite = $uploader->canOverwrite();
		$createDirIfNotExists = $uploader->canCreateDirIfNotExists();
		$blockedMimeTypes = $uploader->getBlockedMimeTypes();
		$this->assertEquals(10, $maxFileSize);
		$this->assertTrue($makeFilenameUnique);
		$this->assertTrue($overwrite);
		$this->assertTrue($createDirIfNotExists);
		$this->assertEquals('application/x-msdownload', $blockedMimeTypes[0]);
	}

	/**
	 * @test
	 */
	public function it_throws_NoOverwritePermissionException_on_upload()
	{
		$this->setExpectedException('FileUploader\Exceptions\NoOverwritePermissionException');
		$this->uploader->setFilename('test.txt');
		$this->uploader->setPath('files/');
		$this->uploader->uploadFile();
	}

	/**
	 * @test
	 */
	public function it_throws_DirectoryNotFoundException_on_upload()
	{
		$this->setExpectedException('FileUploader\Exceptions\DirectoryNotFoundException');
		$this->uploader->setFilename('test.txt');
		$this->uploader->setPath('files/' . rand() . '/');
		$this->uploader->uploadFile();
	}

	/**
	 * @test
	 */
	public function it_throws_FileSizeTooLargeException_on_upload()
	{
		$this->setExpectedException('FileUploader\Exceptions\FileSizeTooLargeException');
		$this->uploader->setFilename(rand() . '.txt');
		$this->uploader->setPath('files/');
		$this->uploader->setMaxFileSize(1);
		$this->uploader->uploadFile();
	}

	/**
	 * @test
	 */
	public function it_throws_a_not_allowed_InvalidFileTypeException_on_upload()
	{
		$this->setExpectedException('FileUploader\Exceptions\InvalidFileTypeException', "Invalid File Type: text/plain has not been allowed");
		$this->uploader->setFilename(rand() . '.txt');
		$this->uploader->setPath('files/');
		$this->uploader->setAllowedMimeTypes(['image/jpeg']);
		$this->uploader->uploadFile();
	}

	/**
	 * @test
	 */
	public function it_throws_a_blocked_allowed_InvalidFileTypeException_on_upload()
	{
		$this->setExpectedException('FileUploader\Exceptions\InvalidFileTypeException', "Invalid File Type: text/plain type has been blocked");
		$this->uploader->setFilename(rand() . '.txt');
		$this->uploader->setPath('files/');
		$this->uploader->setBlockedMimeTypes(['text/plain']);
		$this->uploader->uploadFile();
	}

	/**
	 * @test
	 */
	public function it_validates_the_upload()
	{
		$file = rand() . '.txt';
		$this->uploader->setFilename($file);
		$this->uploader->setPath('files/');
		$this->uploader->setAllowedMimeTypes(['text/plain']);
		$this->assertEquals('files/' . $file, $this->uploader->uploadFile());
	}

	/**
	 * @test
	 */
	public function it_creates_a_new_upload_directory()
	{
		$file = 'file.txt';
		$this->uploader->setFilename($file);
		$this->uploader->setPath('files/new');
		$this->uploader->createDirIfNotExists(true);
		$this->uploader->setAllowedMimeTypes(['text/plain']);
		$this->uploader->uploadFile();
		$this->assertTrue(is_dir('files/new'));
		rmdir('files/new');
	}

	/**
	 * @test
	 */
	public function it_creates_a_unique_filename_for_upload()
	{
		$file = 'test.txt';
		$this->uploader->setFilename($file);
		$this->uploader->setPath('files');
		$this->uploader->makeFilenameUnique(true);
		$this->uploader->setAllowedMimeTypes(['text/plain']);
		$this->assertEquals('files/test_1.txt', $this->uploader->uploadFile());
	}

	/**
	 * @test
	 */
	public function it_sets_valid_max_file_sizes()
	{
		$this->uploader->setMaxFileSize(10000, 'B');
		$this->assertEquals(10000, $this->uploader->getMaxFileSize());
		$this->uploader->setMaxFileSize(100, 'KB');
		$this->assertEquals(1e+5, $this->uploader->getMaxFileSize());
		$this->uploader->setMaxFileSize(1, 'MB');
		$this->assertEquals(1e+6, $this->uploader->getMaxFileSize());
	}

	/**
	 * @test
	 */
	public function it_sets_an_invalid_file_size()
	{
		$this->setExpectedException('Exception');
		$this->uploader->setMaxFileSize('one', 'B');
	}

	/**
	 * @test
	 */
	public function it_sets_an_invalid_file_size_unit()
	{
		$this->setExpectedException('Exception');
		$this->uploader->setMaxFileSize(1, 'GB');
	}

	/**
	 * @test
	 */
	public function it_sets_overwrite_to_true()
	{
		$this->uploader->overwrite(true);
		$this->assertTrue($this->uploader->canOverwrite());
	}

	/**
	 * @test
	 */
	public function it_proves_that_blocked_mime_types_take_precedence()
	{
		$this->setExpectedException('FileUploader\Exceptions\InvalidFileTypeException', "Invalid File Type: text/plain type has been blocked");
		$this->uploader->setFilename(rand() . '.txt');
		$this->uploader->setPath('files/');
		$this->uploader->setBlockedMimeTypes(['text/plain']);
		$this->uploader->setAllowedMimeTypes(['text/plain','image/jpeg']);
		$this->uploader->uploadFile();
	}
}