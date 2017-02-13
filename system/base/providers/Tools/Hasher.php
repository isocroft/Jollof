<?php

/*!
 * Jollof Framework (c) 2016
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
        * @var bool - 
        */ 

       protected $canHash;

       public function __construct(array $config = array()){

            $this->algorithms = array('bcrypt'=>"$2y$", 'bcrypt_blowfish'=>"$2a$");

            $this->workLoadFactors = array('high_factor'=>"12$", 'low_factor'=>"10$");

            /* @TODO: try to find out about PHP's built in bcrypt for [PHP >= 5.5.*] - syntactically sugary
            */

            // >>>>>> http://docs.php.net/manual/en/function.password-hash.php

            $this->canHash = (function_exists('crypt') || function_exists('password-hash'));
       }

       /**
        * 
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
            // secure hashing of passwords wih [bcrypt]
       	    // salt for [bcrypt] needs to be 22 base_64 characters (blowfish_salt)

       	    $blowfish_salt = bin2hex(openssl_random_pseudo_bytes(22));
       	    $format = $this->algorithms['bcrypt_blowfish'] . $this->workLoadFactors['high_factor'];

       	    return (crypt($plain_text, ($format . $blowfish_salt)));
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

                return false;
       	}

            return (crypt($plain_text, $hash) === $hash);
       }

}

?>