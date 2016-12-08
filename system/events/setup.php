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
       
       Logger::error("[Jollof - " . $code . "]  " . $message . " in " . $file . " on line " . $line);

       switch ($status) {
       	case 'dev': # Development Environment
       	   return Response::view('errors/report', array('err' => $code, 'msg' => $message, 'file' => $file, 'line' => $line));
       	break;
       	case 'prod': # Staging/Production Environment
           # use the app error reporter or any external reporters e.g. BugSnag
           # code ...
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
