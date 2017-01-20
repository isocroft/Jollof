<?php

namespace Providers\Tools;

use Providers\Tools\SocketConnection as SocketConnection;

class TCPSocket {

	const STREAM_SOCKET = 1;

	const SOCKET_EXTENSIONS = 2;

	protected $ip_address_scheme = '/^[\d]{3}(?:(\.[\d]{1,3}){3})$/';

	protected $ip_address;

	protected $port;

	protected $socket_type;

	protected $socket;

	protected $remotes;

	protected $reads;

	protected $writes;

	protected $minReadBytes;

	protected $maxReadBytes;

	protected $maxWriteBytes;

	protected $protocol;

    protected $errNo;

    protected $errMsg;

	protected $socketStarted;

	protected $connections;


	public function __construct($address, $port, $protocol){

          if(!preg_match($this->ip_address_scheme, $address)){
          	   $address = gethostbyname($address);
          }

          $this->ip_address = $address;

          if(!is_int($port)){
          	 $port = intval($port);
          }

          $this->port = $port;

          $this->remotes = array();

          $this->minReadBytes = 1024; // bytes

	      $this->maxReadBytes = 8192; // bytes

	 	  $this->maxWriteBytes = 20240; // bytes

          $this->socketStarted = false;

          if(!isset($protocol)){

          	 $this->protocol = "unix"; // "udp"

          }else{

          	 $this->protocol = $protocol;
          }

          $this->connections = array();
	}

	private function start(){

		  set_time_limit(0);

		  $context = stream_context_create();

		  $this->errMsg = $this->errNo = NULL;

          //stream_context_set_option(stream_or_context, wrapper, option, value); FOR SSL

          $this->socket = stream_socket_server($this->protocol . "://" . $this->ip_address . ":" . $this->port, $this->errNo, $this->errMsg, \STREAM_SERVER_BIND | \STREAM_SERVER_LISTEN);/* , $context); FOR SSL */

          if($this->socket === false){

          	    return FALSE;
          }

          $this->reads = $this->writes = array($this->socket);

          $this->socketStarted = TRUE;

          stream_set_blocking($this->socket, 0); // set to non-blocking mode

          return $this->socketStarted;
	}


    private function clean(){
    	$ipList = array();
        foreach($this->connections as $connection){
	    	$socket = $connection->getSocket();
		    if(!is_resource($socket)){
                 fwrite();
			 	 $ipList[] = $connection->getIp();
			}
		}

		foreach ($ipList as $ip) {
			 fwrite(STDOUT, "s " . count($ipList))
			 try{
		 	    unset($this->connections[$ip]);
		 	 }catch(\Exception $e){}
		}
	}

	private function read($connection){

		 $read = $socket = NULL;

		 if($connection === $read){

		 	 return $read;
		 }

		 $socket = $connection->getSocket();

         if(count($this->reads) && in_array($socket, $this->reads)){
         	  $read = '';
              while(!feof($socket) && empty($read)){

		         $read .= fread($socket, $this->minReadBytes);

		      }
		 }

		 return $read;
	}

	public function getClientAddresses(){

		return $this->remotes;
	}

	private function getConnectionsCount(){

		return count($this->connections);
	}

	public function listen(callable $handle, $maxConnections){

          $continue = $this->start();

          while($continue){

          	   if($this->getConnectionsCount() >= $maxConnections){
                    // the server has reach and/or exceeded its carrying capacity...
          	   	    break;
          	   }

               $continue = $this->blockAndWait($handle);

               $this->clean();
          }


          if(!$this->socketStarted){

                $error = new \UnexpectedValueException($this->errNo . ": Could not create socket: " . $this->errMsg);
                fwrite(STDERR, $error->getMessage());
                throw $error;

          }else{

          	   ;
          }

    }

    public function blockAndWait(callable $handle){


            if(!is_resource($this->socket)){

            	 return false;
            }

            $remoteIp = NULL;
            $client = NULL;
            $timeout = 1337; // seconds
            $localIp = stream_socket_get_name($this->socket, FALSE);
            /* check if ther's a new connection waiting to be accepted (non-blocking mode with {$timeout} = 0 or higher) --- (blocking mode with {$timeout} = -1) */

            $changed = @stream_select($this->reads, $this->writes, $this->writes, $timeout);

  	   	    if($changed === FALSE){

  	   	    	 $this->reads = $this->writes = array($this->socket);

		         fwrite(STDOUT, "Hello Bad Worldy" . PHP_EOL);

  	   	   	     return true;
  	   	    }

  	   	    for($i = 0; $i < $changed; $i++){

                if($this->reads[$i] === $this->socket){
		            // this line below blocks the thread of execution until a client socket connects or it times out!
		            // this times out immediatelly because of the 0 as second argument
				  	$client = @stream_socket_accept($this->socket, -1, $remoteIp); // "-1"


                    // if {$client} socket doesn't connect or the socket times out, {$client} equals NULL
				  	if(isset($client)){

				  	      $this->reads[] = $client;

				  	      if(!isset($remoteIp)){
		  			            $remoteIp = stream_socket_get_name($client, TRUE);
		  		          }

					  	  // if no {$client} socket connects or the socket times out, {$remoteIp} is never set
					  	  if(isset($remoteIp)){
					  	       array_push($this->remotes, $remoteIp);
					  	       $this->connections[$remoteIp] = new SocketConnection($client, $remoteIp);
			              }

				  	}

				}

			}

		  	if(count($this->connections) > 0){

		  	   	foreach($this->connections as $connection){

                          // set the write size in bytes
	                      $connection->setBucketSize($this->maxWriteBytes);

	                      // read all information in the HTTP request headers or any data at all.
	                      $connection->parseRequest($this->read($connection));

	                      // we intentionally block the client socket here to allow {$handle} below execute properly
	                      // we aren't blocking the thread of execution on the server here however
	                      //stream_set_blocking($connection->getSocket(), true);

	                      $handle($connection);

	                      // we unblock the client socket so it can prepare to recieve data
	                      //stream_set_blocking($connection->getSocket(), false);

	                      $this->write($connection);

	                      $this->finish($connection);
                }
		  	}

		    return true;
	}

	public function drop(){

		 fclose($this->socket);

		 if(is_resource($this->socket)){
		 	 stream_socket_shutdown($this->scoket, \STREAM_SHUT_RDWR);
		 }
	}

	private function write($connection){

		$data = NULL;
		$socket = $connection->getSocket();

        //if(count($this->writes) && in_array($socket, $this->writes)){

            $data = $connection->getResponse()->payload;

            fwrite(STDOUT, $data);

    		fwrite($socket, $data);

    		$connection->flush();

		    fclose($socket);
		//}

	}

	private function finish($connection){

        $socket = $connection->getSocket();
        if(is_resource($socket)){
		 	stream_socket_shutdown($socket, \STREAM_SHUT_RDWR);
		}
        // {stream_select} modifies the contents of {$this->reads} & {$this->writes} so... we reset them back to their initial state
        $this->reads = $this->writes = array($this->socket);
	}

      /*

       $address="127.0.0.1";
$port="3222";
$sock = socket_create(AF_INET,SOCK_STREAM,0) or die("Cannot create a socket");
socket_bind($sock,$address,$port) or die("Couldnot bind to socket");
socket_listen($sock) or die("Couldnot listen to socket");
$accept=socket_accept($sock) or die("Couldnot accept");
$read=socket_read($accept,1024) or die("Cannot read from socket");
echo $read;
socket_write($accept,"Hello client");
socket_close($sock);

      */

}


?>