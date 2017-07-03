<?php

/*!
 * Jollof (c) Copyright 2016
 *
 * {File.php}
 *
 */

use \Cache;

final class File {

    /**
      * @var File
      */

    private static $instance = NULL;

    /**
      * @var Cache - For caching file related information across requests
      */

    protected $file_cache;

    /**
     * Constructor
     *
     *
     * @param Cache $cache
     * @api
     */

    private function __construct(Cache $cache){

        $this->file_cache = $cache;
    }

    /**
     *
     *
     *
     *
     */

    public function __destruct(){

        $this->file_cache = NULL;
    }

    /**
     *
     *
     *
     *
     *
     * @param Cache $cache
     * @return object $instance
     * @api
     */

    public static function createInstance(Cache $cache){

        if(static::$instance === NULL){
            static::$instance = new File($cache);
            return static::$instance;
        }
    }

    /**
     * Checks if a file path exists on the file system 
     *
     *
     *
     *
     * @param string $file_path
     * @return bool
     */

    public static function exists($file_path){

            return file_exists($file_path);
    }

    /**
     * Reads out the contents of a file all at once.
     *
     *
     *
     * @param string $file_path
     * @return string
     * @api
     */

    public static function read($file_path){

        return read_from_file($file_path);
    }

    /**
     * Reads out the contents of a file line by line 
     * as an array of lines
     *
     *
     * @param string $file_path
     * @return array $file_bits
     * @api
     */

    public static function readAsArray($file_path){

        $file_bits =  file($file_path);

        return $file_bits;
    }

    /**
     * Writes into a file all at once
     *
     *
     *
     * @param string $file_path
     * @param string $contents
     * @param bool $overwrite
     * @return bool
     * @api
     */

    public static function write($file_path, $contents, $overwrite){

        return write_to_file($contents, $file_path, $overwrite);
    }

    /**
     * Creates a file in the server file system.
     * 
     *
     *
     * @param string $file_path
     * @return bool
     * @api
     */

    public static function makeFile($file_path){

        return (bool) make_file($file_path);
    }

    /**
     * Deletes a file from the server file system
     *
     *
     *
     * @param string $file_path
     * @return bool
     * @api
     */

    public static function deleteFile($file_path){

        return (bool) delete_file($file_path);
    }

    /**
     * Creates a folder on  the server file system
     *
     *
     * @param string $folder_name - name of the folder to be create on the file system
     * @param bool $hide - if the folder will be hidden and non-writable on the file system
     * @param int $mode - permissions for the folder be created on the file system
     * @param bool $depth -
     * @return bool
     * @api
     */

    public static function makeFolder($folder_name, $hide = FALSE, $mode = 0755, $depth = TRUE){


        if($hide === TRUE){

            $mode = 0777;
        }

        if(is_dir($folder_name)){

            return false;
        }   

        return (bool) make_folder($folder_name, $hide, $mode, $depth);
        
    }

    /**
     *
     *
     *
     * @param string $folder_name
     * @return bool
     * @api
     */

    public static function deleteFolder($folder_name) {

        return (bool) del_folder($folder_name);            
    }

    /**
     * Reads out a file as a stream.
     *
     *
     *
     * @param string $file_path
     * @param array $file_context_options
     * @return string $content;
     * @api
     */

    public static function readStream($file_path, array $file_context_options = array()){

        $content = NULL;
        $context = NULL;
        $include = NULL;

        if(array_key_exists('http', $file_context_options)){
            $context = stream_context_create($file_context_options);
        }

        // Open the file using the HTTP headers set above
        if($context === NULL){
            $include = false;
            $content = file_get_contents($file_path, $include);
        }else{

            if(PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION <= 1){
                $include = true;
            }else{
                $include = FILE_USE_INCLUDE_PATH;
            }

            $content = file_get_contents($file_path, $include, $context);
        }

        sleep(2);

        return $content;

    }

    /**
     * Read file as chunks
     *
     *
     *
     * @param string $file_name - file name/path
     * @param integer $offset - offset in bytes
     * @return mixed
     * @api
     */

    public static function readChunk($file_name, $offset = 0){
        
        $hdle = fopen($file_name, 'r');
        
        /* fseek($hdle, $offset); */
    }

    /**
     * Fetch file(s) that have file names/extension
     * as that which is depicted by the pattern
     * within the top-level folder and sub folders
     *
     *
     * @param string $pattern - a glob pattern (does not support braces for now)
     * @return array
     * @api
     */

    public static function grepFiles($pattern){

        return rglob($pattern, GLOB_NOSORT);
    }

    /**
     * Writes out to a file in chunks.
     * (used especially for chunked file uploads)
     *
     *
     *
     * @param string $file_name - filename
     * @param string $chunk_content - the chunk data itself
     * @param integer $offset - offset in bytes
     * @param bool $replace - if the chunk data will overwrite existing data in the file
     * @return mixed (integer|bool) - number of bytes written or write operation failure status
     * @api
     */

    public static function writeChunk($file_name, $chunk_content, $offset = 0, $replace = FALSE){

        return write_file_chunk($file_name, $chunk_content, $replace);
    }



}

?>