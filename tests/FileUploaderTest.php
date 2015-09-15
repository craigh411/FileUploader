<?php
namespace Humps\FileUploader\Tests;

use Humps\FileUploader\File;
use Humps\FileUploader\FileUploader;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var FileUploader $uploader
	 */
	protected $uploader;

	protected function setUp()
	{
		$handle = fopen('tmp/tmp.txt','w+');
		fwrite($handle,'');
		fclose($handle);

		$this->uploader = new FileUploader($this->getFile());
	}


	protected function getFile($tmp = 'tmp/tmp.txt', $file = 'test.txt')
	{
		// return an UploadedFile object in test mode
		return new UploadedFile($tmp, $file, null, null, null, true);
	}

	/**
	 * @test
	 */
	public function it_sets_the_name_of_the_file_to_be_uploaded()
	{
		$file = $this->getFile();
		$this->uploader->file($file);
		$this->assertEquals('test.txt', $this->uploader->getFile()->getClientOriginalName());
	}

	/**
	 * @test
	 */
	public function it_sets_the_size_of_the_file_to_be_uploaded()
	{
		$file = $this->getFile();
		$this->uploader->file($file);
		$this->assertEquals(0, $this->uploader->getFile()->getSize());
	}

	/**
	 * @test
	 */
	public function it_sets_the_mime_type_of_the_file_to_be_uploaded()
	{
		$file = $this->getFile();
		$this->uploader->file($file);
		$this->assertEquals('file', $this->uploader->getFile()->getType());
	}

	/**
	 * @test
	 */
	public function it_sets_the_tmp_name_of_the_file_to_be_uploaded()
	{
		$file = $this->getFile();
		$this->uploader->file($file);
		$this->assertEquals('tmp', $this->uploader->getFile()->getPath());
	}

	/**
	 * @test
	 */
	public function it_returns_an_instance_of_UploadedFile()
	{
		$uploadedFile = ['name' => 'test.txt', 'type' => 'file', 'size' => '0', 'tmp_name' => 'tmp/tmp.txt'];
		$file = File::getUploadedFile($uploadedFile);
		$this->assertInstanceOf('Symfony\Component\HttpFoundation\File\UploadedFile', $file);
	}

	/**
	 * @test
	 */
	public function it_replaces_spaces_with_underscores()
	{
		$filename = 'my test txt file.txt';
		$this->uploader->filename($filename);
		$this->uploader->sanitizeFilename();
		$this->assertEquals('my_test_txt_file.txt', $this->uploader->getFilename());
	}

	/**
	 * @test
	 */
	public function it_removes_all_non_safe_characters()
	{
		$filename = 'my $&?test_txt.\@*" file 1.txt';
		$this->uploader->filename($filename);
		$this->uploader->sanitizeFilename();
		$this->assertEquals('my_test_txt_file_1.txt', $this->uploader->getFilename());
	}

	/**
	 * @test
	 */
	public function it_removes_all_dots_except_the_one_for_the_extension()
	{
		$filename = 'my... test_txt. file. 1..txt';
		$this->uploader->filename($filename);
		$this->uploader->sanitizeFilename();
		$this->assertEquals('my_test_txt_file_1.txt', $this->uploader->getFilename());
	}

	/**
	 * @test
	 */
	public function it_sets_allowed_file_types()
	{
		$allow = ['image/jpg', 'image/png'];
		$this->uploader->allowedMimeTypes($allow);
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
		$uploadDir = $uploader->getUploadDir();
		$maxFileSize = $uploader->getMaxFileSize();
		$makeFilenameUnique = $uploader->getMakeFilenameUnique();
		$overwrite = $uploader->canOverwrite();
		$createDirIfNotExists = $uploader->canCreateDirs();
		$blockedMimeTypes = $uploader->getBlockedMimeTypes();

		$this->assertEquals('uploads/', $uploadDir);
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
		$this->setExpectedException('Humps\FileUploader\Exceptions\NoOverwritePermissionException');
		$this->uploader->filename('test.txt');
		$this->uploader->uploadDir('files/');
		$this->uploader->upload();
	}

	/**
	 * @test
	 */
	public function it_throws_DirectoryNotFoundException_on_upload()
	{
		$this->setExpectedException('Humps\FileUploader\Exceptions\DirectoryNotFoundException');
		$this->uploader->filename('test.txt');
		$this->uploader->uploadDir('files/' . rand() . '/');
		$this->uploader->upload();
	}

	/**
	 * @test
	 */
	public function it_throws_FileSizeTooLargeException_on_upload()
	{
		$this->setExpectedException('Humps\FileUploader\Exceptions\FileSizeTooLargeException');
		$uploader = new FileUploader($this->getFile('tmp/large_tmp.txt', 'large_test.txt'));
		$uploader->uploadDir('files/');
		$uploader->maxFileSize(1);
		$uploader->upload();
	}

	/**
	 * @test
	 */
	public function it_throws_a_not_allowed_InvalidFileTypeException_on_upload()
	{
		$this->setExpectedException('Humps\FileUploader\Exceptions\InvalidFileTypeException', "Invalid File Type: inode/x-empty has not been allowed");
		$this->uploader->filename(rand() . '.txt');
		$this->uploader->uploadDir('files/');
		$this->uploader->allowedMimeTypes(['image/jpeg']);
		$this->uploader->upload();
	}

	/**
	 * @test
	 */
	public function it_throws_a_blocked_allowed_InvalidFileTypeException_on_upload()
	{
		$this->setExpectedException('Humps\FileUploader\Exceptions\InvalidFileTypeException', "Invalid File Type: inode/x-empty type has been blocked");
		$this->uploader->filename(rand() . '.txt');
		$this->uploader->uploadDir('files/');
		$this->uploader->blockedMimeTypes(['inode/x-empty']);
		$this->uploader->upload();
	}


	/**
	 * @test
	 */
	public function it_creates_a_new_upload_directory()
	{
		$file = 'file.txt';
		$this->uploader->filename($file);
		$this->uploader->uploadDir('files/new');
		$this->uploader->createDirs(true);
		$this->uploader->allowedMimeTypes(['inode/x-empty']);
		$this->uploader->upload();
		$this->assertTrue(is_dir('files/new'));
		unlink('files/new/file.txt');
		rmdir('files/new');
	}

	/**
	 * @test
	 */
	public function it_creates_a_unique_filename_for_upload()
	{
		$file = 'test.txt';
		$this->uploader->filename($file);
		$this->uploader->uploadDir('files');
		$this->uploader->makeFilenameUnique(true);
		$this->uploader->allowedMimeTypes(['inode/x-empty']);
		$this->assertEquals('files/test_1.txt', $this->uploader->upload());
		unlink('files/test_1.txt');
	}

	/**
	 * @test
	 */
	public function it_sets_valid_max_file_sizes()
	{
		$this->uploader->maxFileSize(10000, 'B');
		$this->assertEquals(10000, $this->uploader->getMaxFileSize());
		$this->uploader->maxFileSize(10000, 'BYTE');
		$this->assertEquals(10000, $this->uploader->getMaxFileSize());
		$this->uploader->maxFileSize(10000, 'BYTES');
		$this->assertEquals(10000, $this->uploader->getMaxFileSize());
		$this->uploader->maxFileSize(100, 'KB');
		$this->assertEquals(1e+5, $this->uploader->getMaxFileSize());
		$this->uploader->maxFileSize(100, 'KILOBYTE');
		$this->assertEquals(1e+5, $this->uploader->getMaxFileSize());
		$this->uploader->maxFileSize(100, 'KILOBYTES');
		$this->assertEquals(1e+5, $this->uploader->getMaxFileSize());
		$this->uploader->maxFileSize(1, 'MEGABYTE');
		$this->assertEquals(1e+6, $this->uploader->getMaxFileSize());
		$this->uploader->maxFileSize(1, 'MEGABYTES');
		$this->assertEquals(1e+6, $this->uploader->getMaxFileSize());
		$this->uploader->maxFileSize(1, 'MB');
		$this->assertEquals(1e+6, $this->uploader->getMaxFileSize());
		$this->uploader->maxFileSize(1, 'megabyte');
		$this->assertEquals(1e+6, $this->uploader->getMaxFileSize());
	}

	/**
	 * @test
	 */
	public function it_sets_an_invalid_file_size()
	{
		$this->setExpectedException('Exception');
		$this->uploader->maxFileSize('one', 'B');
	}

	/**
	 * @test
	 */
	public function it_sets_an_invalid_file_size_unit()
	{
		$this->setExpectedException('Exception');
		$this->uploader->maxFileSize(1, 'GB');
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
		$this->setExpectedException('Humps\FileUploader\Exceptions\InvalidFileTypeException', "Invalid File Type: inode/x-empty type has been blocked");
		$this->uploader->filename(rand() . '.txt');
		$this->uploader->uploadDir('files/');
		$this->uploader->blockedMimeTypes(['text/plain', 'inode/x-empty']);
		$this->uploader->allowedMimeTypes(['inode/x-empty', 'image/jpeg']);
		$this->uploader->upload();
	}

	/**
	 * @test
	 */
	public function it_chains_options_together_and_returns_the_upload_path()
	{
		$upload = (new FileUploader($this->getFile()))
			->uploadDir('files/')
			->allowedMimeTypes(['inode/x-empty'])
			->overwrite(false)
			->makeFilenameUnique(true)
			->upload();
		$this->assertEquals('files/test_1.txt', $upload);
		unlink('files/test_1.txt');
	}

	/**
	 * @test
	 */
	public function it_appends_a_trailing_slash_to_upload_directory()
	{
		$this->uploader->uploadDir('uploads');
		$this->assertEquals('uploads/', $this->uploader->getUploadDir());

		$this->uploader->uploadDir('');
		$this->assertEquals('/', $this->uploader->getUploadDir());

		$this->uploader->uploadDir('uploads/');
		$this->assertEquals('uploads/', $this->uploader->getUploadDir());

	}

}