<?php

/**
 * Jollof Framework (c) 2016 - 2017
 *
 *
 * {App.php}
 *
 */

namespace Providers\Core;


use Providers\Tools\JollofSecureHeaders as JollofSecureHeaders;
use Providers\Core\HTTPResolver as Resolver;
use Providers\Services\DBService as DBService;
use Providers\Services\EnvService as EnvService;

use Request;
use Router;
use Session;
use Auth;
use Config;
use System;
use Cache;
use Helpers;
use File;
use Response;
use Validator;
use Logger;
use TextStream;
use Comms;

final class App {

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
     * @var Providers\Tools\JollofSecureHeaders
     */

     protected $jheaders;

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

             $this->jheaders = new JollofSecureHeaders();

             $this->os = get_os();
        }

        $this->instances = array();

        $this->cookieQueue = array();

        $this->hasCachedModels = FALSE;

     }

    /**
     * Destructor.
     *
     * @param void
     *
     * @scope public
     */

     public function __destruct(){

          $this->shutDown();
     }

     /**
      * Registers the database configuration.
      *
      *
      * @param array $DBCONFIG
      * @return void
      * @throws Exception
      */

     public function installDBService(array $DBCONFIG){

            if(!is_array($DBCONFIG)){

                exit(1);
            }

            $engine_type = $DBCONFIG['db_engine'];

            $engines = $DBCONFIG['engines'];
            
            if(!array_key_exists($engine_type, $engines)){

                throw new \Exception("Database Engine not Found");
            }

            $engine = $engines[$engine_type];

            $driver = $engine['driver'];

            /*
              if (!extension_loaded('mongo')) {
                      ;
              }
            */  

            if (! extension_loaded($driver) 
                || ! extension_loaded(
                      ($engine_type != $driver? strtolower($driver) . "_" : "") . $engine_type)){

                exit(1);
            }   
          

			      $this->dbservice = new DBService($DBCONFIG); // extract($DBCONFIG, EXTR_PREFIX_ALL , "db");
     }

     /**
      * Registers the environmental configuration.
      *
      *
      * @param array $ENVCONFIG
      * @return void
      */

     public function installENVService(array $ENVCONFIG){

            /*

            if (! extension_loaded('zlib') ){

                exit(1);
            }

            */

     	      if ( ! extension_loaded($ENVCONFIG['encryption_scheme'])){

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

     /**
      * Retrieves the Operating System name for the server.
      *
      *
      * @param void
      * @return string
      */

     public function getOS(){

     	    return $this->os;
     }

     /**
      * Retrieves the host name for the server.
      *
      *
      * @param string $appendChar
      * @return string
      */

     public function getHost($appendChar = ''){

     	    return $this->apphost . $appendChar;
     }

     /**
      * Sets up the connection for the database.
      *
      *
      * @param void
      * @return array
      */

     public function getCookieQueue(){

         return $this->cookieQueue;
     }

     /**
      * Adds an entry to the cookie queue.
      *
      *
      * @param string $ckey
      * @param array $cval
      * @return void
      */

     public function pushCookieQueue($ckey, $cval){

         $this->cookieQueue[$ckey] = $cval;
     }

     /**
      * Initializes the Http resolution mechanism.
      *
      *
      * @param void
      * @return void
      */

     public function initHTTPResolver(){

         $router = $this->getInstance('Router');

         $system = $this->getInstance('System');

         $auth = $this->getInstance('Auth');

     	   $this->resolver->draftRouteHandler($router->getMethod());

         $request = $this->getInstance('Request');
                    
         $session = $this->getInstance('Session')->getDriver(); 

     	   $resolved = $this->resolver->handleCurrentRoute($router, $system, $auth);

         if($request->getMethod() === 'GET' 
            && !$request->ajaxRequest()){

                $rl = $router->getCurrentRouteUrl();
              
                if($session->read('previousRoute') !== $rl){
                    $session->write(
                            'previousRoute', 
                            $rl
                    );
                }
         }

         return $resolved;

     }

     /**
      * Stores all model instances for latter access.
      *
      *
      * @param array $models
      * @return void
      */

     public function cacheModelInstances(array $models){

        if($this->hasCachedModels === FALSE){

            $this->dbservice->bindSchema($models);

            $this->hasCachedModels = TRUE;

        }

     }

     /**
      * Reveals all environment configs to the global space.
      *
      *
      * @param string $root
      * @return array
      */

     public function exposeEnvironment($root){

         return $this->envservice->exposeEnvironment($root);

     }

     /**
      * Retrieve the name of the current DB driver
      *
      *
      *
      * @param void
      * @return string
      *
      */

     public function getDBDriver(){

          return $this->dbservice->getDBEngine();
     }

     /**
      * Creates the query builder for each (requesting) Model
      * (Proxy API)
      *
      *
      * @param array $atrribs
      * @param string $modelName
      * @return \Providers\Core\QueryBuilder
      */

     public function getBuilder(array $attribs, $modelName){

          $builder = $this->dbservice->getBuilder($attribs, $modelName);

          if(!is_null($builder)){
                /* making sure that Jollof [Model] queries and results can be cached */
                $builder->setQueryAndResultCache($this->getInstance('Cache'));
          }

          return $builder;
     }

     /**
      * Retrives the error reporter.
      *
      *
      * @param void
      * @return void
      */

     public function getRemoteErrorReporter(){

         $packages_path = $GLOBALS['env']['app.path.packages'];

         $errorsConfig = $this->envservice->getConfig('app_errors');

         $settings = $errorsConfig['reporter_settings'];

         $request = $this->getInstance('Request');

         if(array_key_exists('meta_data', $settings)){
                if(is_array($settings['meta_data'])){
                    $settings['meta_data']['browser'] = $request->getInfo('HTTP_USER_AGENT');
                    $settings['meta_data']['req_time'] = $request->getInfo('REQUEST_TIME');
                    $settings['meta_data']['time_zone'] = 'Africa/Lagos';

                    $settings['meta_data']['exec_session_id'] = JOLLOF_EXEC_ID;
                }
         }

         if(file_exists($packages_path . 'vendor/autoload.php')){
             return (new \Jollof\ErrorReporter\Reporter($this->getInstance('Comms'), $settings));
         }

         return NULL;
     }

     /**
      * Registers all core components and ancillary
      * services for the application. 
      *
      * @param void
      * @return void
      */

     public function registerCoreComponents(){


            $dotenv = $GLOBALS['env']['app.path.base'] . '.env';
          
            /* 
             *  set up config for Content-Security-Policy HTTP response headers
             */
            if(!$this->inCLIMode()){

                $this->jheaders->installConfig($this->envservice->getConfig("app_security"));

            }

            /*
             * Setup all Singletons for the application
             */

            /*
             * @TODO: later, try to do the below in a loop! it probably will 
             *         be a much cleaner code
             */

               $this->instances['Logger'] = Logger::createInstance();
               $this->instances['Config'] = Config::createInstance($this->envservice->getAllConfig());
               $this->instances['System'] = System::createInstance($this->getInstance('Config'));
               $this->instances['Session'] = Session::createInstance($this->envservice->getConfig('app_session'));
               $this->instances['Request'] = Request::createInstance($this->envservice->getConfig('app_uploads'), $this->getInstance('Session'));
               $this->instances['Response'] = Response::createInstance($this->inCLIMode()? array() : $this->jheaders->getSourceNonces());
               $this->instances['Router'] = Router::createInstance($this->getInstance('Request'), $this->getInstance('Response'));
               $this->instances['Cache'] = Cache::createInstance($this->envservice->getConfig('app_cache'));
               $this->instances['Validator'] = Validator::createInstance();
         	   $this->instances['File'] = File::createInstance($this->getInstance('Cache'));
         	   $this->instances['Auth'] = Auth::createInstance($this->envservice->getConfig('app_auth'), $this->getInstance('Request'));
               $this->instances['Helpers'] = Helpers::createInstance();
               $this->instances['Comms'] = Comms::createInstance($this->envservice->getConfig('app_mails'), $this->envservice->getConfig('app_connection'), $this->envservice->getConfig('app_messaging'));
               $this->instances['TextStream'] = TextStream::createInstance();

               if(file_exists($dotenv)){ 
                    
                    $this->dbservice->connect($dotenv);
               }

               $this->apphost = $this->instances['Request']->host();
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

        $this->hasCachedModels = FALSE;

        $this->dbservice = NULL; // called by __destruct to disconnect DB connection

        $this->envservice = NULL; // called by __desstruct to unset configs

        $this->resolver = NULL; // called by __destruct

        $this->instances = array(); // recover more memory (if any need be)

        $this->jheaders = NULL;

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
