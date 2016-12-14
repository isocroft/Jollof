<?php

namespace Providers\Tools;

class SocketConnection {

	protected $request;

	protected $response;

    protected $bucketSize;

    protected $bucket;

    protected $ip;

    protected $body;

    protected $query;

    protected $responseHeaders;

    protected $clientSocket;

    private $htmlTemplate = "<!DOCTYPE html>
        <html>
        <head>
        	<title>$[title]</title>
        	<meta charset='UTF-8'>
        </head>
        <body>
           $[body]
        </body>
        </html>";

    public function __construct($socket,  $remoteIp){

        $this->flush();

        $this->ip = $remoteIp;
        
        $this->clientSocket = $socket;

    }

    public function flush(){

    	$this->request = new \stdClass();

        $this->response = new \stdClass();

        $this->body = array();

        $this->query = array();
        
        $this->bucket = array();

        $this->responseHeaders = array();

    }

    public function getIp(){

    	return $this->ip;
    }

    public function getSocket(){

    	 return $this->clientSocket;
    }

    public function isAjax(){

    	$headers = $this->request->headers;

    	if(is_array($headers)){
    		return ($headers['X-Requested-With'] === 'XMLHttpRequest');
    	}

    	return false;
    }

    public function parseRequest($h){
         
         if($h === NULL){
         	return;
         }

    	 $o = array();
    	 $i = array();
    	 $pos = stripos($h, "\r\n");
    	 $x = substr($h, 0, $pos);
    	 $x = explode(" ", $x);
    	 $h = substr($h, $pos);
    	 $h = explode("\r\n", $h);

    	 if(!is_array($x) or !is_array($h)){
             $this->request->data = $h;
             return;
         }

    	 foreach ($x as $k => $y) { 
    	 	  switch ($k) {
    	 	  	case 0:
    	 	  	   $i['method'] = $y;
    	 	  	break;
    	 	  	case 1:
    	 	  	   $i['uri'] = $y;
    	 	  	break;
    	 	  	case 2:
    	 	  	   $i['version'] = $y;
    	 	  	break;
    	 	  }
    	 }

         foreach($h as $r){
         	 $exploded = explode(":", $r);
         	 if(stripos($r, "\r\n") === 0 || count($exploded) == 1){
                $this->request->rawBody = trim($r); // we have entered the enity body section of the request 
                break;
         	 }
         	 
             list($v, $l) = $exploded;
             if($v === null) continue;
             $o[$v] = trim($l);
         }

         $this->request->headers = $o;
         $this->request->details = $i;

         if(!array_key_exists('Content-Type', $this->request->headers)){
            if($this->request->details['method'] != 'GET'){
               $this->request->headers['Content-Type'] = 'text/plain';
            }
         }
    }

    public function getMethod(){

    	return $this->request->details['method'];
    }



    public function getURI(){

    	return urldecode(parse_url($this->request->details['uri'], PHP_URL_PATH));
    }

    public function getQuery(){

        $qs = urldecode(parse_url($this->request->details['uri'], PHP_URL_QUERY));
        parse_str($qs, $this->query);
        return $query;
    }

    public function getBody(){
       
       if(property_exists($this->request, 'rawBody')){
    	   switch($this->request->headers['Content-Type']){
    	   	    case 'application/json':
    	   	        $this->body = json_decode($this->request->rawBody, TRUE);
    	   	    break;
    	   	    case 'application/x-www-form-urlencoded':
                    parse_str($this->request->rawBody, $this->body);
    	   	    break;    
    	   }   
       }

       return $this->body;	
    }

    public function getHTTPVersion(){

    	return $this->request->details['version'];
    }

    public function getBucketSize(){

        return $this->bucketSize;
    }

    public function getHeaders(){

        return $this->request->headers;
    }

    public function setHeader($headerKey = NULL, $headerValue = NULL){
        
        if($headerKey !== NULL && $headerValue !== NULL){
          
           $this->responseHeaders[] = "$headerKey: $headerValue";
        }
    }

    public function setBucketSize($size){

    	$this->bucketSize = $size;
    }

    public function send($data, $statusCode, $contentType = "text/html; charset=UTF-8"){

        $status = $this->getHTTPVersion();
        switch($statusCode){
           case 200:
              $status .= " $statusCode OK";
           break;
           case 304:
              $status .= " $statusCode Not Modified";
           break;
           case 404:
              $status .= " $statusCode Not Found";
           break;
           case 401:
              $status .= " $statusCode Conflict";
           break;  
           case 403:
              $status .= " $statusCode Forbidden";
           break;
        }

         $this->setHeader("Content-Type", $contentType);
         $this->bucket[] = $status;

         $this->setToResponse($data, $contentType);
    }

    public function getResponse(){
        
         return $this->response;

    }

    private function setToResponse($entity_body, $contentType){

        $headers = implode("\r\n", array_merge($this->bucket, $this->responseHeaders));

        if(starts_with($contentType, "text/html;")){
        	$entity_body = preg_replace(array('/\$\[body\]/'), array($entity_body), $this->htmlTemplate);
        }

        $headers .= "\r\n\r\n" . $entity_body . (($contentType === "text/event-stream")? "\n" : "");

        $this->response->payload = $headers;

    }

}

?>