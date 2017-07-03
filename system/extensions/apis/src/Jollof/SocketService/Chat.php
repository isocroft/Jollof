<?php

namespace Jollof\SocketService;

/**
 * Jollof Framework - (c) 2016
 *
 *
 * @author Ifeora Okechukwu
 * @license    MIT License
 * @copyright   Mobicent, Ltd.
 * @link htps://github.com/isocroft/Jollof
 */

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {

    /**
     * @var array -
     */

    protected $clients;

    /**
     * @var array -
     */

    protected $clientSessions;	

    /**
     * @var string -
     */

    private $sessionName;

    /**
     * Constructor.
     *
     * @param void
     *
     * @scope private
     */

    public function __construct($sessionName){

        $this->clients = new \SplObjectStorage;
	    $this->clientSessions = new ArrayObject(array());

        $this->sessionName = $sessionName;

    }

    public function onOpen(ConnectionInterface $conn) {

	$cookies = (string) $conn->WebSocket->request->getHeader('Cookie');
	$cookies = array_map('trim', explode(';', $cookies));
	$sessionId;

	foreach($cookies as $cookie){

		if(!strlen($cookie)){
			continue;
		}

		list($cookieName, $cookieValue) = explode('=', $cookie, 2) + [NULL, NULL];

		if(empty($cookieName) || empty($cookieValue)){
			continue;
		}

		if($cookieName !== $this->sessionName){
			 continue;
		}

		$sessionId = urldecode($cookieValue);
		break;		
	}

	if(!$sessionId){

		return $conn->close();
	}

        // Store the new connection to send messages to later
        $this->clients->attach($conn);
	    $this->clientsSessions[$conn->resourceId] = $sessionId;

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function init($redis){

	echo "Connected to Redis, now listening for incoming messages... \n";

	$redis->pubSubLoop('chat', function($event){

		echo "Recieved message {$event->payload} from {$event->channel}";
		
		foreach($this->wsclients as $wsclient){

			$wsclient->send($event->payload);

		}

	});

    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}

?>