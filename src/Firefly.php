<?php 

namespace Firefly;
require_once 'MimeType.php';

/**
 * Firefly: A simple PHP uploader
 *
 * @author Scott Cruwys
 */

class Firefly {
  
  protected $permissions = 0777;

  protected $file = [];

  protected $accepted_types;

  protected $max_size = 500000;

  protected $upload_dir;

  protected $callbacks = ['checkExtension', 'checkFileExists', 'checkFileSize'];

  public $errors = [];
  
  /**
   * Initialize/return Firefly object
   * 
   * @param  string $upload_dir = 'path/to/destination/folder'
   * @param  array  $accepted_types
   * @return Firefly
   */
  public static function uploader( $upload_dir, $accepted_types = ['*'] ) {
    return new Firefly($upload_dir, $accepted_types);
  }

  /** 
   * Set upload directory and accepted mime types
   * 
   * @param string $upload_dir
   * @param array  $accepted_types
   */
  public function __construct( $upload_dir, $accepted_types ) {
    $this->accepted_types = $accepted_types;
    
    if ( !$this->setDirectory($upload_dir) ) {
      throw new Exception('Cannot create destination at ' . $upload_dir);
    }
  }

  /**
   * Check for valid file and save/upload
   * 
   * @param  string $filename
   * @return boolean
   */
  public function upload( $filename = false ) {
    if ( empty($this->file) ) $this->mount($filename);

    if ( $this->validate() ) return $this->saveFile();
  }

  /**
   * Set the uploaded file as a variable
   *   
   * @param  string $filename 
   * @return Firefly           
   */
  public function mount( $filename ) {
    $this->file = $filename;

    return $this;
  }

  /**
   * Save file to $upload_dir
   * 
   * @return boolean
   */
  protected function saveFile() {
    $upload_file = $this->upload_dir . basename($this->file['name']);

    if ( move_uploaded_file($this->file['tmp_name'], $upload_file) ) return true;
    
    throw new Exception('An error occurred during the upload process');
  }

  /**
   * Run validation callbacks
   * 
   * @return boolean
   */
  public function validate() {
    foreach ($this->callbacks as $method) {
      $this->$method();
    }

    if ( !empty($this->errors) ) {
      return $this->errors;
    }

    return true;
  }
  
  /**
   * Set working directory and create if one does not exist
   * 
   * @param string $dir
   */
  protected function setDirectory( $dir = '/' ) {
    $this->upload_dir = rtrim($dir, '/') . '/';

    return $this->directoryExists() ?: $this->createDirectory();
  }

  /**
   * Check if directory exists
   * 
   * @return boolean
   */
  protected function directoryExists() {
    return file_exists($this->upload_dir);
  }

  /**
   * Create a new directory and set permissions
   * 
   * @return boolean
   */
  protected function createDirectory() {
    return mkdir($this->upload_dir, $this->permissions, true);
  }

  public function checkContentType() {
    try {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
    } catch ( Exception $e ) {
      throw new Exception('Firefly requires the fileinfo.so extension for content type validation');
    }

    $ctype = finfo_file($finfo, $this->file['tmp_name']);
    finfo_close($finfo);

    foreach( $this->accepted_types as $mime_type ) {
      if ( $ctype == MimeType::aliases()[$mime_type] )
        $passed = true; break;
    }

    if ( !$passed )
      $this->errors[] = $ctype . ' is not an acceptable file type';  
  }

  protected function checkExtension() {
    if ( in_array('*', $this->accepted_types) ) return false;

    $tmp = explode('.', $this->file['name']);
    $ext = end($tmp);

    if ( !in_array($ext, $this->accepted_types) )
      $this->errors[] = ucfirst($ext) . ' is not an acceptable file type';       
  }

  protected function checkFileSize() {
    if ( $this->file['size'] > $this->max_size)
      $this->errors[] = 'File is too large';
  }

  protected function checkFileExists() {
    $upload_file = $this->upload_dir . basename($this->file['name']);

    if ( file_exists($upload_file) )
      $this->errors[] = 'File already exists';
  }

  /**
   * Set validation callbacks
   * 
   * @param array $callbacks
   */
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
