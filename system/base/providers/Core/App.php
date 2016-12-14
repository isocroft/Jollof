<?php

/**
 * Jollof Framework (c) 2016
 * 
 * {App.php}
 */

namespace Providers\Core;

use Providers\Core\HTTPResolver as Resolver;
use Providers\Services\DBService as DBService;
use Providers\Services\EnvService as EnvService;

use Request;
use Router;
use Session;
use Auth;
use System;
use Cache;
use Helpers;
use File;
use Response;
use Validator;
use Logger;
use TextStream;
use Comms;

class App {

    /**
     * @var string
     */ 

     private $apphost;

    /**
     * @var string
     */ 

     private $os;

    /**
     * @var array
     */ 

     protected $cookieQueue;

    /**
     * @var bool
     */ 

     protected $hasCachedModels;

    /**
     * @var Providers\Core\HTTPResolver
     */ 

     protected $resolver;

    /**
     * @var Providers\Services\DBService
     */ 

     protected $dbservice;

    /**
     * @var Providers\Services\EnvService
     */ 

     protected $envservice;

    /**
     * @var array
     */ 

     protected $instances;

     /**
     * Constructor.
     *
     * @param void
     *
     * @scope public
     */

     public function __construct(){

        if(!$this->inCLIMode()){

             $this->resolver = new Resolver();

             $this->apphost = (Request::header('HTTP_PROTOCOL') . Request::getHost());

             $this->os = preg_replace('/^(?:.+)?\((Win32|Linux)\)(?:.+)?$/i', '${1}', Request::header('SERVER_SOFTWARE'));
        }     

        $this->instances = array();

        $this->cookieQueue = array();

        $this->hasCachedModels = FALSE;

     }

     public function __destruct(){ 

          $this->shutDown();
     }


     public function installDBService(array $DBCONFIG){

            $engine = $DBCONFIG['engines']['mysql'];
            
            if (! extension_loaded($engine['driver']) 
          	   || ! extension_loaded(strtolower($engine['driver']) . "_mysql")){
	            
	           exit(1);
            }

			if(!is_array($DBCONFIG)){
             
                exit(1);
			}

			$this->dbservice = new DBService($DBCONFIG); // extract($DBCONFIG, EXTR_PREFIX_ALL , "db");
     }

     public function installENVService(array $ENVCONFIG){

     	    if ( ! extension_loaded($ENVCONFIG['encryption_scheme'])){
                 # "Error: 01:29:35 - 03/09/2016 [.]" . PHP_EOL;
               
                 exit(1);
            }

            if ($ENVCONFIG['image_processing_enabled'] && ! extension_loaded('gd')){

                 exit(1);
            }

	     	if(!is_array($ENVCONFIG)){
	             
	            exit(1);
				   
			}	   

            $this->envservice = new EnvService($ENVCONFIG);
     }

     public function getOS(){

     	return $this->os;
     }

     public function getHost($appendChar = ''){

     	return $this->apphost . $appendChar;
     }

     public function setDBConnection($env_path){ 

         $this->dbservice->connect($env_path);
     }

     public function getCookieQueue(){

         return $this->cookieQueue;
     }

     public function pushCookieQueue($ckey, $cval){

         $this->cookieQueue[$ckey] = $cval;
     }

     public function initHTTPResolver(){

         $router = $this->getInstance('Router');

         $system = $this->getInstance('System');

         $auth = $this->getInstance('Auth');

     	 $this->resolver->draftRouteHandler($router->getMethod());

     	 return $this->resolver->handleCurrentRoute($router, $system, $auth);

     }

     public function cacheModelInstances(array $models){
 
        if($this->hasCachedModels === FALSE){ 

            $this->dbservice->setModelsToBuilder($models);

            $this->hasCachedModels = TRUE;

        }    

     }

     public function exposeEnvironment($root){

         return $this->envservice->exposeEnvironment($root);

     }

     public function getRemoteErrorReporter(){

         $packages_path = $GLOBALS['env']['app.path.packages'];
        
         if(file_exists($packages_path . 'vendor/autoload.php')){
             return (new \Jollof\ErrorReporter\Reporter($this->getInstance('Comms')));
         }

         return NULL;
     }

     public function registerCoreComponents(){
          /*
           * Setup all Singletons for the application
           */

         // @TODO: later, try to do the below in a loop! it probably will be a much cleaner code
               $this->instances['Logger'] = Logger::createInstance();
               $this->instances['System'] = System::createInstance();
               $this->instances['Session'] = Session::createInstance($this->envservice->getConfig('app_session'));
               $this->instances['Response'] = Response::createInstance();
               $this->instances['Request'] = Request::createInstance($this->envservice->getConfig('app_uploads'));
               $this->instances['Cache'] = Cache::createInstance($this->envservice->getConfig('app_cache'));
               $this->instances['Router'] = Router::createInstance($this->getInstance('Request'), $this->getInstance('Response'));
               $this->instances['Validator'] = Validator::createInstance();
         	   $this->instances['File'] = File::createInstance();
         	   $this->instances['Auth'] = Auth::createInstance($this->envservice->getConfig('app_auth'));
               $this->instances['Helpers'] = Helpers::createInstance();
               $this->instances['Comms'] = Comms::createInstance($this->envservice->getConfig('app_mails'), $this->envservice->getConfig('app_connection'), $this->envservice->getConfig('app_messaging'));
               $this->instances['TextStream'] = TextStream::createInstance();
     }

     public function registerComponent(callable $componentPool){

            $component = $componentPool($this->envservice);

            if(!is_object($component)){
                throw new \UnexpectedValueException("Failed To Register Component >> Expected an 'object' but found a/an '" . gettype($component) . "'");
            }

            $this->instances[get_class($component)] = $component;  
     }     

     public function inCLIMode(){

         return (isset($_SERVER['argv']) && isset($_SERVER['argc']));
     }

     private function shutDown(){

     	 Logger::info("Application is Shutting Down...");

         $this->hasCachedModels = FALSE;

         $this->dbservice = NULL; // called by __destruct to disconnect DB connection 

         $this->envservice = NULL; // called by __desstruct to unset configs

         $this->resolver = NULL; // called by __destruct 

         $this->instances = array(); // recover more memory (if any need be)
            
     }

     public function crash(\Exception $e){
     	 
     	  Response::error($e);
     }

     public function hasInstances(){

         return (count($this->instances) > 0);
     }

     private function getInstance($instance_name){
        
        if(array_key_exists($instance_name, $this->instances)){

             return $this->instances[$instance_name];
        }

        return NULL;
     }

}


?>
