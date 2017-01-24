<?php

/*!
 * Jolloff Framework (c) 2016
 *
 * {NativeSessionService.php}
 */

namespace Providers\Services;

use \Contracts\Policies\SessionAccessInterface as SessionAccessInterface;


class NativeSessionService implements SessionAccessInterface {

    protected $sessionId;

    protected $sessionName;

    protected $sessionCacheExpires;

    protected $sessionCookieExpires;

    protected $sessionBag;

    protected $previousReqTime;

    protected $novelReqTime;

   /**
    *
    * Constructor
    *
    *
    *
    * @param void
    * @api
    */

	public function __construct($sessionName){

		$this->sessionCookieExpires = (time()+246000);

        $this->sessionCacheExpires = 10800; // max-age = 10800 seconds

        $this->sessionId = '';

        if(!$GLOBALS['app']->inCLIMode()){

               if($sessionName !== ""){
                    session_name($sessionName);
               }

               $this->sessionName = session_name();

			   $this->open();

			   $this->cacheRequestTime();

			   $this->setSessionCookie();
	    }
	}


	public function __destruct(){

		$this->close();
	}

	/**
	 *
	 *
	 *
	 *
	 *
	 * @param string $key
	 * @return bool
	 */

	public function hasKey($key){

        return array_key_exists($key, $_SESSION);
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

	public function getId(){

		return $this->sessionId;
	}

	/**
	 *
	 *
	 *
	 *
	 * @param void
	 * @return void
	 * @scope private
	 */

	private function cacheRequestTime(){
         $reqtime = array_key_exists('REQUEST_TIME', $_SERVER) ? $_SERVER['REQUEST_TIME'] : NULL;
         if(!isset($reqtime)){
         	$reqtime = time()-2; // just an estimation... no biggie!
         }

         $this->previousReqTime = intval($this->getSessionData('_lastreq'));

         if($this->previousReqTime === FALSE){
         	 $this->previousReqTime = 0;
         }
         $this->setSessionData('_lastreq', intval($reqtime));
         $this->novelReqTime = intval($reqtime);
	}

	public function setSessionCookie(){
        $params = $GLOBALS['env']['app.settings.cookie'];
$this->sessionId = (array_key_exists($this->sessionName, $_COOKIE))? $_COOKIE[$this->sessionName] : custom_session_id(TRUE);
        // regenerate id manually for large diff in request times (This handles browsers which don't support {httpOnly})
		 /*if(($this->previousReqTime - $this->novelReqTime) >= 400){
	           $this->sessionId = custom_session_id(TRUE);
	           session_id($this->sessionId); // manually inform PHP that the session id has been updated/regenrated!!
         }*/
         // overwrite what {session_start()} has done by manually resetting the cookie to enable settings
setcookie($this->sessionName, $this->sessionId, $this->sessionCookieExpires, '/', $params['domian_factor'], $params['secure'], $params['server_only']);
	}

	/**
     *
     *
     *
     * @param void
     * @return string
     * @api
     */

	public function getName(){

		return ($this->sessionName || session_name());
	}

	/**
	 * Put a value in the session.
	 *
	 * @param  mixed  $value
	 * @return void
	 */

	public function write($key, $value){

		return $this->setSessionData($key, $value);
	}

	/**
	 * Get the session value.
	 *
	 * @param string {$key}
	 * @return mixed
	 */

	public function read($key){

		return $this->getSessionData($key);
	}

	/**
	 * Remove all session values completely
	 *
	 * @param  string  $name
	 * @return bool
	 */

	public function destroy($name){

		if($name !== ''){

             $_SESSION = array(); // remove all session data only
			 session_destroy(); // remove the entire session cookie only

			 return TRUE;
		}

		return FALSE;
	}

	/**
	 * Erase a value in the session.
	 *
	 * @param  string $key
	 * @return void
	 */

	public function erase($key){

         return $this->forgetSessionData($key);
	}

	/**
	 * Starts the session.
	 *
	 * @param void
	 * @return void
	 */

	public function open(){

        // Interfering here to protect client user from XSS attack vectors -- via JavaScript document.cookie)
        $this->sessionBag = array();
        // lets' get the session ready
        if (session_id() == $this->sessionId){
session_cache_limiter('private_no_expire'); // enable caching of response on the client (disallow on proxies)
session_cache_expire($this->sessionCacheExpires); // setup Cache-Control to secs value (response meant for single user)
session_set_cookie_params(0); // tricking PHP actually ;)
session_start();
		}
	}

	/**
	 * Closes out the session using thread-safe method.
	 *
	 * @param void
	 * @return void
	 */

	public function close(){

		 session_write_close();
	}

	private function setSessionData($key, $value, $overwrite=TRUE){

		$_SESSION[$key] = serialize($value);
	}

	private function getSessionData($key){

		if ($this->hasKey($key) && isset($_SESSION[$key])){

			return unserialize($_SESSION[$key]);
		}

		return FALSE;
	}


	private function forgetSessionData($key){

		if ($this->hasKey($key) && isset($_SESSION[$key])){

			 unset($_SESSION[$key]);
		}

		return $this->hasKey($key);
	}

}

?>