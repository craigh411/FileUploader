# FileUploader

[![Build Status](https://travis-ci.org/craigh411/FileUploader.svg?branch=master)](https://travis-ci.org/craigh411/FileUploader)

An easily configurable file uploader for uploading files via a web form

## Features

- Set accepted file types
- Block unwanted file types
- Set maximum file size
- Option to auto create upload directories if they do not exist
- Option to overwrite files
- Option to automatically create unique filenames
- Filename sanitisation

## Installing

The easiest way to install is via Composer:

`composer require craigh/file-uploader`

or you can add `craigh/file-uploader` to your composer.json file and run `composer update`


**Note:** The FileUploader requires [`symfony/HttpFoundation`](https://github.com/symfony/HttpFoundation), so if you are not using composer you will need to
make sure you have this library in your include path.

## Usage

The FileUploader requires a `Symfony\Component\HttpFoundation\File\UploadedFile` object. You can easily retrieve an instance by passing the $_FILES['Your_field_name'] variable into the getUploadedFile() method
on the `Humps\FileUploader\File` class:

`$file = Humps\FileUploader\File::getUploadedFile($_FILES['file']);`

This can then be passed in to the FileUploader:

`$uploader = new Humps\FileUploader\FileUploader($file);`

You can then upload the file as follows:

`$uploader->upload();`

## Options

##### uploadDir(string)
Sets the upload path. It will also append any required '/' if it is not set, so both 'path/to/dir' and 'path/to/dir/' will work (defaults to current directory)

`$uploader->uploadDir('path/to/dir');`

##### overwrite(boolean)
Set to true to allow overwriting of files with the same name (default: false)

`$uploader->overwrite(true);`

##### allowedMimeTypes(array) 
Pass in an array of allowed mime types, everything else will be blocked. When empty all file types will be allowed unless
explicitly blocked.

`$uploader->allowedMimeTypes(['image/jpeg,'image/png', 'image/gif']);`

##### blockedMimeTypes(array)
You can also block file types if you prefer. Pass in an array of mime types you want to block

`$uploader->blockedMimeTypes(['application/x-msdownload']);`


##### maxFileSize($size, $unit)
The maximum file size you want to allow, expects size to be a number and unit to be either:
- B - Byte
- KB - Kilobyte
- MB - Megabyte

`$uploader->maxFileSize(5, 'MB');`

You can also use the words BYTE, BYTES, KILOBYTE, KILOBYTES, MEGABYTE or MEGABYTES if you prefer:

`$uploader->maxFileSize(1, 'MEGABYTE');`

##### createDirs(bool)
If set to true this will recursively create any specified directories if they do not exist (default: false)

`$uploader->createDirs(true);`

##### makeFilenameUnique(bool)
If set to true this will make the filename unique by appending a _{number} to the end.

`$uploader->makeFilenameUnique(true);`

##### filename(string)
By default the filename will be a sanitised version of the uploaded filename. Use this method if you want to set your own filename.

`$uploader->filename('myFile.txt');`

**Note:** When using this method the filename will not be sanatised, if you want to sanatise the filename you can use the sanitizeFilename() method.

##### sanitizeFilename()

Sanitises the given filename by removing any non alpha numeric characters and replacing any spaces with an underscore. You will only need to call this if you want to set your own filenames using the filename() method, otherwise this method is called automatically. You should also be aware that this call will need to be made after you set your filename:

```
$uploader->filename('my%$crazy@filename.txt')->sanitizeFilename();
```

##### upload() 
Uploads the file and returns the upload path.

`$uploadPath = $uploader->upload();`

upload() is an alias of move(), so you can also use the move() method if you feel it's wording is more appropriate:

`$uploadPath = $uploader->move();`

## Chaining

All methods above can be applied in a chain for a clean syntax:

```
use Humps\FileUploader\File;
use Humps\FileUploader\FileUploader;

$file = File::getUploadedFile($_FILE['file']);
$uploader = new FileUploader($file);
$uploader->uploadPath->('files')->overwrite(true)->upload();
```

or even

```
use Humps\FileUploader\File;
use Humps\FileUploader\FileUploader;

$file = File::getUploadedFile($_FILE['file']);
$uploader = (new FileUploader($file))->upload();
```

## Config by Extending the FileUploader Class

For a cleaner way to configure you uploads you can extend the FileUploader class which will give you access to the protected
variables, e.g.:

```
use Humps\FileUploader\FileUploader;

class ImageUploader extends FileUploader{

	protected $allowedMimeTypes = [
		'image/jpeg',
		'image/png',
		'image/gif'
	];
  
	protected $maxFileSize = 5e+6; // In bytes (this is 5MB or 5000000 bytes)
	protected $makeFilenameUnique = true;
	protected $createDirIfNotExists = true;
}
```

This can then be used as follows:

```
use Humps\FileUploader\File;

$image = File::getUploadedFile($_FILE['image']);
$uploader = new ImageUploader($image);
$image->upload();
```

The following variables are protected and so can be set by child classes:

```
protected $uploadDir; // Upload directory
protected $allowedMimeTypes = []; // Only allow these file to be uploaded
protected $blockedMimeTypes = []; // Don't allow these files to be uploaded
protected $maxFileSize = 1000000; // In bytes
protected $makeFilenameUnique = false; // Make the filename unique if two files have the same name
protected $overwrite = false; // Allow overwriting of files with the same name
protected $createDirs = false; // Allow the automatic creation of any upload directories
```

## A note on the examples

If you want to run the examples then you will need to change the include path to point to your autoloader or include the FileUploader classes manually.



