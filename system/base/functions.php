<?php

/**
 * Jollof Framework - (c) 2016 - 2017
 *
 * {functions.php}
 *
 * @author Jollof Community
 * @license    MIT License
 * @copyright   Mobicent, Ltd.
 * @link htps://github.com/isocroft/Jollof
 */


if(!defined('COUNT_RECURSIVE')){

    define('COUNT_RECURSIVE', true);
}

if(!defined('PHP_VERSION_ID')){
    $version = explode('.', PHP_VERSION);

    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));

    if(PHP_VERSION_ID < 50207){ // PHP 5.2.7

        define('PHP_MAJOR_VERSION', $version[0]);
        define('PHP_MINOR_VERSION', $version[1]);
        define('PHP_RELEASE_VERSION', $version[2]);

    }
}

if(! function_exists('char_at') ){
    function char_at($str, $num){
	        if(gettype($str) == 'string' && gettype($num) == 'integer'){
               if($num > -1 && $num < strlen($str)){
	                for($i = 0; $i < strlen($str); $i++){
		               if($i == $num){
			              return substr($str, $i, ($i + (1 - $i)));
			           }
		            }
	            }else{ return -1; }
	        }
	}
}

if(!function_exists('apache_request_headers')){

  function apache_request_headers(){

      $headers = array();

      foreach($_SERVER as $name => $value){

        if(substr($name, 0, 5) == 'HTTP_'){

            $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));

            $headers[$name] = $value;
        }else{

            $headers[$name] = $value; 
        }
      }

      return $headers;
  }
}

if(! function_exists('get_gravatar_link')){

    function get_gravatar_link($email = '', $default='mm', $size=80, $rating='g'){

      return sprintf("https://www.gravatar.com/avatar.php?gravatar_id=%s&default=%s&size=%srating=%s", md5(strtolower(trim($email))), $default, $size, $rating);
    }
}

if(! function_exists('fast_in_array')){
  /*
    Perf: roughly 2x faster than native PHP 'in_array' function 

    -- Reason: too many C-based functions in use 
  */
  function fast_in_array($needle, $haystack){   
        $arr = array_flip($haystack);
        return isset($arr[$needle]);
  }
}

if(! function_exists('array_pick')){
      function array_pick($collection, $index){
          if(array_key_exists($index, $collection)){
              return $collection[$index];
          }
          return NULL;
      }
}

if(! function_exists('ordinal') ){
    function ordinal($cardinal){
        $test_c = abs($cardinal) % 10;
        $ext = ((abs($cardinal) % 100 < 21 && abs($cardinal) % 100 > 4)
            ? 'th'
            : (($test_c < 4)
                ? ($test_c < 3)
                    ? ($test_c < 2)
                        ? ($test_c < 1)
                            ? 'th'
                            : 'st'
                        : 'nd'
                    : 'rd'
                : 'th'));
        return $cardinal.$ext;
    }
}


if(! function_exists('days_in_month') ){ 
    function days_in_month($month = 0, $year = 0){
        if ($month < 1 or $month > 12) {
            return 0;
        }
        if (!is_numeric($year) or strlen($year) != 4) {
            $year = date('Y');
        }
        if ($month == 2) {
            if ($year % 400 == 0 or ($year % 4 == 0 and $year % 100 != 0)) {
                return 29;
            }
        }
        $days_in_month = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        return $days_in_month[$month - 1];
    }
}


if(!function_exists('tiny_url')){
    function tiny_url($url){
        if (strpos($url, 'http') !== 0) {
            $url = urlencode('http://'.$url);
        }
        $gettiny = file_get_contents("http://tinyurl.com/api-create.php?url={$url}");
        if (isset($gettiny) && !empty($gettiny)) {
            return $gettiny;
        }
        return false;
    }
    
}

if(! function_exists('simple_encode')){
    function simple_encode($string, $passkey = null){
        $key = $passkey;
        if (!isset($passkey) || empty($passkey)) {
            $key = 'eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee';
        }
        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result .= $char;
        }
        return base64_encode($result);
    }
}

if(! function_exists('simple_decode')){
    function simple_decode($string, $passkey = null){
        $key = $passkey;
        if (!isset($passkey) || empty($passkey)) {
            $key = 'eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee';
        }
        $result = '';
        $string = base64_decode($string);
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result .= $char;
        }
        return $result;
    }
}

