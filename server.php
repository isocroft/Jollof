<?php

require_once './app.php';

$port = 8888;

/*$server = new Providers\Core\HttpServer('127.0.0.1', $port);

$server->onConnection(function(&$connection) use ($env){

	 $headers = $connection->getHeaders();
	 $method = $connection->getMethod();
	 $uri = $connection->getURI();
	 $modified = false;

	 fwrite(STDOUT, "$uri : $method: \r\n\n  " . implode(PHP_EOL, $headers));

     if(array_key_exists('Cookie', $headers)){
         $modified = Session::setCookieValue($headers['Cookie']); // only to be used when the driver is "redis"
     }

	 $requested = $env['app.path.public'] . $uri;

	 if(!$modified){
	 	$connection->send("You are not allowed to access this service", 200, "text/plain");
		return false;
	 }

	 if(file_exists($requested)){
	     $connection->send("You are not allowed to access this route", 403);
		 return false;
	 }

	 if(!Auth::check(Route::currentRoute())){
	 	 $connection->send("You are not allowed to access this route", 403);
		 return false;
	 }

     switch($method){
     	case "GET":
     	    $query = $connection->getQuery();
     	    $connection->setHeader("Connection", "keep-alive");
     	    $connection->setHeader("Keep-Alive", "timeout=15 max=100");
     	    $connection->setHeader("Cache-Control", "no-cache");
            $connection->send('data: {"id":"_9fc40a0223445"}', 200, "text/event-stream");
     	break;
     	case "POST":
     	    $body = $connection->getBody();
     	    if($connection->isAjax()){
                $connection->send('{"record":"_8ae0c31d23445"}', 200, "application/json");
            }else{
                $connection->send('Hello World', 200);
            }
     	break;
     }

});*/


use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Wamp\WampServer;

use Predis\Async\Client;

use Jollof\SocketService\Chat;
use Jollof\SocketService\Push;


$args = $_SERVER['argv'];

$args = array_slice($args, 1);


switch ($args[0]) {
    case 'chat':
    $chat = new Chat($env['app.session.name']);

    $server = IoServer::factory(
     new HttpServer(
         new WsServer(  
            $chat
         )  
     ),
            $port
    );

    $redis = new Client('tcp://127.0.0.1:6379', $server->loop);
    $redis->connect(function ($redis) use ($chat) {
        
        echo "Connected to Redis, now listening for incoming messages...\n";

        $chat->init($redis);
    });

    $server->run();

    break;
    case 'push':

    $loop = \React\EventLoop\Factory::create();
    $push = new Push($env['app.session.name']);

    $redis = new Client('tcp://127.0.0.1:6379', $loop);
    $redis->connect(function ($redis) use ($push) {
        
        echo "Connected to Redis, now listening for incoming messages...\n";

        $push->init($redis);
    });


    $webSock = new \React\Socket\Server($loop);
    $webSock->listen($port, '127.0.0.1');

    $webServer = new Ratchet\Server\IoServer(
        new Ratchet\WebSocket\WsServer(
            new Ratchet\Wamp\WampServer(
                $push
            )
        ),
        $webSock
    );

    $loop->run();
    break;
    default:

        return;

    break;    
}

echo "Jollof [Ratchet] Socket Server Listening on Port: $port \n";

?>

