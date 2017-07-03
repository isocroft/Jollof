<?php

/*!
 *
 * Jollof (c) Copyright 2016
 *
 * {Helpers.php}
 *
 */

use \Request;

final class Helpers {

      const CIPHER_KEY = 'ABCDEF123JKLMNOPQvwxUVWYZ0GHI456789abcghi]|klp[_#$qrstuRSTyz@,!def^*/?><:;+% -=.")(}{mn&o\'`~©';

      /**
       * @var Helper
       */

      private static $instance = NULL;

      /**
       * Constructor
       *
       *
       * @param void
       * @api
       */

      private function __construct(){


      }

      /**
       *
       *
       *
       *
       *
       * @param void
       * @return object $instance
       * @api
       */

      public static function createInstance(){

         if(static::$instance == NULL){
            static::$instance = new Helpers();
            return static::$instance;
         }
      }

      public static function randomCode($length = 10){
          $code = '';
          $total = 0;

          do{
              if (rand(0, 1) == 0){
                  $code.= chr(rand(97, 122)); // ASCII code from **a(97)** to **z(122)**
              }
              else{
                  $code.= rand(0, 9); // Numbers!!
              }
              $total++;
          } while ($total < $length);

          return $code;
      }

      /**
       * Get client location.
       *
       * @see https://github.com/ngfw/Recipe/
       *
       * @param void
       * @return array
       */

      public static function getClientLocation(){
          $result = array();
          $ip_data = @json_decode(file_get_contents('http://www.geoplugin.net/json.gp?ip='.Request::ip()));
          if (isset($ip_data) && $ip_data->geoplugin_countryName != NULL) {
              $result = array(
                'city' => $ip_data->geoplugin_city, 
                'code' => $ip_data->geoplugin_countryCode,
                'name' => $ip_data->geoplugin_countryName
              );
          }
          return $result;
      }


    public static function emptyCheck($value){

       return !isset($value) || empty($value);
    }



    public static function limitWords($string, $word_limit){

        $words = explode(" ", $string);
        return implode(" ", array_splice($words, 0, $word_limit));
    }


    public static function dateSet($date, $date_to_add){

        $date = new \DateTime($date);
        date_add($date, new \DateInterval("P".$date_to_add."D"));
        //date_sub();
        return $date->format("d-m-Y");
    }

    public static function encodeValue($value, $padding = 'abcd1234abcdefg') {
      	if(!static::emptyCheck($padding)){

      		      $value = urlencode(base64_encode($padding.trim($value).$padding));

      	}else{

      		      $value = urlencode(base64_encode(trim($value)));
      	}

      	return $value;
    }



    public static function decodeValue($value, $padding = 'abcd1234abcdefg') {

    	$value = base64_decode(urldecode($value));
    	if(!static::emptyCheck($padding)){

    		    $value = str_replace($padding,'',$value);
    	}

    	return $value;
    }

    public static function lowEncipher($plain_str, $key=NULL){
        /* implementing simple substitutuion cipher algorithm with nulls using random number generation +3 points */
          if($key === NULL){
             $key = self::CIPHER_KEY;
          }
          $valid_key = strrev(trim($key));
          $text_length = strlen(trim($plain_str));
          $cipher_str = '';

            if($text_length < (strlen(trim($key)) - 3)){
              for($i = 0;$i < $text_length;$i++){
                 $index = index_of($valid_key, char_at(trim($plain_str), $i));
                 $cipher_str .=  ($index > -1) ? char_at($valid_key, index_of($valid_key , char_at(trim($plain_str), $i)) + 3) : char_at($valid_key, $i);
              }
            }
          return $cipher_str;
       }


       public static function lowDecipher($cipher_str, $key=NULL){
            /* implementing simple substitutuion cipher algorithm with alternate null characters using random number generation -3 points*/
            if($key === NULL){
                $key = self::CIPHER_KEY;
            }
            $valid_key = trim($key);
            $key_length = strlen($valid_key);
            $plain_str = '';

            for($i = 0;$i < $key_length;$i++){
                  $index = index_of($valid_key, char_at(trim($cipher_str), $i));
                  $plain_str .=  ($index > -1) ? char_at($valid_key, index_of($valid_key , char_at(trim($cipher_str), $i)) + 3) : '' ;
            }
            return $plain_str;
       }

       public static function delay($input, $secret) {
              $hash = crc32(serialize($secret . $input . $secret));
              // make it take a maximum of 0.1 milliseconds
              time_nanosleep(0, abs($hash % 100000));
       }

       public static function clamp(callable $op, array $args, $time = 100) {
            $start = microtime(true);
            $return = call_user_func_array($op, $args);
            $end = microtime(true);
            // convert float seconds to integer nanoseconds
            $diff = floor((($end - $start) * 1000000000) % 1000000000);
            $sleep = $diff - $time;
            if ($sleep > 0) {
                time_nanosleep(0, $sleep);
            }
            return $return;
       }

       public static function objectToArray($anyObj){
           if(is_object($anyObj)){
               $anyObj = get_object_vars($anyObj);
           }

           if(is_array($anyObj)){
               return array_map(__METHOD__, $anyObj);
           }else{
               return $anyObj;
           }
       }

