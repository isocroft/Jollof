<?php

/*!
 * Jollof Framework (c) 2016
 *
 * {Response.php}
 *
 */

use \Request;
use \System;
use \Session;

use \Providers\Tools\TemplateRunner as Runner;

final class Response {

    /**
     * @var Response
     */

     private static $instance = NULL;

    /**
     * @var \Providers\Tools\TemplateRunner - logic that compiles view (template) 
     *                                        files into php files and renders to the 
     *                                        client/browser/user-agent
     */

     private $runner;

     /**
      * @var array - list of HTTP response headers used to validate the 
      *              client/browser/user-agent cache
      */

     private $validators;

    /**
     * @var bool - flag for stating whether the server supports the 
     *             compression/encoding scheme indicated by the 
     *             client/browser/user-agent in the 'Accept-Encoding' HTTP request header
     */

     private $compressionStatus;

    /**
     * Constructor.
     *
     * @param void
     *
     * @scope private
     */

     private function __construct(array $viewNonces){

            $this->openOutputBuffers();

            $this->runner = new Runner($viewNonces);
     }

    /**
     * sets up output buffers for the client
     *
     * @param void
     * @return void
     *
     * @scope private
     */

     private function openOutputBuffers(){

          $compressionEnabled = (ini_get('zlib.output_compression') == 'On' ||
                        ini_get('zlib.output_compression_level') > 0);
          
          $gzipEnabled = (ini_get('output_handler') == 'ob_gzhandler');
          $deflateEnabled = false;

          $encodingList = explode(', ', static::getInfo('HTTP_ACCEPT_ENCODING'));
          $supportsCompression = false;

          if(!is_array($encodingList)
              || count($encodingList) == 0){

              $encodingList = array("");
          }

         if($gzipEnabled && index_of($encodingList[0], 'gzip') > -1){
              $supportsCompression = ob_start("ob_gzhandler");
              /*
                setting "Content-Encoding" to 'gzip' when a browser can't handle gzip-ed content 
                makes the browser choke on the byte stream so it's better to avoid it completely
              */
              if(!$supportsCompression){
                  ob_start();
                  ob_implicit_flush(0); // stop implicit flush from happening so we can set response headers later before output is sent
              }
         }else if(index_of($encodingList[0], 'deflate') > -1){
              ob_start("ob_deflatehandler"); 
         }else{
              if(!$compressionEnabled 
                  || (ob_get_level() == 0)){
                  ob_start();
              }    
         }

         $this->compressionStatus = $supportsCompression;
     }

     /**
      *
      *
      *
      * @param array $validators -
      * @return void
      */

     public function setCacheValidators(array $validators){

            $this->validators = $validators;
     }

    /**
     * Factory to supply instance
     *
     * @param void
     * @return Response
     *
     * @api
     */
     public static function createInstance(array $viewNonces){
         if(static::$instance == NULL){
               static::$instance = new Response($viewNonces);
               return static::$instance;
         }
     }

    /**
     * Sets up HTTP response header
     *
     * @param string $key
     * @param string $value
     * @return void
     *
     * @api
     */

     public static function header($key, $value, $replace = TRUE){

          if(headers_sent()){

              return false;
          }

          return header($key . ': ' . $value, $replace);
     }

    /**
     * Retrieves HTTP Server details
     *
     * @param string $var
     * @return string $value
     *
     *
     * @scope private
     */

     private static function getInfo($var){
       $value = '';
       if(array_key_exists($var, $_SERVER)){
            $value = $_SERVER[$var];
       }else if(array_key_exists($var, $_REQUEST)){
            $value = $_REQUEST[$var];
       }
       return $value;
     }

    /**
     * Returns text data back to the client
     *
     * @param string $data
     * @param int $statusCode
     * @return bool
     *
     * @api
     */

     public static function text($data, $statusCode = 200){

        if(index_of(Request::header('HTTP_ACCEPT'), 'text/event-stream') > -1){

              static::header('Content-type', 'text/event-stream');

              static::header('Cache-Control', 'no-cache');

              if(!is_null($data)){

                  $data .= PHP_EOL;
              }
        }else{

             static::header('Content-type', 'text/plain; charste=UTF-8');
        }

          http_response_code(intval($statusCode));

          return static::end($data, 'text');
     }