if(!function_exists('getallheaders')){

    function getallheaders(){

        $headers = array();

        foreach($_SERVER as $name => $value){

            if(substr($name, 0, 5) == 'HTTP_'){

                $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));

                $headers[$name] = $value;

            }else if($name == "CONTENT_TYPE"){

                $headers['Content-Type'] = $value;
      
            }else if($name == "CONTENT_LENGTH"){

                $headers['Content-Length'] = $value;
            }
        }

        return $headers;
    }
}

// Fix for overflowing signed 32 bit integers,
// works for sizes up to 2^32-1 bytes (4 GiB - 1):
if(! function_exists('fix_integer_overflow')){
	function fix_integer_overflow($size) {
        if ($size < 0) {
            $size += 2.0 * (PHP_INT_MAX + 1);
        }
        return $size;
    }
}

if(! function_exists('index_of') ){
    function index_of($str, $seed, $radix  = -1){
	      $mixed = FALSE;
	      if($radix == -1){
             $mixed = strpos($str, $seed);
	      }else if($radix > -1){
	           $mixed = strpos($str, $seed, $radix);
	      }

		    return (gettype($mixed) === 'integer')? $mixed : -1;
	}
}

if(! function_exists('contains') ){
    function contains($str, $seed){
        return (index_of($str, $seed, 0) != -1);
    }
}

if(! function_exists('ignorecase_index_of') ){
    function ignorecase_index_of($str, $seed, $radix = -1){
	      if($radix == -1){
               return stripos($str, $seed);
	      }else if($radix > -1){
	           return stripos($str, $seed, $radix);
	      }else{
	          return -1;
	      }
	}
}

if(! function_exists('array_shuffle')){
    function array_shuffle($input, $input2){
      $merged = array_merge($input, $input2);
      $count = count($merged);
      $order = range(1, $count);

      shuffle($order);
      array_multisort($order, $merged);
      return $merged;
    }
}

if(!function_exists('is_multi_array')){
    function is_multi_array($array){
        $result = false;
        $filtered;
        if(count($array) != count($array, COUNT_RECURSIVE)){
            $filtered = array_filter($array, 'is_array');
            $result = is_array(current($array)) || (count($filtered) > 0);  
        }
        return $result;
    }
}

if(! function_exists('array_select')){
  function array_select($input, $select_keys){

  }
}

if(! function_exists('array_flatten')){
  function array_flatten($input){
      if(!is_multi_array($input)){
          return $input;
      }

      $return = array();
      foreach($input as $key => $value){
          if(is_array($value)){
              foreach($value as $k => $v){
                  $return[$k] = $v;
              }
              continue;  
          }

          $return[$key] = $value;  
      } 
      return $return;    
  }
}

if(! function_exists('array_pluck')){
    function array_pluck($array, $index, $as = ''){
       $return = array();
       foreach ($array as $key => $value) {
           if(is_array($value)){ 
               if(isset($value[$index])){
                  $return[$key] = $value[$index];
               }else{
                  $return[$key] = $value;
               }
           }else{
               if($index === $key){
                  if($as === ''){
                      $return[$key] = $value;
                  }else{
                      $return[$as] = $value;
                  }
               }
           }
       }
       return $return;
    }
}

if(! function_exists('str_compare_to') ){
    function str_compare_to($str1, $str2){
        if(gettype($str1) == 'string' && gettype($str2) == 'string'){
           if(strcmp($str1,$str2) == 0){
	         return 0;
	       }else{ return 1; }
	    }else{
	       return -1;
	    }
    }
}

if(! function_exists('index_of_any') ){
    function index_of_any($str, $seed, $arr){

    }
}

if(! function_exists('asset')){
    function asset($__url, $file){
        if(starts_with($file, '/') || starts_with($file, './')){
            $file = substr($file, 1);
        }

        $public = $GLOBALS['env']['app.path.public'];
        $app_root = $GLOBALS['env']['app.root'];
        $queryIndex = index_of($file, '?');
        $query = '';

        if($queryIndex > -1){
            $query = substr($file, $queryIndex);
            $file = substr($file, 0, $queryIndex);
        }

        $root = preg_replace('/\//i', DIRECTORY_SEPARATOR, $_SERVER['DOCUMENT_ROOT']);
        $root .= contains($root, $app_root)? '' :  DIRECTORY_SEPARATOR . $app_root;
        $root .= DIRECTORY_SEPARATOR;

        $real = (index_of($__url, ':') > -1)? realpath(basename($public) . '/' . $file) : realpath($file);

        $filepath = str_replace($root, $__url, $real);
        return preg_replace('/[\x5c]/i', '/', ($filepath . $query));
    }
}

