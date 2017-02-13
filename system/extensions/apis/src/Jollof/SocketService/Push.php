<?php

namespace Jollof\SocketService;

use Ratchet\Wamp\WampServerInterface;
use Ratchet\ConnectionInterface;

class Push implements WampServerInterface {

	public $subscribedThreads = array();
	protected $redis;

	private $sessionName;

	public function __construct($sessionName){

        $this->sessionName = $sessionName;
	}

	public function timedCallback(){

		if(array_key_exists('debug', $this->subscribedThreads)){
			$thread = $this->subscribedThread['debug'];
			$thread->broadcast('timestamp', time());
		}

	}

	public function init($redis){

		$this->redis = $redis;
		echo "Connected To Redis, Now listening for incoming messages...";

	}

	public function onSubscribe(){

	}

	public function onPublish(){

	}

	public function onClose(){

	}

	public function onOpen(){

	}

	public function onCall(){ // Remote Procdure Call [RPC]

	}

	public function onError(){

	}
}

?>