<?php

namespace Providers\Core;
 
 /*!
  *
  * Jollof (c) Copyright 2016
  *
  *
  * {HttpClient.php}
  *
  */
  
class HttpClient {

        const HTTP_PACKETING = TRUE;
		
        private $HTTP_HOST = '';
        private $HTTP_OPTIONS = array('RETURN_HTTP_HEADERS'=>1,'RETURN_HTTP_RESPONSE'=>TRUE);
        private $port = 0;
        private $method = "";
        private $recieve = NULL;

        private $ch = NULL;
        private $headers = NULL;

        public function __construct($HTTP_HOST, $HTTP_PORT, $HTTP_METHOD="GET",array $HTTP_OPTS = array()){

            if(!ends_with($HTTP_HOST, '/')){
                $HTTP_HOST .= '/';
            }

		        $this->HTTP_HOST = $HTTP_HOST;
		        $this->HTTP_OPTIONS = array_merge($this->HTTP_OPTIONS, $HTTP_OPTS);
			      
            $this->port = $HTTP_PORT;
            $this->method = $HTTP_METHOD;
            $this->headers = array('Expect:', 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8');

            $this->ch = curl_init();
        }

        public function setCookie(array $cookieKeys = array()){
            $cookie = '';
            if(!is_array($cookieKeys)){
                 $cookieKeys = array();
            }
            foreach ($_COOKIE as $ckey => $cvalue) {
                  if(in_array($ckey, $cookieKeys)){
                        $cookie .= (strlen($cookie) == 0 ? '' : ';';
                        $cookie .= $ckey . '=' $cvalue 
                  }
            }
            curl_setopt($this->ch, CURLOPT_COOKIE, $cookie);
        }

        public function setHeaders(array $headers = array()){
            if(!is_array($headers)){
               $headers = array();
            }
            $merged = array_merge($this->headers, $headers);
            $this->headers = $merged;
        }

        public function setMethod($method){
              $methods = array('GET', 'POST', 'PUT', 'PATCH', 'HEAD', 'DELETE');
              if(in_array($method, $methods)){
              
                  $this->method = $method;
              }
        }

        public function setRequest($pathname, $client_id, $client_data){
   
             curl_setopt($this->ch, CURLOPT_URL, ($this->HTTP_HOST . $pathname));
             curl_setopt($this->ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
             curl_setopt($this->ch, CURLOPT_HEADER, $this->HTTP_OPTIONS['RETURN_HTTP_HEADERS']);
	           curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->headers); 
             if($this->port != 80){   
                curl_setopt($this->ch, CURLOPT_PORT, $this->port);
             }
             curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 2);
               
             if($this->method == "GET"){
		            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, $this->HTTP_OPTIONS['RETURN_HTTP_RESPONSE']);
                curl_setopt($this->ch, CURLOPT_GET, true);
             }else{
                curl_setopt($this->ch, CURLOPT_POST, true);
             }
 
             $pf = array('client_id'=>$client_id);

              foreach($client_data as $key => $val){
                 $pf[$key] = $val;
              }

              if($this->method == "POST"){
                 curl_setopt($this->ch, CURLOPT_POSTFIELDS, $this->http_build_query($pf));
		              //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
              }
             
              $this->recieve = curl_exec($this->ch);
             
              curl_close($this->ch);
        }

        public function setAsStale($flag = TRUE){

            if($flag === TRUE){
                curl_setopt($this->ch, CURLOPT_FORBID_REUSE, 1);
                curl_setopt($this->ch, CURLOPT_FRESH_CONNECT, $flag);
            }else if($flag === FALSE){
                curl_setopt($this->ch, CURLOPT_FORBID_REUSE, 0);
                curl_setopt($this->ch, CURLOPT_FRESH_CONNECT, $flag);
            }
        }

        public function setAsSecure(){

            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 2);
        }

        public function getResponse(){

             return $this->recieve;
        }

        private function http_build_query($arr){
             $str = "";
             if(function_exists('http_build_query')){
                return http_build_query($arr);
             }
             foreach($arr as $key => $val){
               if(is_array($val)){
                 $str .= $this->http_build_query($val);
               }else{
                 $str .= $key . "=" . $val . "&";
               }
             }
             $str = substr($str, 0, strlen($str)-1);
             return $str;
        }
  }

?>