if(! function_exists('url')){
    function url($__url, $route){
        if(starts_with($route, '/')){
            $route = substr($route, 1);
        }
        $fullroute = $__url . $route;
        return $fullroute;
    }
}

if(!function_exists('build_http_path')){
    function build_http_path($origin, $basefilename){
      
      $root_handler = $_SERVER['PHP_SELF'];

      if(!empty($_SERVER['CONTEXT_PREFIX'])){
        $origin .= $_SERVER['CONTEXT_PREFIX'];

        $origin .= substr(realpath($basefilename), strlen($_SERVER['CONTEXT_DOCUMENT_ROOT']));
      }else{

        $origin .= substr(realpath($basefilename), strlen($_SERVER['DOCUMENT_ROOT']));
      }
      
      $origin .= '/';
    }
}

# build_http_path(Request::getOrigin(), 'public/assets/js/src/built.js');

if(! function_exists('http_response_code') ){
   function http_response_code($code = NULL){
         $text = '';
         $GLOBALS['HTTP_CODE'] = 200; # default
         if($code === NULL || (gettype($code) != "integer")){
             $code = 0;
         }
         switch(intval($code)){
               case 100:
                  $text = 'Continue';
               break;
               case 101:
                  $text = 'Switching Protocols';
               break;
               case 200:
                  $text = 'OK';
               break;
               case 201:
                  $text = 'Created';
               break;
               case 202:
                  $text = 'Accepted';
               break;
               case 203:
                  $text = 'Non-Authoritative Information';
               break;
               case 204:
                  $text = 'No Content';
               break;
               case 205:
                  $text = 'Reset Content';
               break;
               case 206:
                  $text = 'Partial Content';
               break;
               case 207:
                  $text = 'Multi-Status';          // RFC4918
               break;
               case 208:
                  $text = 'Already Reported'; // RFC5842
               break;
               case 226:
                  $text = 'IM Used';               // RFC3229
               break;
               case 300:
                  $text = 'Multiple Choices';
               break;
               case 301:
                  $text = 'Moved Permanently';
               break;
               case 302:
                  $text = 'Found';
               break;
               case 303:
                  $text = 'See Other';
               break;
               case 304:
                  $text = 'Not Modified';
               break;
               case 305:
                  $text = 'Use Proxy';
               break;
               case 306:
                  $text = 'Reserved';
               break;
               case 307:
                  $text = 'Temporary Redirect';
               break;
               case 308:
                  $text = 'Permanent Redirect';    // RFC7238
               break;
               case 400:
                  $text = 'Bad Request';
               break;
               case 401:
                  $text = 'Unauthorized';
               break;
               case 402:
                  $text = 'Payment Required';
               break;
               case 403:
                  $text = 'Forbidden';
               break;
               case 404:
                  $text = 'Not Found';
               break;
               case 405:
                  $text = 'Method Not Allowed';
               break;
               case 406:
                  $text = 'Not Acceptable';
               break;
               case 407:
                  $text = 'Proxy Authentication Required';
               break;
               case 408:
                  $text = 'Request Timeout';
               break;
               case 409:
                  $text = 'Conflict';
               break;
               case 410:
                  $text = 'Gone';
               break;
               case 411:
                  $text = 'Length Required';
               break;
               case 412:
                  $text = 'Precondition Failed';
               break;
               case 413:
                  $text = 'Request Entity Too Large';
               break;
               case 414:
                  $text = 'Request-URI Too Long';
               break;
               case 415:
                  $text = 'Unsupported Media Type';
               break;
               case 416:
                  $text = 'Requested Range Not Satisfiable';
               break;
               case 417:
                  $text = 'Expectation Failed';
               break;
               case 418:
                  $text = 'I\'m a teapot';                                 // RFC2324
               break;
               case 422:
                  $text = 'Unprocessable Entity';                                  // RFC4918
               break;
               case 423:
                  $text = 'Locked';                            // RFC4918
               break;
               case 424:
                  $text = 'Failed Dependency';                        // RFC4918
               break;
               case 425:
                  $text = 'Reserved for WebDAV advanced collections expired proposal';      // RFC2817
               break;
               case 426:
                  $text = 'Upgrade Required';                            // RFC2817
               break;
               case 428:
                  $text = 'Precondition Required';                       // RFC6585
               break;
               case 429:
                  $text = 'Too Many Requests';                           // RFC6585
               break;
               case 431:
                  $text = 'Request Header Fields Too Large';             // RFC6585
               break;
               case 500:
                  $text = 'Internal Server Error';
               break;
               case 501:
                  $text = 'Not Implemented';
               break;
               case 502:
                  $text = 'Bad Gateway';
               break;
               case 503:
                  $text = 'Service Unavailable';
               break;
               case 504:
                  $text = 'Gateway Timeout';
               break;
               case 505:
                  $text = 'HTTP Version Not Supported';
               break;
               case 506:
                  $text = 'Variant Also Negotiates (Experimental)';              // RFC2295
               break;
               case 507:
                  $text = 'Insufficient Storage';                // RFC4918
               break;
               case 508:
                  $text = 'Loop Detected';              // RFC5842
               break;
               case 510:
                  $text = 'Not Extended';               // RFC2774
               break;
               case 511:
                  $text = 'Network Authentication Required';
               break;
               default:
                  $text = 'Unknown';
               break;
         }

         $proto = (isset($_SERVER['SERVER_PROTOCOL'])? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

         if($code != 0 && $text != 'Unknown'){
              $GLOBALS['HTTP_CODE'] = $code;
              header($proto . ' ' . $code . ' ' . $text, true, $code);
         }else{
              return $GLOBAL['HTTP_CODE'];
         }
   }
}

if(! function_exists('last_index_of') ){
    function last_index_of($str, $seed){
      if(gettype($str) == 'string' && gettype($seed) == 'string'){
         $rstr = strrev($str);
         $lx = (strlen($str)-1) - (index_of($rstr, $seed));
             if(index_of($str, $seed, ($lx-1)) + index_of($rstr, $seed) == (strlen($str)-1)){
                                   return $lx;
             }else{ return -1; }
      }
    }
}

if(! function_exists('array_mapper')){
    function array_mapper($callback, $list = array(), $context = NULL){
        $_list = array();
        
        if(!is_callable($callback)){
            return $_list;
        }

        foreach($list as $key => $value){
            $_list[$key] = $callback($value, $key, $context);
        }

        return $_list;
    }
}

if(! function_exists('all_index_of') ){
    function all_index_of($str, $seed){

    }
}

if(! function_exists('get_os') ){
    function get_os(){
        $os = '';
        if(defined('PHP_OS')){
            if(PHP_OS == 'WINNT' || PHP_OS == 'WIN32' || PHP_OS == 'Windows'){
                $os = 'windows';
            }else if(PHP_OS == 'FreeBSD' || PHP_OS == 'OpenBSD' || PHP_OS == 'NetBSD'){
                $os = 'bsd';
            }else if(PHP_OS == 'Unix'){
                $os = 'unix';
            }else if(PHP_OS == 'Linux'){
                $os = 'linux';
            }else if(PHP_OS == 'SunOS'){
                $os = 'sun';
            }
        }else{
            if(ignorecase_index_of($_SERVER['SERVER_SOFTWARE'], "Linux") > -1){
                $os = 'linux';
            }else if(ignorecase_index_of($_SERVER['SERVER_SOFTWARE'], "Unix") > -1){
                $os = 'unix';
            }else if(ignorecase_index_of($_SERVER['SERVER_SOFTWARE'], "Win") > -1){
                $os = 'windows';
            }
        }
        return $os;
    }
}

if(! function_exists('run_command_deamon')){
    function run_command_deamon($command){
      
      $php_os = get_os();
      
      if($php_os == 'windows'){
          $command = 'start "" ' . $command;
      }else{
          $command = $command . '/dev/null &';
      }

      $handle = popen($command, 'r');
      if($handle!==false){
          pclose($handle);
          return true;
      }else{
          return false;
      }
    }
}

if(! function_exists('http_link_header')){
    function http_link_header($link_file, $link_key, $context){

          $headerPartStyle = '<~~~>; rel=preload; as=style';
          $headerPartScript = '<~~~>; rel=preload; as=script';

          list(, $file_assets) = explode('../', $link_file);

          return str_replace('~~~', $context.$file_assets, $headerPartStyle);
    }
}

if(! function_exists('generate_uniq_string') ){
    function generate_uniq_string($input = NULL){
        if($input === NULL){
           $input = "abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890@#%$*";
        }
        $index = strlen($keyset);
        $modT = rand(1, 28);
        $keyset = str_shuffle($input);
	      $keyset = substr($keyset, 0 , ($index * 2) - ($index + $modT));
	      $crypt = sha1($keyset);
	      return strrev($crypt);
    }
}

if(! function_exists('starts_with') ){
   function starts_with($str, $begin, $ignorecase = FALSE){
       $len = strlen($begin);
       $slen = strlen($str);
       $sub = substr($str, 0, $len);
       if((gettype($str) == 'string' && gettype($begin) == 'string') && ($slen > $len)){
	           if($ignorecase){
		            $begin = strtolower($begin);
			           $sub = strtolower($sub);
		         }
             if(strcmp($sub, $begin) == 0){
                 return TRUE;
             }else{
                return FALSE;
             }

       }else{
	          return NULL;
	   }
   }
}

if(! function_exists('ends_with') ){
    function ends_with($str, $end){
        $len = strlen($end);
        $slen = strlen($str);
        $sub = substr($str, (-$len), ($slen-1));
        if((gettype($str) == 'string' && gettype($end) == 'string') && ($slen > $len)){
            if(strcmp($sub, $end) == 0){
                 return TRUE;
            }else{
                 return FALSE;
            }
        }else{
		        return NULL;
		}
    }
}

if(! function_exists('region_matches') ){
    function region_matches(){

    }
}


if(! function_exists('get_file_extension') ){
    function get_file_extension($file_path){
        $isdir = false;
        $args = func_get_args();
        $filename = '';
        if(strpos($file_path,'/') > 0){
            $isdir = true;
	          $filename = basename($args[0]);
        }else{
            $filename = $file_path;
        }
        $ext = explode('.', $filename);
        return $ext[1];
    }
}

if(! function_exists('strip_file_extension')){
    function strip_file_extension($file_name){
         // $_ext = get_file_extension($file_name);
         $_ext = '.php';
         return str_replace($_ext, '', $file_name);
    }
}

if(! function_exists('is_image_file') ){
    function is_image_file($file){
        $est = get_file_extension($file);
        $result = false;
            switch(strtolower($est)){
               case "jpg":
	              $result =  true;
	           break;
	           case "png":
	              $result =  true;
	           break;
	           case "gif":
	              $result =  true;
	           break;
	           case "jpeg":
	              $result =  true;
	           break;
               default:
                  $result = false;
            }
            return $result;
    }
}

if(! function_exists('get_file_name') ){
    function get_file_name($file_path, $withExt = FALSE){
        $isdir = false;
        $args = func_get_args();
        $filename = '';
        if(index_of($file_path,'/') > -1
           || index_of($file_path, DIRECTORY_SEPARATOR) > -1){
            $isdir = true;
	          $filename = basename($args[0]);
        }
        else{
            $filename = $file_path;
        }
        $ext = explode('.', $filename);

        return (!$withExt)? $ext[0] : $filename;

    }
}

if(! function_exists('custom_session_id') ){
    function custom_session_id($native = FALSE){
         return substr(generate_uniq_string(NULL), 1, ($native? 31 : 23));
    }
}

if(! function_exists('is_binary_file') ){
    function is_binary_file($file, $asString=FALSE){
        $out = array();
        $php_os = get_os();
        if($php_os != 'windows'){
           exec("file -bi" . $file, $out);
           return $asString? $out[0] : index_of($out[0], "charset=binary") > -1;
        }else{
           $out[0] = mime_content_type($file);
           return $asString? $out[0] : index_of($out[0], 'text/') == -1;
        }
    }
}

if(! function_exists('delete_file') ){
    function delete_file($file){
        if(file_exists($file)){
           return system('del '.$file);
        }
    }
}

if(! function_exists('reduce_boolean')){
    function reduce_boolean($accum, $item){
      $accum &= $item;
      return $accum;
    }
}

if(! function_exists('get_random_from_string') ){
    function get_random_from_string($str){
       return generate_uniq_string($str);
    }
}

if(! function_exists('update_placeholder')){
    function update_placeholder($value, $key){
        $stub = '?';
        $range = array();
        if(!is_array($value)){
            if(preg_match('/^\+(?:[\d]+)/', $value)){
                return ('$key = $key + ' . str_replace('+', '', $value));
            }
            return "$key = " . $stub;
        }

        if(ignorecase_index_of($value[0], 'between') > -1){
            if(array_key_exists(1, $value)){
                $range = explode(',', $value[1]);
                $range = array_fill(0, count($range), $stub);
            }
        }

        return "$key " . $value[0] . " " . (count($range) > 0? (implode(' AND ', $range)) : $stub);
    }
}

if(! function_exists('array_prefix_values')){
    function array_prefix_values(){

    }
}

if(! function_exists('array_swap_values')){
    function array_swap_values($str, $arr){
        $ret = array();
        foreach($arr as $key => $val){
            $ret[$key] = $str;
        }
        return $ret;
    }
}

if(! function_exists('make_file') ){
    function make_file($file){
	   return system('echo. >> '.$file);
	}
}

if(! function_exists('write_file_chunk')){
    function write_file_chunk($file_name, $chunk, $replace){
          $status = false;

        if(!isset($file_name) 
            || !file_exists($file_name)){
            return false;
        }

        if(!isset($chunk)){
            $chunk = " " . PHP_EOL;
        }
    
        $file = fopen($file_name, "a");

        if (!$file) return $status;

        if (!flock($file, LOCK_EX)) {
            return $status;
        }

        if(!$replace){
            $status = fwrite($file, $chunk);
            fclose($file);
        }else{
            $status = file_put_contents($file_name, $chunk, LOCK_EX);
        }    
    
        return $status;
    }
}

if(! function_exists('make_folder') ){
    function make_folder($folder, $hide = FALSE, $mode, $depth){
	    $val = @mkdir($folder, $mode, $depth);
      $cmd = "";
		  if($hide === TRUE){
         if(get_os() == 'windows'){
            $cmd = "attrib +h +s {$folder}";
		     }else if(get_os() == 'linux'){
            $cmd = "mv {$folder} .{$folder}";
         }
         system($cmd);
      }
		  return $val;
	}
}

if(! function_exists('del_folder') ){
    function del_folder($folder){
        $val = @rmdir($folder);
        return $val;
    }
}

if(! function_exists('delete_text_from_file') ){
    function delete_text_from_file($file, $str){
           $isdir = is_dir($file);
           $isfile = is_file($file);
           $oldx = ($isfile) ? file_get_contents($file) : $isdir ? file_get_contents(basename($file)) : "";
           if(index_of($oldx, $str) > -1){
              $newx =  str_replace($str, "", $oldx);
              file_put_contents($newx, $file);
              return TRUE;
           }
           return FALSE;
    }
}

/*
  @from: https://stackoverflow.com/questions/17160696/php-glob-scan-in-subfolders-for-a-file
*/
if(! function_exists('rglob')){
  function rglob($pattern, $flags = 0) {
      $files = glob($pattern, $flags); 
      foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
          $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
      }
      return $files;
  }
}

