<?php

/*!
 * Jollof Framework (c) 2016 - 2017
 *
 * {Hasher.php}
 *
 */

namespace Providers\Tools;

class Hasher {

       /**
        * @var array - list of possible BCrypt algorithms
        */ 

       protected $algorithms;

       /**
        * @var array - the rounds used to determine hash
        */ 

       protected $workLoadFactors;

       /**
        * @var string - the specific algorithm chosen from the [$algoritms] array
        */ 

       private $algosSpecified;

       /**
        * @var bool - 
        */ 

       protected $canHash;

       public function __construct(array $config = array()){

            /*  http://php.net/crypt */

            if(CRYPT_BLOWFISH != 1) {

                  throw new \Exception("Bcrypt is not supported on this server");

            }

            $this->algosSpecified = array_key_exists('crypt_algos', $config)? $config['crypt_algos'] : 'bcrypt_blowfish'; 

            $this->algorithms = array('bcrypt'=>"$2y$", 'bcrypt_blowfish'=>"$2a$");

            $this->workLoadFactors = array(
                  'high_factor'=>"12$", 
                  'low_factor'=>"10$"
            );

            /* @TODO: try to find out about PHP's built in bcrypt for [PHP >= 5.5.*] - syntactically sugary
            */

            // >>>>>> http://docs.php.net/manual/en/function.password-hash.php

            $this->canHash = (function_exists('crypt') || function_exists('password-hash'));
       }

       /**
        * Generates a salt based on current crypt algorithm
        *
        *
        *
        *
        * @param void
        * @return mixed (string|null) 
        */

        private function generateSalt(){

            $salt = NULL;

            if($this->algosSpecified == 'bcrypt'){

                  $random = str_shuffle(mt_rand());

                  $salt = uniqid($random, true);

            }else{

                  /* secure hashing of passwords wih [bcrypt]
                     salt for [bcrypt] needs to be 22 base_64 characters (blowfish_salt)
                  */   

                  $salt = bin2hex(openssl_random_pseudo_bytes(22));
            }      

            return $salt;

        }

       /**
        * Generates a hash based on current crypt algorithm
        *
        *
        *
        * @param string $plain_text 
        * @return string 
        */

       public function hash($plain_text){

       	    if(!$this->canHash){

                   return NULL;
       	    }
            
       	    $format = $this->algorithms[$this->algosSpecified] . $this->workLoadFactors['high_factor'];

            $hash_salt = $this->generateSalt();

       	    return crypt($plain_text, ($format . $hash_salt));
       }

       /**
        * 
        *
        *
        *
        * @param string $plain_text
        * @param string $hash
        * @return bool
        */

       public function checkHash($plain_text, $hash){
            
            if(!$this->canHash){

                  return FALSE;
       	    }

            return (crypt($plain_text, $hash) === $hash);
       }

}

?>