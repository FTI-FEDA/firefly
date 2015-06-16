# Firefly

Firely is a stupidly simple file uploader class written in PHP.

## Getting Started

```php
<?php

require_once('src/Firefly.php');

// initialize uploader & define upload path and acceptable file types
$uploader = Firefly::uploader('uploads/resumes', ['doc', 'docx', 'pdf', 'pdfx', 'pages']);

$uploader->upload($_FILES['resumeUpload']);

// an empty errors attribute should indicate a succesful upload
if ( empty($uploader->errors) )
  echo 'There was an error: ' . $uploader->errors[0];
```

## Lightweight Validation

Firefly ships with some lighweight validation that comes in the form of callback methods:
- File size 
- File name uniqueness
- Extension type

Each callback is triggered by default. You can turn off specific callbacks via the ```setCallbacks``` method:

```php
$uploader->setCallbacks(['checkFileExists', 'checkContentType']); // skips checkFileSize callback
```

## To Do
- Advanced validation (mime type, etc.)
- Extract file information
- Unit testing

## License

The Firefly class is open-sourced software licensed under the [MIT license](https://github.com/fti-feda/firefly/blob/master/LICENSE).
