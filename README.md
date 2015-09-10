# FileUploader

A easily configurable file uploader for uploading files via a web form

## Features

- Set accepted file types
- Block unwanted file types
- Set maximum file size
- Option to auto create upload directories if they do not exist
- Option to overwrite files
- Option to automatically create unique filenames
- Filename sanitisation

## Usage

The FileUploader accepts a FileUploader\File object. You can easily retrieve an instance by passing the $_FILES['Your_field_name'] variable into the getInstance() method:

`$file = FileUploader\File::getInstance($_FILES['file']);`

This can then be passed in to the FileUploader:

`$uploader = new FileUploader\FileUploader($file);`

You can then upload the file as follows:

`$uploader->uploadFile();`

## Options

##### setPath(string)
Sets the upload path. This can also be set via the second parameter on the constructor (defaults to current directory)

`$uploader->setPath('path\to\dir');`

##### overwrite(boolean)
Set to true to allow overwriting of files with the same name (default: false)

`$uploader->overwrite(true);`

##### setAllowedMimeTypes(array) 
Pass in an array of allowed mime types, everything else will be blocked. When empty all file types will be allowed unless
explicitely blocked.

`$uploader->setAllowedMimeTypes(['image\jpeg,'image\png', 'image\gif']);`

##### setBlockedMimeTypes(array)
You can also block file types if you prefer. Pass in an array of mime types you want to block

`$uploader->setBlockedMimeTypes(['application/x-msdownload']);`


#####setMaxFileSize($size, $unit)
The maximum file size you want to allow, expects size to be a number and unit to be either:
- B - Byte
- KB - Kilobyte
- MB - Megabyte

`$uploader->setMaxFileSize(5, 'MB');`

#####createDirIfNotExists(bool)
If set to true this will recursively create any specified directories if they do not exist (default: false)

`$uploader->createDirIfNotExists(true);`

##### makeFilenameUnique(bool)
If set to true this will make the filename unique by appending a _{number} to the end.

`$uploader->makeFilenameUnique(true);`

##### setFilename(string)
By default the filename will be a sanitised version of the uploaded filename. Use this method if you want to set your own filename.

`$uploader->setFilename('myFile.txt');`

**Note:** When using this method the filename will not be sanatised, if you want to sanatise the filename you can use the
makeFilenameSafe() method

##### makeFilenameSafe()
Sanitises the given filename by removing any dangerous characters and replaces any spaces with an underscore. You will only need to call this if you want to set your
own filenames using the setFilename() method. This method is called automatically if you do not want to set your own filename
You should also be aware that this call will beed to be made after you set your filename:

```
$uploader->setFilename('my%$crazy@filename.txt');
$uploader->makeFilenameSafe();
```

##### uploadFile() 
Uploads the file

`$uploader->uploadFile();`

## Extending

For a cleaner way to configure you uploads you can extend the FileUploader class which will give you access to the protected
variables, e.g.:

```

class ImageUploader extends FileUploader{

  protected $allowedMimeTypes = [
    'image/jpeg',
    'image/png',
    'image/gif'
  ]
  
	protected $maxFileSize = 5e+6; // In bytes (this is 5MB or 5000000 bytes)
	protected $makeFilenameUnique = true;
	protected $createDirIfNotExists = true;
}
```

This can then be used as follows:

```
$image = File::getInstance($_FILE['image']);
$uploader = new ImageUploader($image);
$image->uploadFile();

```

The following variables are protected and so can be set by child classes:

```
protected $allowedMimeTypes = [];
protected $blockedMimeTypes = [];
protected $maxFileSize = 1000000;
protected $makeFilenameUnique = false;
protected $overwrite = false;
protected $createDirIfNotExists = false;
```