/*
  @from: https://stackoverflow.com/questions/17160696/php-glob-scan-in-subfolders-for-a-file
*/
if(! function_exists('rsearch')){
    function rsearch($folder, $pattern_array) {
        $return = array();
        $dir = new RecursiveDirectoryIterator($folder);
        $ite = new RecursiveIteratorIterator($dir);
        //$files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
        foreach($ite as $file){ # foreach($files as $file){
            if (in_array(strtolower(array_pop(explode('.', $file))), $pattern_array)){
                $return[] = $file;
            }
        }
        return $return;
    }
}

# rsearch('/public/', array('jpeg', 'png'));

if(! function_exists('make_seed') ){
    function make_seed(){
      list($usec, $sec) = explode(' ', microtime());
      return (float) $sec + ((float) $usec * 100000);
    }
}

if(! function_exists('get_random_as_range') ){
    function get_random_as_range($useText=FALSE, $len=10, $range=10){
        $text = array();
        for($i=0;$i < $len; $i++){
            $rnd = rand(2, $range);
          if($useText){
            //mt_srand(make_seed());
                $text[] = base_convert(mt_rand(0xaaff355db, 0x543dbbca310) >> 0xffa, $rnd, 36);
          }
        }
        return join($text);
    }
}


if(! function_exists('write_to_file') ){
    function write_to_file($entry, $file, $overwrite=true){
         $is_dir = is_dir($file); // (index_of(trim($file), '/') > -1 && last_index_of(trim($file),'.') > -1);
         $is_file = is_file($file) && file_exists($file); //(index_of(trim($file), '/') == -1 && last_index_of(trim($file),'.') > -1);

         if($is_dir || $is_file){
            if(!$overwrite && get_file_extension($file) == 'rtc' || get_file_extension($file) == 'log'){
                 $fh = fopen($file, 'a');
            }
            else if(!$overwrite && get_file_extension($file) == 'txt'){
                 $fh = fopen($file, 'at');
            }
      			else if(!$overwrite && get_file_extension($file) == 'json'){
      			     $fh = fopen($file, 'a');
      			}
            else if($overwrite){
                 $fh = fopen($file, 'w+');
            }else{}

            if(isset($fh) && gettype($entry) == 'string'){
	            fwrite($fh, $entry);
		        fclose($fh);
		        return TRUE;
	        }else{
	            return FALSE;
	        }
        }else{
		       return FALSE;
	    }
    }
}

