<?php

/*!
 * Jollof (c) Copyright 2016
 *
 * {Request.php}
 *
 */

use \Session;
use \Providers\Core\InputManager as Manager;


final class Request {

      const CR_LF = "\r\n";

     /**
      * @var Request
      */

     private static $instance = NULL;

     /**
      * @var string - HTTP request method
      */

     private $method;

     /**
      * @var Providers\Core\InputManager - manages all input from HTTP request (e.g query strings, post data, entity body)
      */

     private $inputManager;

     /**
      * @var array - keeps track of all HTTP request header
      */

     private $headers;

     /**
      * @var array - payload formats for HTTP requests
      */

     private $accepted_formats = array(
          'application/octet-stream' => 'binary',
          'application/json' => 'json',
          'application/xhtml+xml' => 'xml',
          'application/xml' => 'xml',
          'application/x-www-form-urlencoded' => 'html',
          'text/html' => 'html',
          'text/plain' => 'text',
          'multipart/form-data' => 'multipart'
     );

     /**
      * @var string - currently selected format for HTTP requests
      */

     private $format = '';

     /**
      * @var string - 
      */

     private $base_dir = '';

     /**
      * @var array - paramenters from HTTP requests (GET, POST)
      */

     protected $parameters;

     /**
      * @var array - 
      */

     protected $uploadConfig;

     /**
      * @var SessionService - the configured session driver service
      */

     protected $sessionService;

    /**
     * Constructor
     *
     *
     * @param array -
     * @api
     */

