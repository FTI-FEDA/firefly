<?php 

namespace Firefly;

class MimeType {

  /**
   * Returns extension/mime-type pairings
   * 
   * @return array
   */
  public static function aliases() {
    return array(
      '7z'   => 'application/x-7z-compressed',
      'bmp'  => 'image/bmp',
      'bz'   => 'application/x-bzip',
      'bz2'  => 'application/x-bzip2',
      'css'  => 'text/css',
      'csv'  => 'text/csv',
      'doc'  => 'application/msword',
      'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'gif'  => 'image/gif',
      'html' => 'text/html', 
      'jpeg' => 'image/jpeg',
      'jpg'  => 'image/jpeg',
      'json' => 'application/json',
      'pdf'  => 'application/pdf',
      'png'  => 'image/png',
      'ppt'  => 'application/vnd.ms-powerpoint',
      'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
      'tar'  => 'application/x-tar',
      'tiff' => 'image/tiff',
      'tsv'  => 'text/tab-separated-values',
      'txt'  => 'text/plain',
      'vsd'  => 'application/vnd.visio',
      'xls'  => 'application/vnd.ms-excel',
      'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'xml'  => 'application/xml',
      'yaml' => 'text/yaml',
      'zip'  => 'application/zip'
    );
  }

}