if(! function_exists('read_from_file') ){
    function read_from_file($file){
        if(gettype($file) == 'string' && !is_dir($file) && is_file($file) && file_exists($file)){
            $fr = fopen($file, 'r');

            while(!feof($fr)){
               $reader = fread($fr, filesize($fr));
            }

	        fclose($fr);
	        return $reader;
        }
    }
}

if(! function_exists('is_file_in_dir') ){
    function is_file_in_dir($file, $dir){
        $isDir = is_dir($dir);
        if(gettype($dir) == 'string' && gettype($file) == 'string' && $isDir){
            $dh = opendir($dir);
            while($filename = readdir($dh)){
	            if($filename == $dir.'/'.$file && is_file($dir.'/'.$file)){
		          return TRUE;
		        }else{
		          return false;
		        }
	        }
        }
    }
}

if(! function_exists('cloudinary_public_id') ){
     function cloudinary_public_id($cloudinary_url){
          return (preg_replace('/http\:\/\/res\.cloudinary\.com\/(?:[^\s\S]+?)\/image\/upload\//', '', $cloudinary_url));
     }
}

if(! function_exists('cloudinary_url')){
     function cloudinary_url($public_url){

     }
}

########### DB access function #############

/*
 HOW TO USE THE BELOW FUNCTIONS

 db_get("SELECT * FROM tbl_client_testimonies WHERE client = ?", array("str" => "Alexis"), 3);
 db_put("INSERT/UPDATE INTO tbl_news_subscribers (email, location, created_at) SET logged = ? / VALUES (?, ?, ?)", array("str" => "xyz@gmail.com", "str" => "Abuja", "int" => time(), TRUE);
 db_delete("DELETE * FROM tbl_saas_pricing WHERE admin_id = ?", array("str" => ""), TRUE);

 @TODO: THINKING OF USING db_let("UPDATE tbl_saas_pricing SET =  WHERE admin_id = ?"); FOR UPDATE QUERIES ??

 */