        /**
         * A timing safe equals comparison
         *
         * To prevent leaking length information, it is important
         * that user input is always used as the second parameter.
         *
         * @param string $safe The internal (safe) value to be checked
         * @param string $unsafe The user submitted (unsafe) value
         *
         * @return boolean True if the two strings are identical.
         */
         public static function timingSafeCompare($safe, $unsafe) {
            // Prevent issues if string length is 0
            $safe .= chr(0);
            $unsafe .= chr(0);

            $safeLen = strlen($safe);
            $userLen = strlen($unsafe);

            // Set the result to the difference between the lengths
            $result = $safeLen - $userLen;

            // Note that we ALWAYS iterate over the user-supplied length
            // This is to prevent leaking length information
            for ($i = 0; $i < $userLen; $i++) {
                // Using % here is a trick to prevent notices
                // It's safe, since if the lengths are different
                // $result is already non-0
                $result |= (ord($safe[$i % $safeLen]) ^ ord($unsafe[$i]));
            }

            // They are only identical strings if $result is exactly 0...
            return $result === 0;
       }

       public static function verifyHmac($algos, $data, $secret, $hash){

              $__hash = hash_hmac($algos, $data, $secret);

              return static::timingSafeCompare($hash, $__hash);
       }

       public static function encodeJWTObject(array $item){

          return base64_encode(json_encode($item));
       }

       public static function createJWT(array $props, $hash_key, $hash_algos = "HS256"){
             // @TODO: might change from HMAC Keys to Asymmetric Public/Private Keys at production (RSA)
             $header = array(
                   "typ" => "JWT",
                   "alg" => $hash_algos
             );

              $_time = time();

              // reserved claims
              $payload = array(
                  "iss" => $props['iss'], // issuer -- private claim
                  "iat" => $_time, // issued at -- private claim
                  "sub" => $props['sub'], // sub -- private claim
                  "exp" => ($_time+36000), // expiration -- private claim
                  "jti" => $props['jti'], // jwt identifier -- used to prevent token replay attacks -- private claim
                  "userPermissons" => $props['routes'] // -- public claims
              );

              $header = static::encodeJWTObject($header);
              $payload = static::encodeJWTObject($payload);

              $signature = hash_hmac('sha256', ($header.".".$payload), $hash_key);
              $signature = base64_encode($signature);

              // This will form part and parsel of our SSO signed cookie for SWAP
              $settings = ($header.".".$payload.".".$signature);

              return $settings;
       }

       public static function parseJWT($webtoken){ // when read from Cookie as a string
            $token_bits = explode('.', $webtoken);

            $parse_obj = array();
            $parse_obj['header'] = $token_bits[0];
            $parse_obj['payload'] = $token_bits[1];
            $parse_obj['signature'] = $token_bits[2];


            return static::decodeJWTObject($parse_obj);
       }

       public static function uuid($inputstr, $noDash = TRUE){ // v4 UUID format

            assert(strlen($inputstr) == 16);

            $inputstr[6] = chr(ord($inputstr[6]) & 0x0f | 0x40); // set version to 0100
            $inputstr[8] = chr(ord($inputstr[8]) & 0x3f | 0x80); // set bits 6-7 to 10

            $outputstr = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($inputstr), 4));

            return ($noDash)? preg_replace('/-/i', '', $outputstr) : $outputstr;
       }

       public static function generateRandomByPattern($pattern = "xxxxxxxx-xxxxxxxx-xxxr4xxx-xxxxxxkx"){

            return preg_replace_callback('/[xy]/', function ($matches){

                              $r = rand(1, (16|0));
                              $v = ($matches[0] == "x") ? $r : ($r&0x3|0x8);
                              return dechex($v);

            }, $pattern);

       }

       public static function validateJWT(array $jwt_arr, $hash_key){
              // pick both $jwt_arr and $hash_key from the redis server
              $jwt_plain = $jwt_arr;
              $head = base64_decode($jwt_plain['header']);
              $pload= base64_decode($jwt_plain['payload']);
              $signature = $jwt_plain['signature'];
              $time = time();
              $message = ($jwt_arr['header'].".".$jwt_arr['payload']);

              // prevents timing attacks
              if(static::verifyHmac('sha256', $message, $hash_key, $signature) /* && $time <= $pload['exp'] */){
                  return json_decode($pload, TRUE);
              }else{
                  return NULL;
              }
       }

       /**
        * Retrieves keyword suggestions from Google servers
        *
        * @see https://github.com/ngfw/Recipe/
        *
        * @param string $keyword
        * @return bool
        */

       public static function keywordSuggestionsGoogle($keyword = ' '){
            $data = file_get_contents('http://suggestqueries.google.com/complete/search?output=firefox&client=firefox&hl=en-US&q='. urlencode($keyword));
            if (($data = json_decode($data, true)) !== null 
                  && !static::emptyCheck($data[1])) {
                return $data[1];
            }
            return false;
       }

       /**
        * Returns a automatically-generated random passcode 
        *
        * @see https://github.com/ngfw/Recipe/
        *
        * @param integer $length
        * @param string $customAlphabet
        * @return string
        */


        public static function autoPassKey($length = 8, $customAlphabet = null){
            $pass = [];
            if (strlen(trim($customAlphabet))) {
                $alphabet = trim($customAlphabet);
            } else {
                $alphabet = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789';
            }
            $alphaLength = strlen($alphabet) - 1;
            for ($i = 0; $i < $length; ++$i) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            return implode($pass);
        }

       public static function decodeJWTObject(array $jwt_obj){

            $token_bits = array();

            foreach ($jwt_obj as $key => $value) {
                if($key == "signature"){
                    //$value = json_decode($value, TRUE);
                    $value = base64_decode((string) $value);
                }
                $token_bits[$key] = $value;
             }

            return $token_bits;
       }

       public static function generateCode($prefix = ""){

            return ((uniqid($prefix, false)) . (uniqid($prefix, false)));
       }


}

?>