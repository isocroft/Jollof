<?php

/*!
 * Jollof (c) Copyright 2016
 *
 * {File.php}
 *
 */

final class File {

    private static $instance = NULL;

    protected $file_name;

    /**
     * Constructor
     *
     *
     * @param void
     * @api
     */

    private function __construct(){

    }

    /**
     *
     *
     *
     *
     *
     * @param void
     * @return object $instance
     * @api
     */

    public static function createInstance(){

        if(static::$instance === NULL){
            static::$instance = new File();
            return static::$instance;
        }
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
     *
     * @param string $file_path
     * @return bool
     * @api
     */

    public static function deleteFile($file_path){

        return (bool) delete_file($file_path);
    }

    /**
     * Creates a folder in server file system
     *
     *
     * @param string $folder_name
     * @param bool $hide
     * @return bool
     * @api
     */

    public static function makeFolder($folder_name, $hide = false){

        return (bool) make_folder($folder_name, $hide);
    }

    /**
     * Reads out a file in chunks.
     *
     *
     *
     * @param string $file_path
     * @param array $file_context_options
     * @return string $content;
     * @api
     */

    public static function readChunk($file_path, array $file_context_options = array()){

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
     * Writes out a file in chunks.
     *
     *
     *
     * @param string $file_path
     * @param string $file_content
     * @return bool
     * @api
     */

    public static function writeChunk($file_path, $file_content){

        if(!isset($file_path)){
            return false;
        }

        if(!isset($file_content)){
            $file_content = " " . PHP_EOL;
        }

        return file_put_contents($file_path, $file_content, LOCK_EX);
    }

}

?>