     public static function json(array $data, $statusCode = 200){

          static::header('Vary', 'Accept');

          if(index_of(Request::header('HTTP_ACCEPT'), 'application/json') > -1){

               static::header('Content-type', 'application/json; charset=UTF-8');

          }else{

               static::header('Content-type', 'text/plain; charste=UTF-8');
          }

          /* @TEMPORARY - to be removed later */
          static::header('Cache-Control', 'max-age=600');

          http_response_code(intval($statusCode));

          return static::end(json_encode($data), 'text');

     }

     public static function status($code){

         http_response_code(intval($code));

         return static::end(null);
     }

     public static function error(\Exception $e){

          static::header('Content-type', 'text/plain; charset=UTF-8');

          return static::end($e->getTraceAsString(), 'text');

     }

     public static function file($filename){

        $file = preg_replace('/[\x5c]/i', '/', realpath($filename));
        //$ftype = mime_content_type($file);
        $ftype = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);

        if($ftype == 'text/x-c++'){

              $ftype = 'text/plain';
        }
        
        static::header('Content-Type', $ftype);

        $contents = File::read($file);

        static::header('Content-Length', (is_string($contents)? strlen($contents) : filesize($file)));

        return static::end($contents);
     }

     public static function download($filename){

          $file = preg_replace('/[\x5c]/i', '/', realpath($filename));

          static::header('Content-Description', 'File Transfer');

          $ftype = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
          
          if($ftype == 'text/x-c++'){
            
              $ftype = 'text/plain';
          }
          
          static::header('Content-Type', $ftype);
          // static::header('Content-Disposition', 'inline; filename="' . get_file_name($file, true) . '"');
          static::header('Content-Disposition', 'attachment; filename="' . get_file_name($file, true) . '"');

          $contents = readfile($file);

          return static::end($contents);
     }

     /**
      *
      *
      *
      *
      * @param string $name -
      * @param array $data -
      * @param array $config -
      */

      # Response::view('user/index', array('title' => 'Jollof'), array('serverPush' => '.css'));

     public static function view($name, $data = array(), $config = array()){

        if(!array_key_exists('asXML', $config)){

            static::header('Content-Type', 'text/html; charset=UTF-8');

        }else{
            if($config['asXML'] === TRUE){

                static::header('Content-Type', 'application/xml'); 
            }
        }

        if(array_key_exists('serverPush', $config)){

            if($config['serverPush'] == '.css'){

              /* $assets = basename($GLOBALS['env']['app.path.assets']); */

              $publicPath = $GLOBALS['env']['app.path.public'];

              $public = basename($publicPath);

              $rootPath = Request::getOrigin();

              $rootPath .= (str_replace('public/index.php', '', static::getInfo('PHP_SELF')));

              /* $rootPath .= $public . '/' . $assets; */

              /* 
                 Always use 'GLOB_NOSORT' ...
                 it takes away performance costs by not returning
                 file names in a sorted order. 
              */

              $cssFiles = rglob($publicPath . '/{*.css}', GLOB_BRACE|GLOB_NOSORT);

              /* @TODO: Optimize the 'Link' header inclusion with a flag in the session to avoid sending the response header every time */

              if($cssFiles !== FALSE 
                  && is_array($cssFiles)){

                      $headerParts = implode(', ',  array_mapper('http_link_header', $cssFiles, $rootPath));

                      static::header("Link", $headerParts); 
                  ;
              }
           }   

        }

        /* @TEMPORARY - to be removed later */
        static::header('Cache-Control', 'max-age=600');

        $content = static::$instance->runner->render($name, $data);

        if(!static::$instance->compressionStatus){

            // @TODO: see if ob_get_length(); works better than strlen($content); below
            static::header('Content-Length', strlen($content)); 

            static::header('Content-Encoding', 'none');

        }

        if(array_key_exists('statusCode', $config)){

             http_response_code(intval($config['statusCode']));
        }

        return static::end($content, 'view');

     }

     private static function end($data, $from = ''){

            if(strtolower(Request::rawHeader('Connection')) === 'keep-alive'){

                    /* - OPTIMIZATION
                      we need to suspend the request for some time so the user-agent/browser 
                      can make multiple HTTP requests on a single TCP connection (in case the web application server doesn't do so - Apache/Nginx/ e.t.c)
                    */
                    Response::header("Keep-Alive", "timeout=15, max=100"); 
                    // Response::header("Connection", "keep-alive");
            }

            if(function_exists('fast_cgi_finish_request')) {

                fast_cgi_finish_request();

            }else if('cli' !== PHP_SAPI){

                static::$instance->closeOutputBuffers(0, true, $from);
            }


            if((!is_null($data)) || (!static::isEmpty())){
                    echo $data;
            }

            return TRUE;
     }

     private function closeOutputBuffers($targetLevel, $flush, $type){

        $status = ob_get_status(true); 
        $level = count($status); 
        
        // @TODO: check if {ob_get_status(FULL_STATUS);} works as well as {ob_get_level();}
        $oLevel = ob_get_level();
        
        $contentLen = 0;

        /*
            while(--$oLevel != 0){

                $contentLen += ob_get_length();
            }

            header('Content-Length: ' . $contentLen);
        */

        while ($level-- > $targetLevel
            && (!empty($status[$level]['del'])
                || (isset($status[$level]['flags'])
                    && ($status[$level]['flags'] & PHP_OUTPUT_HANDLER_REMOVABLE)
                    && ($status[$level]['flags'] & ($flush ? PHP_OUTPUT_HANDLER_FLUSHABLE : PHP_OUTPUT_HANDLER_CLEANABLE))
                )
            )
        ) {

            /* 
                @TODO:  See how CUSTOM "Content-Length" header can be set for buffered
                        gzip-ed (compressed) output so certain browsers don't choke on the byte stream using {ob_get_length()}  for each open output buffer [$contentLen]
            */

            $contentLen += ob_get_length(); 

            if($flush){
                if($type === 'text'
                    || $type === 'view'){
                    ob_flush(); // this will trigger the "ob_gzhandler" callback
                    flush();
                }
            }

        }

     }

     /**
      * Checks if response will have an entity body or not
      *
      * @use Response::isEmpty();
      *
      * @param void
      * @return bool
      *
      * @api
      */

     public static function isEmpty(){

        return in_array(http_response_code(), array(204, 304)); // 'No Content' OR 'Not Modified'
    }

     /**
      * Sets up redirection for client response
      *
      * @use Response::redirect('/');
      *
      * @param string $route
      * @param bool $temporary
      * @return bool
      *
      * @throws InvalidArgumentException
      * @api
      */

     public static function redirect($route, $temporary = TRUE){

           if(!isset($route) || empty($route)){

                throw new \InvalidArgumentException("Cannot redirect to unknown destination");
           }

           $root = $GLOBALS['env']['app.root'];

           $host = Request::getHost();

           if(contains($route, $root)){
              $route = str_replace($root, '', $route);
           }

           if(!starts_with($route, "/")){
               $route = "/" . $route;
           }

           if(!starts_with($root, "/")){
               $root = "/" . $root;
           }

           $protocol = Request::getProtocol();
           $_uri = Request::header('REQUEST_URI');

            $url = (contains($_uri, $root) && (php_sapi_name() == 'apache' || php_sapi_name() == 'apache2handler'))? ($host . $root . $route) : ($host . $route);

           http_response_code(($temporary)? 302 : 301);

           static::header('Location', "{$protocol}://{$url}");

           return TRUE;

     }

     public static function redirectBack(){

          $origin = Request::getOrigin() . '/';

          $backUrl = str_replace($origin, '', Request::referer());

          if(!isset($backUrl)){

              $backUrl = Session::get('previousRoute');
          }

          return static::redirect($backUrl);
     }

     public static function setCookie($key, $value){

          $config = $GLOBALS['env']['app.settings.cookie'];

          $GLOBALS['app']->pushCookieQueue($key, $value);

          $val = setcookie($key, $value, (time()+$config['max_life']), '/' , $config['domain_factor'], $config['secure'], $config['server_only']);
     }

}

?>
