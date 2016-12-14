<?php

/**
 * Jollof (c) Copyright 2016
 *
 *
 * {Encrypter.php}
 *
 */

namespace Providers\Tools;

class Encrypter {

       protected $algorithm = MCRYPT_BLOWFISH;

       protected $mode = MCRYPT_MODE_CBC;

       protected $iv;

       protected $key;
       
       public function __construct($app_key){

           $this->key = $app_key;

           $this->createIV();
              
       }

       private function createIV(){

 			$this->iv = mcrypt_create_iv($this->getIVSize(), MCRYPT_DEV_URANDOM);
       }

       public function encrypt($plain_text){

       		$payload = mcrypt_encrypt($this->algorithm, $this->key, $plain_text, $this->mode, $this->iv);

       		return $this->wrapPayload($payload); 
       }

       public function decrypt($encoded_text){

       		$cipher_text = $this->unwrapPayload($encoded_text);

       		return mcrypt_decrypt($this->algorithm, $this->key, $cipher_text, $this->mode, $this->iv);

       }

       private function wrapPayload($payload){

       		return base64_encode($payload);
       }

       private function unwrapPayload($payload){

       		return base64_decode($payload);
       }

       private function getIVSize(){

       	    return mcrypt_get_iv_size($this->algorithm, $this->mode);
       }
	
}

?>