<?php

/**
 * Firefly: A simple PHP uploader
 *
 * @author Scott Cruwys
 */

class Firefly {
  
  protected $permissions = 0777;

  protected $file = [];

  protected $mimes;

  protected $max_size = 500000;

  protected $upload_dir;

  protected $callbacks = ['checkContentType', 'checkFileExists', 'checkFileSize'];

  public $errors = [];
  
  /**
   * Initialize/return Firefly object
   * 
   * @param  string $upload_dir = 'path/to/destination/folder'
   * @param  array  $mimes
   * @return Firefly
   */
  public static function uploader( $upload_dir, $mimes = ['*'] ) {
    return new Firefly($upload_dir, $mimes);
  }

  /** 
   * Set upload directory and accepted mime types
   * 
   * @param string $upload_dir
   * @param array  $mimes
   */
  public function __construct( $upload_dir, $mimes ) {
    $this->mimes = $mimes;
    
    if ( !$this->setDirectory($upload_dir) ) {
      throw new Exception('Cannot create destination at ' . $upload_dir);
    }
  }

  public function upload( $file ) {
    $this->file = $file;

    if ( $this->validate() ) return $this->saveFile();
  }

  protected function saveFile() {
    $upload_file = $this->upload_dir . basename($this->file['name']);

    if ( move_uploaded_file($this->file['tmp_name'], $upload_file) ) return true;
    
    throw new Exception('An error occurred during the upload process');
  }

  public function validate() {
    foreach ($this->callbacks as $method) {
      $this->$method();
    }

    if ( empty($this->errors) ) {
      return true;
    }
  }
  
  // Directory Stuff
  protected function setDirectory( $dir = '/' ) {
    $this->upload_dir = rtrim($dir, '/') . '/';

    return $this->directoryExists() ?: $this->createDirectory();
  }

  protected function directoryExists() {
    return file_exists($this->upload_dir);
  }

  protected function createDirectory() {
    return mkdir($this->upload_dir, $this->permissions, true);
  }

  // Check content type
  protected function checkContentType() {
    if ( in_array('*', $this->mimes) ) return false;

    $tmp = explode('.', $this->file['name']);
    $ext = end($tmp);

    if ( !in_array($ext, $this->mimes) )
      $this->errors[] = ucfirst($ext) . ' is not an acceptable file type';       
  }

  // Check file size 
  protected function checkFileSize() {
    if ( $this->file['size'] > $this->max_size)
      $this->errors[] = 'File is too large';
  }
  // Check if file exists
  protected function checkFileExists() {
    $upload_file = $this->upload_dir . basename($this->file['name']);

    if ( file_exists($upload_file) )
      $this->errors[] = 'File already exists';
  }

  public function setCallbacks( $callbacks = array() ) {
    foreach ($callbacks as $method) {
      if ( !method_exists($this, $method) ) {
        throw new Exception('Invalid callback found');
      }
    }

    $this->callbacks = $callbacks;
  }

  /**
   * Set directory read/write permissions
   * 
   * @param Integer $permissions
   */
  public function setPermissions( $permissions ) {
    $this->permissions = $permissions;
  }

}