     private function __construct(array $config = array(), Session $session){

           $this->headers = getallheaders();

           $this->sessionService = $session->getDriver();

           $this->uploadConfig = $config;

           $this->setRequestFormat();

           $this->inputManager = NULL;

           $this->parameters = NULL;

           $this->method = $this->getInfo('REQUEST_METHOD');

           $this->base_dir = $this->getInfo('DOCUMENT_ROOT');
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

     public static function createInstance($config, Session $session){

          if(static::$instance == NULL){
             static::$instance = new Request($config, $session);
             return static::$instance;
          }
     }

     public static function header($key){

         // $_key = str_replace('HTTP', '', $key);

         // @TODO: change the latter [line 78] to the former [line 76]

         // return static::$instance->headers[strtolower(str_replace('_', '-', $_key))];

         return static::$instance->getInfo($key);
     }

    /**
     * Checks if a given HTTP request header exists
     *
     *
     *
     * @param string $header
     * @return bool
     */

     public static function hasHeader($header){

          return array_key_exists($header, static::$instance->headers);
     }

    /**
     * Retrieve the value of a given HTTP request header
     * 
     *
     *
     *
     * @param string $key
     * @return bool
     */

     public static function rawHeader($key){

         $headers = static::$instance->headers;

         if(static::hasHeader($key)){

             return $headers[$key];
         }

         return NULL;
     }

     /**
      *
      *
      *
      * @param void
      * @return string
      */

     public function getIp(){

          $allIPkeys = array(
              'HTTP_CLIENT_IP', // basic client ip
              'HTTP_X_FORWARDED_FOR', // support for proxies/load-balancers (if the server is behind a one)
              'HTTP_X_FORWARDED',
              'HTTP_X_CLUSTER_CLIENT_IP',
              'HTTP_FORWARDED_FOR',
              'HTTP_FORWARDED',
              'REMOTE_ADDR', // fallback finally to actual server ip
          );

          $ips = array();

          foreach ($allIPkeys as $ipkey) {
                if (array_key_exists($ipkey, $_SERVER) !== TRUE) {
                    continue;
                }
                $ips = explode(',', $this->getInfo($ipkey));
                foreach ($ips as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
          }

          return '::1';
     }

     public function pjaxRequest(){

          $x_header = $this->getInfo('HTTP_X_PJAX');

          return (isset($x_header));
     }

     public function ajaxRequest(){

          $x_header = $this->getInfo('HTTP_X_REQUESTED_WITH');
         
          return (!empty($x_header) && strtolower($x_header) == 'xmlhttprequest');
     }

     public function getInfo($var){
           
           if(array_key_exists($var, $_SERVER)){
                 
                 return $_SERVER[$var];
           }
           return '';
     }

     public function getInputManager(){

        if($this->inputManager === NULL){
            $this->inputManager = new Manager($this->getParameters(), $this->getUploadConfig());
        }

        return $this->inputManager;
     }

     /**
      *
      *
      *
      * @param void
      * @return string
      */

     public function host(){

          $_host = NULL;
         
         /* 
            HERE: we give preference to any load balancers/proxies that might be in
            front of the Jollof application server
         */

         $host = $this->getInfo('HTTP_X_FORWARDED_HOST');

         if(!isset($host) || empty($host)){

            $_host = $this->getInfo('HTTP_HOST');

            $host = $this->getInfo('SERVER_NAME');
            
            $host = (isset($_host))? $_host : $host;
         }

         if(preg_match('/^[\d]{2,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}(?:\:\d{2,5})?$/', $host)){

             $_host = gethostbyaddr($host);

         }else{

             $_host = $host;
         }

         return $_host;
     }

     public function url(){

          $r_uri = $this->getInfo('REQUEST_URI');

          if($r_uri === ''){
              $slf = $this->getInfo('PHP_SELF');
              if ($slf != ''){
                    $r_url .= substr($slf, 1, last_index_of($slf, '/'));
                    
                    if ($this->getInfo('QUERY_STRING') != '') {
                        
                        $r_uri .= '?'. $this->getInfo('QUERY_STRING');
                    }
              }
          }

          $host = $this->host();
          $root = $GLOBALS['env']['app.root'];
          if(index_of($r_uri, $root) > -1){
              return preg_replace(
                    '/\/?'.$root.'(?:\/public\/)?/i', 
                    '', 
                    urldecode(parse_url($r_uri, PHP_URL_PATH))
              );
          }else{
              return urldecode(parse_url($r_uri, PHP_URL_PATH));
          }
     }

     private function parseRequestInput($headerKeys){

          $sliced;

          $this->parameters = array();
          $this->url_elements = explode('/', $this->getInfo('PATH_INFO'));

          $qs = $this->getInfo('QUERY_STRING');

          switch($this->method){
               case "JSONP":
               case "GET":
                    if(isset($qs)){
                        parse_str($qs, $this->parameters);
                    }else if(count($_GET) > 0){
                        $sliced = array_slice($_GET, 0);
                        array_merge($sliced, $this->parameters);
                    }
               break;
               case "POST":
               case "PUT":
                   $body = file_get_contents("php://input");
                   if(Helpers::emptyCheck($body)){
                       $body = $HTTP_RAW_POST_DATA;
                   }
                   
                   if(!Helpers::emptyCheck($body)){
                       switch ($this->format){
                          /*case 'text':*/
                          case 'json':
                              $body_params = @json_decode($body, TRUE);
                              if(isset($body_params)){
                                  foreach ($body_params as $param_name => $param_value) {
                                      $this->parameters[$param_name] = trim(strip_tags($param_value));
                                  }
                              }
                          break;
                          case 'text':
                              # more code here ...
                          break;
                          case 'binary':
                              # more code here ...
                          break;
                          case 'multipart':
                              /* multipart/form-data; boundary=xxxxxxx */
                              /* multipart/form-mdata */

                              /* 
                                Jollof will parse HTTP entity body manually, 
                                if and only if the current request is not a
                                POST.
                              */

                               if($this->method === 'POST'
                                 && isset($_POST)){
                                  break;
                               } 

                              $boundary = NULL;
                              $disposition = NULL;
                              $range = NULL;
                              $multi_parts = array();
                              $sentinel = '--';
                              $files = array();
                              $field_details = array('is_file' => FALSE);
                              $end_of_multi_parts = $sentinel . self::CR_LF;

                              if(index_of($body, $sentinel) == 0){
                                  $boundary = substr($body, 0, index_of($body, self::CR_LF));
                              }else{
                                  $boundary = $sentinel;
                              }

                              if($boundary != $sentinel){
                                  $multi_parts = array_slice(explode($boundary, $body), 1);

                                  foreach ($multi_parts as $part) {
                                      /*
                                        if we get to the end of the parts, terminate the loop
                                      */
                                      if($part == $end_of_multi_parts){
                                          break;
                                      }

                                      /* 
                                        Separate actual entity body content from body headers 
                                      */
                                      $part = ltrim($part, self::CR_LF);

                                      list($body_headers, $body) = explode(self::CR_LF . self::CR_LF, $part, 2);

                                      $body_headers = explode(self::CR_LF, $body_headers);

                                      foreach ($body_headers as $header) {
                                          
                                          list($hname, $hvalue) = explode(':', $header);

                                          $this->headers['Chunked-Upload'] = 'invalid';
                                          
                                          if(ignorecase_index_of($hname, 'content-disposition') > -1){
                                              $field_details['content-disposition'] = (ltrim($hvalue, ' '));
                                          }

                                          if(ignorecase_index_of($hname, 'content-range') > -1){
                                              $field_details['content-range'] = (ltrim($hvalue, ' '));
                                              /* creating a "fake" header to signal a chunked file upload */
                                              $this->headers['Chunked-Upload'] = 'valid';
                                          }
                                          
                                          if((ignorecase_index_of($hname, 'transfer-encoding') > -1) && trim($hvalue) == 'chunked'){
                                              /* creating a "fake" header to signal a chunked file upload */
                                              $this->headers['Chunked-Upload'] = 'valid';
                                          }

                                          if(ignorecase_index_of($hname, 'content-type') > -1){

                                              $field_details['is_file'] = TRUE;
                                              $field_details['content-type'] = trim($hvalue);
                                          }
                                      }

                                      /*
                                        Parse out 'Content-Disposition' and 'Content-Range' header(s)
                                      */
                                        
                                      if(count($field_details) > 0){
                                          $disposition = $field_details['content-disposition'];
                                          if(array_key_exists('content-range', $field_details)){
                                             $range = $field_details['content-range'];
                                          }
                                          $filename = '';
                                          if(!is_null($disposition)){
                                              preg_match('/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/', $disposition, $matches);

                                              list(, $kind, $name) = $matches;

                                              if(isset($matches[4])){
                                                  $filename = $matches[4];
                                                  $files[$filename] = array();
                                                  if(!$field_details['is_file']){
                                                      $field_details['is_file'] = TRUE;
                                                  }
                                              }

                                              $matches = NULL;

                                              $chunked = ($this->headers['Chunked-Upload'] === 'valid');

                                              if(array_key_exists($filename, $files)){

                                                          $ext = pathinfo($filename, PATHINFO_EXTENSION);

                                                          $rn = (str_shuffle(implode("",range(0, 10))));

                                                          $files[$filename]['tmp_name'] = "file_{$rn}.{$ext}";
                                                          $files[$filename]['name'] = $filename;
                                                          
                                                          if(isset($field_details['content-type'])){
                                                              $files[$filename]['type'] = $field_details['content-type'];
                                                          }

                                                          $files[$filename]['error'] = 0;

                                                      unset($field_details['content-type']);  
                                              }

                                              if(!is_null($range)){
                                                preg_match('/^bytes(?:=| *)(\d*)-(\d*)\/(.*)$/', $range, $matches);
                                                    if(array_key_exists($filename, $files)){

                                                          if(isset($matches[3]) && $matches[3] != '*'){
                                                              $files[$filename]['size'] = $matches[3];
                                                          }

                                                          if(isset($matches[1])){
                                                              $files[$filename]['chunk_start'] = $matches[1];
                                                          }
                                                          
                                                          if(isset($matches[2])){
                                                              $files[$filename]['chunk_end'] = $matches[2];
                                                          }
                                                    }

                                                    unset($field_details['content-range']);
                                              }

                                              unset($field_details['content-disposition']);

                                              switch($kind){
                                                  case 'form-data':
                                                  default:
                                                      if(isset($filename) 
                                                          && array_key_exists($filename, $files)
                                                            && $field_details['is_file']){
                                                          
                                                          /* Create a "fake superglobal" to mimic $_FILES array for handling uploads */
                                                          if(Helpers::emptyCheck($GLOBALS['FILES'])){
                                                                $GLOBALS['FILES'] = array();
                                                          }

                                                          /* Get the temporary upload dir */
                                                          $up_dir_path = ini_get('upload_tmp_dir');

                                                          /* construct the path for the temp file */
                                                          $tmp_file_path = $up_dir_path . '/' . $files[$filename]['tmp_name'];

                                                          /* create the file in temp dir */
                                                          if($chunked){
                                                                
                                                                /* if it's a chunked file upload, try to extract information from the application session, modify and write back */
                                                                if($this->sessionService->hasKey('__chunked_fileuploads')){
                                                                    $session_data = $this->sessionService->read('__chunked_fileuploads');
                                                                }else{
                                                                    $session_data = array();
                                                                }

                                                                if(array_key_exists($filename, $session_data)){
                                                                    $tmp_file_path = $session_data[$filename]['local_path'];
                                                                }else{
                                                                    $session_data[$filename] = array('is_total' => FALSE);
                                                                    $session_data[$filename]['total_chunk_size'] = 0;
                                                                    $session_data[$filename]['local_path'] = $tmp_file_path;
                                                                    $session_data[$filename]['total_file_size'] = intval($files[$filename]['size']);
                                                                }

                                                                $bytes = write_file_chunk($tmp_file_path, trim(substr($body, 0, strlen($body) - 1)), FALSE);

                                                                if(array_key_exists('chunk_start', $files[$filename])){
                                                                  $chunk_start = intval($files[$filename]['chunk_start']); 
                                                                  $chunk_end = $chunk_start + intval($files[$filename]['chunk_end']);
                                                                }else{
                                                                  $chunk_start = 0;
                                                                  $chunk_end = $bytes || 0;
                                                                }

                                                                if ($chunk_end >= $session_data[$filename]['total_file_size']){
                                                                    $chunk_end = $session_data[$filename]['total_file_size'];
                                                                    $session_data[$filename]['is_total'] = TRUE;
                                                                }

                                                                $session_data[$filename]['chunk_start'] = $chunk_start;
                                                                $session_data[$filename]['chunk_end'] = $chunk_end;

                                                                $session_data[$filename]['uploaded_chunk_updatedat'] = (new \DateTime())->format("Y-m-d H:i:s");

                                                                $this->sessionService->write('__chunked_fileuploads', $session_data);

                                                                if($session_data[$filename]['is_total'] != TRUE){

                                                                    continue;
                                                                }
                                                          }else{
                                                                $bytes = write_file_chunk($tmp_file_path, substr($body, 0, strlen($body) - 1), TRUE);
                                                          }

                                                          if(!isset($files[$filename]['type'])){
                                                              $files[$filename]['type'] = mime_content_type($tmp_file_path);
                                                          }

                                                          $files[$filename]['size'] = filesize($tmp_file_path) /* || $bytes*/;
                                                          
                                                          $GLOBALS['FILES'][$name] = $files[$filename];

                                                          $filename = NULL;

                                                          $field_details['is_file'] = FALSE;

                                                      }else{
                                                          $this->parameters[$name] =  trim(strip_tags(substr($body, 0, strlen($body) - 2)));
                                                      }
                                                  break;
                                              }

                                          }  
                                      }

                                  }
                              }
                          break;
                          case 'html':
                              parse_str($body, $postvars);
                              foreach ($postvars as $field => $value) {
                                  $this->parameters[$field] = trim(strip_tags($value));
                              }
                          break;
                          default:
                             $this->parameters['nothing'] = "";
                          break;
                       }
                   }else if(!Helpers::emptyCheck($_POST)){
                      if(count($_POST) > 0){
                          $sliced = array_slice($_POST, 0);
                          array_merge($sliced, $this->parameters);
                      }
                   }
               break;
          }

          if($headerKeys !== NULL){
              foreach ($headerKeys as $hkey) {
                  $this->parameters[strtolower($hkey)] = (array_key_exists($hkey, $this->headers)? $this->headers[$hkey] : '');
              }
          }
     }

     private function setRequestFormat(){

         $content_type = $this->getInfo('CONTENT_TYPE');

         if(!isset($content_type)){ // this mostly works out for only POST, PUT requests (not GET)
            $content_type = $this->getInfo('HTTP_CONTENT_TYPE'); // this works for GET (custom .htaccess plug)
         }
         $this->format = (array_key_exists($content_type, $this->accepted_formats))? $this->accepted_formats[$content_type] : (contains('/form-data; boundary=', $content_type)? 'multipart' : '');
     }

     private function getFormat(){

         return $this->format;
     }

     private function getParameters(){

         return $this->parameters;
     }

     private function getUploadConfig(){

         return $this->uploadConfig;
     }

     public function getMethod(){

         return $this->method;
     }

    /**
     * Retrieves the HTTP request method
     * 
     *
     *
     *
     * @param void
     * @return string
     */

     public static function method(){

          return static::$instance->getMethod();

     }

     /**
     * Checks if a given HTTP request is an AJAX request
     * 
     *
     *
     *
     * @param void
     * @return bool
     */

     public static function isAjax(){
         
          return static::$instance->ajaxRequest();

     }

    /**
     * Checks if a given HTTP request is a push state AJAX 
     * request
     *
     *
     *
     * @param void
     * @return bool
     */

     public static function isPjax(){

          return static::$instance->pjaxRequest();
     }

    /**
     * Retrieves the Wireless Application protocol for a given 
     * HTTP request (especially from a HTTP request)
     *
     *
     *
     * @param void
     * @return string
     */

     public static function wapProfile(){

          $profile = static::$instance->getInfo('HTTP_X_WAP_PROFILE');
          if(!isset($profile)){
              $profile = static::$instance->getInfo('HTTP_PROFILE');
          }
          return $profile;
     }

    /**
     * Retrieves the referer of a given HTTP request 
     * 
     *
     *
     *
     * @param void
     * @return string
     */

     public static function referer(){

         return static::$instance->getInfo('HTTP_REFERER');
     }

    /**
     * Retrieves the host of a given HTTP request
     *
     *
     *
     * @param void
     * @return string
     */

     public static function getHost(){

         return static::$instance->host();
     }

    /**
     * Retrieves the protocol of a given HTTP request
     *
     *
     *
     *
     * @param void
     * @return string
     */

     public static function getProtocol(){

          $httpVersion = array_pick(explode('/', static::header('SERVER_PROTOCOL')), 1);

          // Non-standard field for [Microsoft] load-balancers that support SSL
          $_isSSL = static::$instance->getInfo('HTTP_FRONT_END_HTTPS');

          // Non-standard field for servers generally
          $isSSL = (isset($_isSSL))? $_isSSL : static::$instance->getInfo('HTTPS');
          $xProto = static::$instance->getInfo('HTTP_X_FORWARDED_PROTO');
          $protocol = "http";

          if((isset($isSSL) &&($isSSL == 'on' || $isSSL === 1))
              || (isset($xProto) && $xProto == 'https' )){
              $protocol = "https";
          }

          return $protocol;
     }

    /**
     * Retrieves the device type of the device from which
     * a HTTP request emanates from
     *
     *
     *
     * @param void
     * @return string
     */

     public static function getDeviceType(){

         $tabletRgx = '/(tablet|ipad|playbook|(andriod(?!.*(?:mobi|opera mini)))/i';
         $mobileRgx = '/(up.browser|up.link|symbian|widp|wap|phone|andriod|iemobile)/i';

         $acceptable = static::$instance->getInfo('HTTP_ACCEPT');
         $mobile_ua = static::$instance->getInfo('HTTP_X_OPERAMINI_PHONE_UA');

         if(!isset($mobile_ua)){
            
            $mobile_ua = static::$instance->getInfo('HTTP_DEVICE_STOCK_UA');
         }

         $wap_proflie = static::wapProfile();

         if(contains($acceptable, 'application/vnd.wap.xhtml+xml')){
                return 'mobile';
         }
    }

    /**
     * Retrieves the port of the server
     *
     *
     *
     * @param void
     * @return string
     */

    public static function getPort(){

          $proto = static::getProtocol();
          $port = static::$instance->getInfo('SERVER_PORT');

          if($proto == 'https'){

              $port = '443';
          }

          if($port != ''){

              $port = intval($port);
          }

          return $port === 80? '' : settype($port, 'string');
     }

     /**
      * Retrieves the URI of a given HTTP request
      *
      *
      *
      *
      * @param void
      * @return string
      */

      public static function uri(){
        
          return static::$instance->url();
      }

    /**
     * Retrieves the origin of a given HTTP request
     *
     *
     *
     *
     * @param bool $parsed
     * @return mixed (string|array)
     */

     public static function getOrigin($parsed = FALSE){

          $_parsed = array(
              'scheme' => static::getProtocol(),
              'host' => static::getHost(),
              'auth' => static::getAuth(),
              'port' => static::getPort(),
              'path' => static::uri()
          );

          $origin = $_parsed;

          if($parsed === FALSE){
            
              $origin = "{$_parsed['scheme']}://{$_parsed['host']}";

              if($_parsed['port'] != ''){

                  $origin .= ":{$port}";
              }

          }

          return $origin;

     }

    /**
     * Checks if a given HTTP request is a cross origin 
     * request
     *
     *
     *
     * @param void
     * @return bool
     */

     public static function isCors(){

        return static::rawHeader('Origin') !== static::getOrigin();
     }

    /**
     * Checks if a given HTTP request is an OPTIONS request 
     * 
     *
     *
     *
     * @param void
     * @return bool
     */

     public static function isPreflight(){

        return static::isCors() && static::method() == 'OPTIONS' && static::hasHeader('Access-Control-Request-Method');
     }

     /**
      * Sets up the body of a given HTTP request to be consumed
      * 
      *
      *
      * 
      * @param array $headerKeys
      * @return Providers\Core\InputManager
      * @deprecated
      */

     public static function input($headerKeys = NULL){

         if(is_null(static::$instance->getParameters())){
              static::$instance->parseRequestInput($headerKeys);
         }

         return static::$instance->getInputManager();
     }

    /**
     * Uploads a file (or group of files) from client to server 
     *
     *
     *
     *
     * @param string $file_upload_folder
     * @param array $errors
     * @return array $result - a map of the file input tag name attribute (key) to the uploaded file full path name (value)
     */

     /* @TODO: 

        check if the below code line is going to create memory leaks as an argument to 
        the upload method -

        &$errors = array();

     */

     public static function upload($file_upload_folder, &$errors){

         $manager = static::input(
                          array( /* incase uploading JavaScript Blob input file using AJAX */
                                  
                                  'X-Blob-Chunk-Size', # the size of the file chunk sent from the client
                                  'X-Blob-Chunk-Number', # the sequence number of the chunk (used to deal effectively with race conditions on the server where file chunks may arrive out of order)
                          )
                    );

         $rules = array();
         $sdata = array();


         $isChunked = (static::rawHeader('Chunked-Upload') === 'valid');
       
         if($isChunked){
              /* if we are dealing with a chunked file upload(s), simply use our tracking data in the session */
              $sdata = static::$instance->sessionService->read('__chunked_fileuploads');
              $rules = is_array($sdata) ? array_keys($sdata) : array();

         }     

         $upload_map = array_swap_values($file_upload_folder, $manager->getFiles());

         /* @HINT: [$rules] and [$errors] variables are passed by reference not by value */
         $results = $manager->uploadFiles($upload_map, $rules, $errors);

         if($isChunked){
              /* clean up our tracking data in the session whenever a chunked file upload has completed and the chunked file reassembled and uploaded */
             foreach($rules as $rule){
                if(array_key_exists($rule, $sdata)){
                    unset($sdata[$rule]);
                }
             }

              static::$instance->sessionService->write('__chunked_fileuploads', $sdata);
         }

         return $results;

     }

    /**
     * Retrieves the content type of a given HTTP request
     *
     *
     *
     *
     * @param void
     * @return string
     */

     public static function contentType(){
           if(static::$instance !== NULL){
                return static::$instance->getFormat();
           }
           return NULL;
     }


     /**
       * Returns the auth credentials for the request.
       * (usually for 'Basic' or 'Digest' Authorization)
       * 
       * @param void
       * @return string
       */

      public static function getAuth(){
        
          
          $authu = static::$instance->getInfo('PHP_AUTH_USER');
          $authp = static::$instance->getInfo('PHP_AUTH_PW');
          $auth_credentials = '';
        
          if ($authu != '') {
                $auth_credentials .= $authu;
                if ($authp != '') {
                    $auth_credentials .= ':'.$authp;
                }
          }else{
              $authu = static::$instance->getInfo('PHP_DIGEST_USER');
              $authp = static::$instance->getInfo('PHP_DIGEST_PW');
              if($authu != ''){
                  $auth_credentials .= $authu;
                  if($authp != ''){
                      $auth_credentials .= ':'.$authp;
                  }
              }
          }
        
          return $auth_credentials;
    }

     

    /**
     * Retrieves the ip address of a given HTTP request
     *
     *
     *
     * @param void
     * @return string
     */

     public static function ip(){

          return static::$instance->getIp(); 
     }

    /**
     * 
     *
     *
     *
     *
     * @param string $key
     * @return string
     */

     public static function hasCookie($key){

          return array_key_exists($key, $_COOKIE);
     }

    /**
     * 
     *
     *
     *
     *
     * @param string $key
     * @return string
     */

     public static function getCookie($key){

          $queue = array();

          if(array_key_exists('app', $GLOBALS)){

              $queue = $GLOBALS['app']->getCookieQueue();

          }

          if(array_key_exists($key, $queue)){

              return $queue[$key];
          }

          if(static::hasCookie($key)){

               return $_COOKIE[$key];
          }

          return NULL;
     }

 }

?>