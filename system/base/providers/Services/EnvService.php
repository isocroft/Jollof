<?php
/*!
 * Jollof Framework (c) 2016
 *
 * {EnvService.php}
 */

namespace Providers\Services;

class EnvService {

	      protected $configs;

        protected $appPaths;

        public function __construct(array $configs){

              $this->configs = $configs;

              $this->appPaths = new \stdClass();

              $this->setupAppEnvironment();

              $this->setupAppPaths();

              $this->setAppRawSockets();

			        $this->setAppMail();

        }

        public function getConfig($key){

           if(array_key_exists($key, $this->configs)){
                 return $this->configs[$key];
           }

           return NULL;
        }

        private function setAppRawSockets(){

           $sockets_enabled = $this->configs['app_connection']['sockets_enabled'];

           if ($sockets_enabled && !extension_loaded('sockets')) {
                  throw new \Exception("The Sockets Extension is required but not loaded.");
           }
        }

    		private function setAppMail(){

    		    $mail_settings = $this->configs['app_mails'];
    		}

        private function setupAppEnvironment (){

        	    $app_env = $this->configs['app_environment'];
              $can_upload = $this->configs['app_uploads']['uploads_enabled'];

                if($app_env == "prod"){

					           error_reporting(-1); // don't display PHP error on web page (still need to check this one)

					           ini_set("expose_php", "Off"); // remove PHP stamp from HTTP response Headers

        				}else if($app_env == "dev"){

        				     error_reporting(E_ALL); // | E_STRICT

        				}

                if(!$can_upload){

                    ini_set("file_uploads", "Off");
                }
        }

        private function setupAppPaths(){

                $app_pths = $this->configs['app_paths'];

                foreach ($app_pths as $key => $value) {
                    if(is_dir($value)){ // it must be a directory to be included as a valid application path
                         $this->appPaths->{$key} = $value;
                    }
                }

        }


        public function exposeEnvironment($root){

           $env_file = $this->appPaths->base . '.env';
           $app_key = '';
           if(file_exists($env_file)){
               // get the contents of the file
               $settings = file($env_file);

               // extract the application key
               foreach ($settings as $line){
                  $split = explode('=', $line);
                  if(index_of($split[0], 'app_') === 0){
                      $app_key = $split[1];
                  }
               }
           }

           $arr = array(
                  /* paths */
                  'app.path.base'=>$this->appPaths->base,
                  'app.path.upload'=>$this->appPaths->storage . '/cabinet/uploads/',
                  'app.path.download'=>$this->appPaths->storage . '/cabinet/downloads/',
                  'app.path.assets'=>$this->appPaths->public . '/assets/',
                  'app.path.storage'=>$this->appPaths->storage . '/',
                  'app.path.packages'=>$this->appPaths->packages . '/',
                  'app.path.views'=>$this->appPaths->views . '/',
                  'app.path.public'=>$this->appPaths->public,

                  /* app specifics */
                  'app.root'=> $root,
                  'app.key'=> $app_key,
                  'app.status' => $this->configs['app_environment'],
                  'app.settings.cookie'=> $this->configs['app_cookies']
             );

             return $arr;

        }


}

?>
