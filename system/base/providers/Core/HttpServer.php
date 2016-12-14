<?php

namespace Providers\Core;

use Providers\Tools\TCPSocket as Socket;

class HttpServer {

   protected $serverSocket;

   protected $handler;	

   protected $maxConnections;

   public function __construct($ip_adress, $port){

       $this->serverSocket = new Socket($ip_adress, $port, "tcp");    

       $this->maxConnections = 11000;  
   }

   public function onConnection(callable $callback){

       $this->handler = $callback;

       $this->serverSocket->listen($this->handler, $this->maxConnections);

       // $ips = $this->serveSocket->getClientAddresses();

   }

   public function shutDown(){

   	    $this->serverSocket->drop();
   }

}


?>