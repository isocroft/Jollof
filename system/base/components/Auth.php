<?php

/*!
 * Jollof Framework (c) 2016
 *
 * {Auth.php}
 *
 */

use \Session;
use \Request;
use \Response;
use \Helpers;

use \User;
use \UserRole;
use \UserThrottle;

use Providers\Tools\LoginThrottle as Throttle;
use Providers\Tools\Hasher as Hasher;

// use Providers\Tools\AuthContext as AuthContext;

final class Auth {

     /**
      * @var Auth
      */ 

     private static $instance = NULL;

     /**
      * @var array
      */ 

     protected $loginFields;

     /**
      * @var array -
      */ 

     protected $options;

     /**
      * @var string - the name for the Auth JWT cookie name
      */ 

     protected $JWTCookieName;

     /**
      * @var array - all routes that dont have an access-control guard 
      */ 

     protected $guestRoutes;

     /**
      * @var Providers\Tools\LoginThrottle - 
      */ 

     protected $throttle;

     /**
      * @var Providers\Tools\Hasher
      */ 

     protected $hasher;

     /**
      * @var string - IP address of the client machine
      */ 

     protected $clientIP;

     /**
      * Constructor
      *
      *
      * @param void
      * @api
      */

     private function __construct(array $options){

         $this->loginFields = array('email', 'password', 'username', 'user_id');

         $this->clientIP = (Request::ip() || '0.0.0.0');

         $this->options = $options;

         $this->actionMap = array(
             'GET' => 'read',
             'POST' => 'write',
             'PUT' => 'write',
             'DELETE' => 'write'
         );

         // These routes can be accessed only if the user is not logged in.
         $this->guestRoutes = $this->options['guest_routes'];

         $this->JWTCookieName = '_jollof_ic'; # Jollof Identity Carrier

         $this->hasher = new Hasher();

         if(!$GLOBALS['app']->inCLIMode()){

                 $this->logGuest();

                 if($this->options['throttle_enabled']){

                      $this->throttle = new Throttle(UserThrottle::$class, Session::id());
                 }
          }

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

     public static function createInstance(array $options){

          if(static::$instance == NULL){

               static::$instance = new Auth($options);
               return static::$instance;
          }

     }

     /**
      * 
      *
      *
      *
      * @param array $props
      * @return array
      * @api
      */

     public static function user(){

         $session = Session::get("accessLogin");

         if(!is_array($session)){

              $session = array();
         }

         if(array_key_exists('info', $session)){

              return $session['info'];
         }

         return NULL;
     }

     /**
      * 
      *
      *
      *
      * @param array $props
      * @param string $role
      * @return bool
      * @api
      */

     public static function register(array $props = array(), $role = 'user'){

          if(isset($props['password'])){

               $props['password'] = $this->hasher->hash($props['password']);
          }

          $isOk = User::create($props);

          if(is_array($isOk)){
              UserRole::create(array('user_id' => $isOk['pkey'], 'role' => $props['role'] || $role));
          }

          return $isOk['pkey'];
     }

     /**
      * 
      *
      *
      *
      * @param void 
      * @return bool
      */

     private function hasSession(){

          $isLogged = Session::has("accessLogin");
          $hasSignedCookie = ($this->options['jwt_enabled'])? Request::hasCookie($this->getJWTCookieName()) : TRUE;

          return ($isLogged && $hasSignedCookie);
     }

     /**
      * 
      *
      *
      *
      * @param void
      * @return array
      */

     public function getGuestRoutes(){

        return $this->guestRoutes;
     }

     /**
      * 
      *
      *
      *
      * @param void
      * @return string $action
      */

     private function fetchRequestAction(){

         $method = Request::method();

         $action = '';

         if(array_key_exists($method, $this->actionMap)){

             $action = $this->actionMap[$method];
         }

         return $action;
     }

     /**
      *
      *
      *
      *
      * @param void
      * @param array
      */

     private function getLoginFields(){

         return $this->loginFields;
     }

     private function getJWTCookieName(){

         return $this->JWTCookieName;
     }

     /**
      *
      *
      *
      *
      * @param void
      * @param string
      */

     private function getClientIP(){

         return $this->clientIP;
     }

     /**
      *
      *
      *
      *
      * @param string $issuer
      * @return void 
      */

     private function logGuest($issuer = 'JOLLOF_AUTH'){

          $hasSession = $this->hasSession();

          $props = array(
              'iss' => $issuer,
              'sub' => 'guest_54773973132539',
              'jti' => Helpers::randomCode(20),
              'routes' => '{}'
          );

          if(!$hasSession){

              $secret = Helpers::generateRandomByPattern("xxxxxxxxyxxxxxxxyxxxxxxxxxyy");

              Session::put("accessLogin", array('id' => 'guest_54773973132539', 'jwt_secret' => $secret, 'role' => 'Guest'));

              if($this->options['jwt_enabled']){

                   Response::setCookie($this->getJWTCookieName(), Helpers::createJWT($props, $secret));
              }
          }
     }

    /**
     * Automatically log user in
     *
     *
     *
     * @param string/integer $id
     * @param array $details
     * @return void
     * @api
     */


    public static function auto($id, array $details){

        $session = NULL;

        $clause = NULL;

        $hasSession = Session::has("accessLogin");

        if(!is_null($id)){

           $secret = Helpers::generateRandomByPattern("xxxxxxxxyxxxxxxxyxxxxxxxxxyy");

           $clause = array('user_id' => array('=' , $id));

           $permissions = UserRole::whereBy($clause, array('role', 'permissions'));

           $props = array(
                'iss' => "JOLLOF_AUTH",
                'sub' => "user_" . $id,
                'jti' => Helpers::randomCode(20),
                'routes' => $permission['permissions'],
           );

           if($hasSession){

                $session = Session::get("accessLogin");

                Session::forget("accessLogin");

                $session['id'] = "user_" . $id;

                $session['info'] = $details;
                $session['jwt_secret'] = $secret;
                $session['role'] => $permission['role'];
           }

           Session::put("accessLogin", $session);

           Response::setCookie(static::$instance->getJWTCookieName(), Helpers::createJWT($props, $secret));

        }
    }



    /**
     * Checks if a given route is allowed to access the  
     * request route {$routeToPermit} and whether or not 
     * the route can be accessed without an active user
     * session
     *
     * @param string $routeToPermit
     * @return bool
     * @api
     */

    public static function check($routeToPermit = NULL){

           $hasSession = static::$instance->hasSession();

           if($routeToPermit === NULL){

               return $hasSession;
           }

            $session = Session::get('accessLogin');

            $cookie = Request::getCookie(static::$instance->getJWTCookieName());

            $cookieArray  = Helpers::parseJWT($cookie);

            $validStatus = Helpers::validateJWT($cookieArray, $session['jwt_secret']);

            $payload = $validStatus;

            if((!array_key_exists('sub', $payload))
                || ($payload['sub'] !== $session['id'])){

                //throw new \Exception("Jollof Identity Carrier has been tampered with");
                return FALSE;
            }


            if($validStatus !== NULL){

                // check whether this session is for a guest or a user

                $type = static::$instance->getSession($session['id']);

                $action = static::$instance->fetchRequestAction();

                $userRoutes = json_decode($payload['userPermissons'], TRUE);

                if(array_key_exists($action, $userRoutes)){
                    $userPermittedRoutes = $userRoutes[$action];
                }else{
                    $userPermittedRoutes = array();
                }

                $allRoutes = array_merge(static::$instance->getGuestRoutes(), $userPermittedRoutes);

                switch($type){
                    case 'guest':
                    case 'user':
                      if(in_array($routeToPermit, $allRoutes)){
                          return ($type == 'user');
                      }
                    break;
                }
            }

            return FALSE;

     }

     /**
      * 
      *
      *
      *
      * @param void
      * @return string $role
      */

     public function getUserRole(){

        $hasSession = $this->hasSession();

        $role = '';

        if($hasSession){

             $session = Session::get("accessLogin");

             $role = $session['role'];
        }

        return $role;

     }

     /**
      * Creates an active login session for the user (via OAuth).
      *
      *
      *
      * @param array $token
      * @return bool
      * @api
      */

     public static function oauth($token){

     }

     /**
      * Creates an active login session for the user.
      *
      *
      *
      * @param array $fields
      * @return bool
      * @api
      */

     public static function login(array $fields = array()){

        $hasSession = static::$instance->hasSession();

        $throttle = NULL;

        $throtts = array();

        $credentials = array();

        if(static::$instance->options['throttle_enabled']){

            $throttle = static::$instance->getThrottle();
        }    

        if($hasSession){

            $session = Session::get("accessLogin");

            Session::forget("accessLogin");

            if(isset($throttle)){

                $throttle->updateSessionDataStore($session);

            }    
        }

        Session::put("accessLogin", $session);

        if(isset($throttle)){

              if(!$throttle->isUserBanned()){

                    $credentials = static::$instance->getUserCredentials($fields);

              }

        }else{

              $credentials = static::$instance->getUserCredentials($fields);

        }      


        if(!is_null($credentials['id'])){

           $secret = Helpers::generateRandomByPattern("xxxxxxxxyxxxxxxxyxxxxxxxxxyy");

           $clause = array('user_id' => array('=' , $credentials['id']));

           $permissions = UserRole::whereBy($clause, array('role', 'permissions'));

           $props = array(
                'iss' => "JOLLOF_AUTH",
                'sub' => "user_" . $credentials['id'],
                'jti' => Helpers::randomCode(20),
                'routes' => $permission['permissions'],
           );

           if($hasSession){

                $session = Session::get("accessLogin");

                Session::forget("accessLogin");

                $session['id'] = "user_" . $credentials['id'];

                unset($credentials['id']);

                $session['info'] = $credentials;
                $session['jwt_secret'] = $secret;
                $session['role'] => $permission['role'];
           }

           Session::put("accessLogin", $session);

           Response::setCookie(static::$instance->getJWTCookieName(), Helpers::createJWT($props, $secret));

        }else{

            # more code ...
            if(isset($throttle)){

                $throtts = array(
                      'throttle_id' => static::getThrottleId(),
                      'ip_address' => static::$instance->getClientIP(),
                      'user_id' => $credentials['id']
                );

                // UPDATE ON DUPLICATE KEY
                $throttle->setAttempt($throtts, array('throttle_count')); 

                if($throttle->isAttemptLimitReached()){

                      $throttle->ban(array('banned' => 1), $throtts);
                }
            }     

        }

        return (Session::has("accessLogin"));
     }

     /**
      *
      *
      *
      *
      *
      * @param string $id
      * @return void
      */

     public static function setThrottleId($id){

          static::$instance->throttle->setThrottleId($id);
     }

     /**
      * 
      *
      *
      * @param string $url
      * @return bool
      * @api
      */

     public static function willBeReturnedToURL($url = ''){

         $hasSession = static::$instance->hasSession();

         $session = array();

         if($hasSession){

              $session = Session::get("accessLogin");

         }

         return (array_key_exists('return_to_url', $session) && $url === $session['return_to_url']);

     }

     /**
      * 
      *
      *
      *
      * @param void
      * @return string
      * @api
      */

     public static function getReturnToURL(){

         $session = Session::get("accessLogin");

         $url = (array_key_exists('return_to_url', $session))? $session['return_to_url'] : "";

         unset($session['return_to_url']);

         Session::forget("accessLogin");

         Session::put("accessLogin", $session);

         return $url;
     }

     /**
      * 
      *
      *
      *
      * @param void
      * @return Provider\Tools\LoginThrottle
      */

     private function getThrottle(){

         return $this->throttle;
     }

     /**
      * 
      *
      *
      *
      * @param string $route
      * @return void
      */

     public function setReturnToUrl($route){

            $session = Session::get("accessLogin");

            $session['return_to_url'] = $route;

            Session::forget("accessLogin");

            Session::put("accessLogin", $session);
     }

     /**
      * 
      *
      *
      *
      * @param string $id
      * @param bool $asType
      * @return string
      */

     private function getSession($id, $asType = TRUE){

           $bits = explode('_', $id);

           $session = ($asType)? $bits[0] : $bits[1];

           return $session;

     }

     /**
      * 
      *
      *
      *
      *
      * @param array $fields
      * @return array
      */

     private function getUserCredentials(array $fields = array()){

        $loginFields = $this->getLoginFields();

        // validate
        foreach ($fields as $fkey => $fvalue) {
              if(!array_key_exists($fkey, $loginFields)){
                  unset($fields[$fkey]);
              }
        }

        /*if(count($fields) != count($loginFields)){

            $fields = array_slice($fields, 0, 2, TRUE);
        }*/

        $compositeFieldValues = array();

        array_walk($fields, function($value, $key){

            if($key == 'password'){

                 $value = $this->hasher->hash($value);
            }

            $compositeFieldValues[] = array('=', $value);
        });

        $credentials = User::whereBy(array_combine($fields,  $compositeFieldValues));

        return (count($credentials) > 0)? $credentials[0] : array('id' => NULL, 'email' => NULL);

     }

}

?>
