<?php

/*!
 * Jollof (c) Copyright 2016
 *
 * {Logger.php}
 *
 */

final class Logger {

    /*
     * @var Logger
     */

    private static $instance = NULL;

    protected $log_file;

  /**
   * Constructor
   *
   *
   * @param void
   * @api
   */

    private function __construct(){

         $this->setLogFileName();
    }

    /**
     *
     *
     *
     *
     * @param void
     * @return object $instance
     * @api 
     */

    public static function createInstance(){

    	if(static::$instance == NULL){
             static::$instance = new Logger();
             return static::$instance;
        }
    }

    public function setLogFileName($file = 'exec'){

        $this->log_file = $GLOBALS['env']['app.path.storage'] . $file . '.log';        
    }

    public static function info($message, $file = NULL){

        static::$instance->publishToFile($message, "info", $file);
    }

    public static function warn($message, $file = NULL){

        static::$instance->publishToFile($message, "warn", $file);
    }

    public static function error($message, $file = NULL){

        static::$instance->publishToFile($message, "error", $file);
    }

    private function publishToFile($line, $type, $file){

        if(is_string($file)){
           $this->setLogFileName($file);
        }
    	write_to_file($this->marker($type) . ' ~ ' . $line . PHP_EOL . PHP_EOL, $this->log_file, FALSE);
    }

    private function marker($type){
       
        return '' . strtoupper($type) . ':' . date('Y-m-d H:i:s');
    }

}

?>