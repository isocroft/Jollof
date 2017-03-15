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
			$thread = $this->subscribedThreads['debug'];
			$thread->broadcast('timestamp', time());
		}

	}

	public function init($redis){

		$this->redis = $redis;
		
		fwrite(STDOUT, "Connected To Redis, Now listening for incoming messages...");

	}

	public function onSubscribe(ConnectionInterface $conn, $thread){

		 fwrite(STDOUT, "on Subscribe => {$conn->WAMP->sessionId}");

		 if(!array_key_exists($thread->getId(), $this->subscribedThreads)){
		 	 $this->subscribedThreads[$thread->getId()] = $thread;
		 	 $pubSubContext = $this->redis->pubsub($thread->getId(), array(&$this, 'pubsub'));

		 }
	}

	public function onUnSubscribe(ConnectionInterface $conn, $topic){

		fwrite(STDOUT, "UnSubscribed Topic: $topic {$topic->count()}");
	}

	public function pubsub($event, $pubsub){

		if(!array_key_exists($event->channel, $this->subscribedThreads)){

			return;
		}

		$thread = $this->subscribedThreads[$event->channel];
		$topic->broadcast("{$event->channel}:{$event->payload}");
		// quit if we get the message from Redis
		if(strtolower(trim($event->payload)) === 'quit'){
			$pubsub->quit();
		}
	}

	public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible){

		$topic->broadcast("$topic:$event");
	}

	public function onClose(ConnectionInterface $conn){

		fwrite(STDERR, "{$conn->WAMP->sessionId} Close:");
	}

	public function onOpen(ConnectionInterface $conn){

		fwrite(STDERR, "{$conn->WAMP->sessionId} Open:");
	}

	public function onCall(ConnectionInterface $conn, $id, $topic, array $params){ // Remote Procdure Call [RPC]

		$conn->callError($id, $topic, 'Forbidden [not allowed]')->close();
	}

	public function onError(ConnectionInterface $conn, \Exception $e){

		fwrite(STDERR, "{$conn->WAMP->sessionId} Error: {$e->getMessage()}");
	}
}

?>