if(! function_exists('db_get') ){ ## SQL SELECT
    function db_get($pdo, $param_types, $query = "", $params = array(), $rows_limit = NULL, $resultset_cols_filter = array()){

		  if(strlen($query) == 0 || !is_array($params) || !is_object($pdo)){
		      return NULL;
		  }

		  $query = trim($query);


		  if(!starts_with($query, "SELECT", TRUE)){
		      return NULL;
		  }

		  //if(count($params) !== count(all_index_of($query, "?"))){
		    //  throw new Exception("Error: 'db_get' function has entered an unstable state: insufficient/excessive query paramters supplied");
		 // }

		try{

		  $param_count = 0;
		  $set_array = array();
		  $set_filter = array();
          $stmt = $pdo->prepare($query);
		  foreach($params as $type => $param){ // params filter by rows (obviously !?)
		     $stmt->bindParam(++$param_count, ("int" != $type? $param : intval($param)), $param_types[$type]);
		  }

             if($stmt->execute()){

                while($resultset = $stmt->fetch(PDO::FETCH_ASSOC)){

				    if(count($resultset_cols_filter) > 0){
				       foreach($resultset as $key => $val){
					      if(in_array($resultset_cols_filter, $key)) // filtering by columns...
                              $set_filter[$key] = $val;
				       }
					   $set_array[] =  (count($set_filter) > 0)? $set_filter : $resultset;
                       $set_filter = array();
					}else{
					   $set_array[] = $resultset;
					}
                }

                if($stmt->rowCount() > 0){
                    if(count($set_array) > 0){
                           return $set_array;
					}else{
					       return FALSE;
					}
                }else{

				}
			}else{
			        return NULL;
			}
          }catch(\Exception $e){
                  throw $e;
          }
	   }
}

