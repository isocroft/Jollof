<?php

/*!
 * Jollof (c) Copyright 2016
 *
 * {System.php}
 *
 */

use \Config;


final class System {

    /**
     * @var System
     */

    private static $instance = NULL;

    /**
     * @var callable
     */

    private $errorHandler;

    /**
     * @var callable
     */

    private $blindRouteHandler;

    /**
     * @var array
     */

    private $faultedMiddlewares;

    /**
     * @var array
     */

    private $superGlobals;

    /**
     * @var array
     */

    private $middlewares;

    /**
     * @var callable
     */

    private $customEventHandlers;

    /**
     * @var bool
     */

    private $errorHandleRequested;

    /**
     * Constructor.
     *
     *
     * @param void
     * @api
     */

    private function __construct(Config $config){

        $this->middlewares = array();

        $this->errorHandler = NULL;

        $this->blindRouteHandler = NULL;

        $this->errorHandleRequested = FALSE;

        $this->faultedMiddlewares = array();

        $this->customEventHandlers = array();

        $this->superGlobals = array(
            '_GET', 
            '_POST', 
            '_COOKIE', 
            '_FILES', 
            '_SERVER', 
            '_REQUEST', 
            '_ENV', 
            'GLOBALS'
        );
        // System Kill
        if(function_exists('pcntl_signal')){

            pcntl_signal(SIGTERM, array(&$this, 'shutdown'));
        }

        // CTRL+C
        if(function_exists('pcntl_signal')){ 

            pcntl_signal(SIGINT, array(&$this, 'shutdown'));
        }

        // Set to plain text message instead of HTML messages ...
        ini_set('html_errors', '0');

        // Tell PHP to use the [CLI] error handler
        set_error_handler(array(&$this, 'error_handler'));

        // Tell PHP when an {Exception} occurs
        set_exception_handler(array(&$this, 'exception_handler'));
        // Surpress Warnings
        assert_options(ASSERT_WARNING, 0);
        // Cacth fatal Errors
        register_shutdown_function(array(&$this, 'shutdown'));
    }

    public function __destruct(){


    }

    public static function createInstance(Config $config){

         if(static::$instance == NULL){
               static::$instance = new System($config);
               return static::$instance;
         }
    }

    public function shutdown($signal = null){
         $fatalError = error_get_last();
         $globs = array_keys($GLOBALS);


         if((!headers_sent()) && is_array($fatalError)){
            if(!$this->hasErrorHandlerBeenRequested()){
                $this->error_handler($fatalError['type'], $fatalError['message'], $fatalError['file'], $fatalError['line']);
            }    
         }

         // ensure that __destruct() calls are made for all existing class objects still in memory
         foreach($globs as $var){
            if(in_array($var, $this->superGlobals)){ 
                    continue;
            }
            unset($GLOBALS[$var]); 
         }
    }

    // A custom error handler
    public function error_handler($errno, $errstr, $errfile, $errline){

       $handler = $this->getErrorHandler();

       if($GLOBALS['app']->inCLIMode()){
           fwrite(STDERR, "Jollof App Exception: => " . PHP_EOL . PHP_EOL . " $errstr in [$errfile] on :$errline" . PHP_EOL);
       }

       if(isset($handler) && is_callable($handler)){
           
           $this->register_error($handler($errno, $errstr, $errfile, $errline));
       }

    }

    public function exception_handler($ex){

        $handler = $this->getErrorHandler();

        if($GLOBALS['app']->inCLIMode()){
           fwrite(STDERR, "Jollof App Exception: => " . PHP_EOL . PHP_EOL . " {$ex->getMessage()} in [{$ex->getFile()}] on :{$ex->getLine()}" . PHP_EOL);
        }

        if(isset($handler) && is_callable($handler)){
           $handler($ex->getCode(), $ex->getMessage(), $ex->getFile(), $ex->getLine());
        }

        $this->register_error($ex);

    }

    private function register_error(\Exception $e, $asMail = FALSE){
        
        $errmsg = "PHP Fatal Error: Uncaught Exception '%s' with message '%s' in %s:%s \n\n\t Stack Trace: \n\n%s\n thrown in %s on line %s";

        $errmsg = sprintf($errmsg, 
            get_class($e),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString(),
            $e->getFile(),
            $e->getLine());

        if(error_reporting()){
            if($asMail === FALSE){
                error_log($errmsg);
            }else{
                error_log($errmsg, 1, 'admin@example.com');
            }

        }
    }

    private function hasErrorHandlerBeenRequested(){

        return $this->errorHandleRequested;
    }

    private function setErrorHandler($callback){

       $this->errorHandler = (is_callable($callback)) ? $callback : NULL;
    }

    private function getErrorHandler(){

        $this->errorHandleRequested = TRUE;

       return $this->errorHandler;
    }

    private function setBlindRouteCallback($callback){

       $this->blindRouteHandler = (is_callable($callback)) ? $callback : NULL;
    }

    private function getBlindRouteCallback(){

       return $this->blindRouteHandler;
    }

    private function addMiddlewares($name, $callback){

       $this->middlewares[$name] = $callback;
    }

    public function hasBlindRouteCallback(){

       return isset($this->blindRouteHandler);
    }

    public function fireCallback($callbackName, array $callbackArgs){

        $result = NULL;
        switch($callbackName){
            case 'BLIND_ROUTE_CALLBACK':
               $result = $this->blindRouteHandler($callbackArgs[0]);
            break;
            case 'FILTERED_ROUTE_CALLABACK':
               $result = FALSE;
            break;
            default:
                if(array_key_exists($callbackName, $this->customEventHandlers)){
                     $callback = $this->customEventHandlers[$callbackName];
                     if(is_callable($callback)){
                         $result = call_user_func_array($callback, $callbackArgs);
                     }
                }
            break;
        }

        return $result;
    }

    public static function fire($eventName, array $args){

        return static::$instance->fireCallback($eventName, $args);
    }

    public static function on($eventName, callable $eventHandler){

        static::$instance->setCustomEvent($eventName, $eventHandler);
    }

    public function getFaultedMiddlewares(){

       return $this->faultedMiddlewares;
    }

    private function setCustomEvent($name, $function){

        $this->customEventHandlers[$name] = $function;
    }

    public function executeAllMiddlewares($route, $auth){
           $result = array();
           // PHP 5.0+
           foreach($this->middlewares as $name => $callback){
             if(is_callable($callback)){
                $result[] = $callback($route, $auth);
             }else{
                throw new \Exception("Error Processing Request >> [$name] Middleware Callback Undefined");
             }
             try{
                $index = ((count($result)) - 1);
                if($result[$index] === FALSE){
                     $this->faultedMiddlewares[] = $name;
                }else{
                    if(array_key_exists('HTTP_CODE', $GLOBALS)){
                        if($GLOBALS['HTTP_CODE'] === 303
                            || $GLOBALS['HTTP_CODE'] === 302){
                            exit;
                        }
                    }
                }
             }catch(\Exception $e){ exit; }
           }

           return (bool) array_reduce($result, 'reduce_boolean', TRUE);
    }

    public static function onAppError(callable $callback){

        static::$instance->setErrorHandler($callback);
    }

    public static function middleware($middleware_name, callable $callback){

       static::$instance->addMiddlewares($middleware_name,  $callback);
    }

    public static function onBlindRoute(callable $callback){

        static::$instance->setBlindRouteCallback($callback);
    }

    public static function onFiltered(callable $callback){
        ;
    }

}

?>