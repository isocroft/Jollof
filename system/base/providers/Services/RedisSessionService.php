<?php

/*!
 * Jollof Framework (c) 2016
 * 
 * {RedisSessionService.php}
 *
 */

namespace Providers\Services;


use \Contracts\Policies\SessionAccessInterface as SessionAccessInterface;
use \Providers\Tools\RedisStorage as RedisStorage;
use \Providers\Tools\Encrypter as Encrypter;

class RedisSessionService implements SessionAccessInterface {

    protected $driver;

    protected $sessionId;

    protected $sessionName;

    protected $gabageCollectTimeout;

    protected $sessionCacheExpires;

    protected $sessionCookieExpires;

    protected $storage;

    protected $sessionBag;

    protected $previousReqTime;

    protected $novelReqTime;

    protected $encrypter;

	public function __construct($sessionName, $sessionHost, $sessionPort){

        $this->sessionId = NULL;

        $this->sessionName = $sessionName;

        $this->sessionBag = array();

        $this->storage = new RedisStorage($sessionHost, $sessionPort);

        $this->encrypter = new Encrypter($GLOBALS['env']['app.key']);
 
		if(!$GLOBALS['app']->inCLIMode()){

               if($sessionName !== ""){
               		$this->sessionName = $sessionName;
               }

			   $this->setSessionCookie(); 

			   $this->open();

			   $this->cacheRequestTime();
	    }
	}

	
	public function __destruct(){
		
		$this->close();
	}

	public function hasKey($key){

        return array_key_exists($key, $this->sessionBag);
	}

	/**
	 *
	 *
	 * @param void
	 * @return void
	 */

	public function cacheRequestTime(){
         $reqtime = array_key_exists('REQUEST_TIME', $_SERVER) ? $_SERVER['REQUEST_TIME'] : NULL;
         if(!isset($reqtime)){
         	$reqtime = time()-2; // just an estimation... no biggie!
         }
         
         $this->previousReqTime = intval($this->getSessionData('_lastreq'));
         
         if($this->previousReqTime === FALSE){
         	 $this->previousReqTime = 0;
         }
         $this->setSessionData('_lastreq', $reqtime);
         $this->novelReqTime = $reqtime;
	}

	/**
     *
     *
     * @param void
     * @return string 
     */
	
	public function getId(){

		return $this->sessionId;
	}
    
    /**
     *
     *
     *
     * @param void
     * @return string 
     */
	
	public function getName(){

		return $this->sessionName;
	}

	/**
	 * Starts the session.
	 *
	 * @param void
	 * @return void
	 */

	public function open(){

		$sessionId = $this->encrypter->decrypt($this->sessionId);

		try{
        
        	$this->sessionBag = unserialize($this->storage->get($sessionId));

        }catch(\Exception $e){

        	$this->sessionBag = array();
        }
	}

    public function setSessionCookie(){
        $params = $GLOBALS['env']['app.settings.cookie'];
$this->sessionId = (array_key_exists($this->sessionName, $_COOKIE))? $_COOKIE[$this->sessionName] : $this->encrypter->encrypt(custom_session_id(TRUE));
        // regenerate id manually for large diff in request times (This handles browsers which dont support {httpOnly})
		 /*if(($this->previousReqTime - $this->novelReqTime) >= 400){ 
	           $this->sessionId = custom_session_id(TRUE);
	           session_id($this->sessionId); // manually inform PHP that the session id has been updated/regenrated!!
         }*/
setcookie($this->sessionName, $this->sessionId, $this->sessionCookieExpires, '/', $params['domian_factor'], $params['secure'], $params['server_only']);
	}

	/**
	 * Closes out the session by writing
	 * out to the stroage server (Redis)
	 *
	 * @param void
	 * @return void
	 */

	public function close(){

		$sessionId = $this->encrypter->decrypt($this->sessionId);

        $this->storage->set($sessionId, serialize($this->sessionBag));
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

		if($name == ''){

			return FALSE;
		}

		$sessionId = $this->encrypter->decrypt($this->sessionId);

		return $this->storage->del($sessionId);
	}

	/**
	 * Erase a value in the session.
	 *
	 * @param  string $key
	 * @return void
	 */

	public function erase($key){


	}

	private function setSessionData($key, $value, $overwrite=TRUE){

		$this->sessionBag[$key] = serialize($value);
	}

	private function getSessionData($key){

		if ($this->hasKey($key) && isset($this->sessionBag[$key])){

			return unserialize($this->sessionBag[$key]);
		}

		return FALSE;
	}

	
	private function forgetSessionData($key){

		if ($this->hasKey($key) && isset($this->sessionBag[$key])){

			 unset($this->sessionBag[$key]);
		}

		return $this->hasKey($key);
	}

}

?>