if(! function_exists('db_put') ){ ## SQL INSERT
    function db_put($pdo, $param_types, $query = "", $params = array(), $commit = FALSE, $transact = TRUE){

		  if(strlen($query) == 0 || !is_array($params) || !is_object($pdo)){
		      return NULL;
		  }

		  $query = trim($query);

		  if(!starts_with($query, "INSERT", TRUE)){
		      return NULL;
		  }
		  //if(count($params) !== count(all_index_of($query, "?"))){
		     //  throw new Exception("Error: 'db_put' function has entered an unstable state: insufficient/excessive query paramters supplied");
           //}


		  try{

		       $param_count = 0;
		       $set_array = array();
           $stmt = $pdo->prepare($query);

		       foreach($params as $type => $param){
		           $stmt->bindParam(++$param_count, ("int" != $type? $param : intval($param)), $param_types[$type]);
		       }

              if($transact)
                 $pdo->beginTransaction();

              if($stmt->execute()){

				   if($commit)
				       $pdo->commit();

                 if(starts_with($query, "INSERT"))
				             return $pdo->lastInsertId();
	             else
				             return 0;

	          }else{

                  $pdo->rollBack();
                  return NULL;
            }

          }catch(\Exception $e){
                 throw $e;
          }
    }
}

if(! function_exists('db_post') ){  ## SQL UPDATE
    function db_post($pdo ,$param_types, $query = "", $params = array(), $commit = FALSE, $transact = TRUE){


		  if(strlen($query) == 0 || !is_array($params) || !is_object($pdo)){
		      return NULL;
		  }
		  $query = trim($query);

		  if(!starts_with($query, "UPDATE", TRUE)){
		      return NULL;
		  }

      try{

           $param_count = 0;
           $set_array = array();
           $stmt = $pdo->prepare($query);

           foreach($params as $type => $param){
               $stmt->bindParam(++$param_count, ("int" != $type? $param : intval($param)), $param_types[$type]);
           }

              if($transact)
                 $pdo->beginTransaction();

              if($stmt->execute()){

                     if($commit)
                         $pdo->commit();

                         return 1;
              }else{

                    $pdo->rollBack();
                    return NULL;
              }

      }catch(\Exception $e){
             throw $e;
      }
	  }
}

if(! function_exists('db_del') ){ ## SQL DELETE
    function db_del($pdo ,$param_types, $query = "", $params = array(), $commit = FALSE){


		  if(strlen($query) == 0 || !is_array($params) || !is_object($pdo)){
		      return NULL;
		  }
		  $query = trim($query);

		  if(!starts_with($query, "DELETE", TRUE)){
		      return NULL;
		  }

      try{

           $param_count = 0;
           $set_array = array();
           $stmt = $pdo->prepare($query);

           foreach($params as $type => $param){
               $stmt->bindParam(++$param_count, ("int" != $type? $param : intval($param)), $param_types[$type]);
           }

              if($transact)
                 $pdo->beginTransaction();

              if($stmt->execute()){

                     if($commit)
                         $pdo->commit();

                         return 1;
              }else{

                    $pdo->rollBack();
                    return NULL;
              }

      }catch(\Exception $e){
             throw $e;
      }

  }
}

if(! function_exists('db_copy') ){ ## SQL INSERT/SELECT
    function db_copy($query, $params = array()){

	}
}


?>
