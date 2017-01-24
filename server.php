<?php

require_once './app.php';

$port = 8888;

fwrite(STDOUT, "Jollof Mini Server Listening on Port: $port");

$server = new Providers\Core\HttpServer('127.0.0.1', $port);

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

});


?>

