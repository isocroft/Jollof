<?php

/*-------------------------------------------------------
 ! This is where application-wide events are setup.
 !
 !
 !
 !
 !* Jollof (c) Copyright 2016
 !*
 !*
 !* {setup.php}
 !*
 !*
 --------------------------------------------------------*/

 System::onAppError(function($code, $message, $file, $line){

       $status = $GLOBALS['env']['app.status'];

       $reporter = $GLOBALS['app']->getRemoteErrorReporter();

       Logger::error("[Jollof - " . $code . "]  " . $message . " in " . $file . " on line " . $line);

       switch ($status) {
       	case 'dev': # Development Environment
       	   return Response::view('errors/report', array('err' => $code, 'msg' => $message, 'file' => $file, 'line' => $line));
       	break;
       	case 'prod': # Staging/Production Environment
           # use the native reporter or any external reporters e.g. BugSnag

           /* uncomment this code when needed

              $ex = new \Exception($message, $code);

              $descriptors = array(
                  'method' => 'GET',
                  'client_id' => Session::id(),
                  'path' => '',
                  'params' => array(
                     'browser' => Request::header('HTTP_USER_AGENT'),
                     'timing' => Request::header('REQUEST_TIME'),
                     'details' => json_encode((compact('code', 'message', 'file', 'line')))
                  )
               );

               $reporter->sendError($ex, $descriptors, function($response){

               });
           */
       	break;
       	default:
       		 # code ...
       	break;
       }

 });

 System::onBlindRoute(function($route){
       // This view deals with all 404 errors - View/Page Not Found
       // (where a route could not be found on the routes table for the application)
       return Response::view('appstate/missing', array('url' => $route));
 });

 System::onFiltered(function($reqMethod, $route){

        # code ...
 });

?>
