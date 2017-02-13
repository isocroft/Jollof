<?php

 /*--------------------------------------------------------
  !
  ! Application middlewares are needed to ensure that certain
  ! route requests meetup with special prerequisites before
  ! these requests are allowed to PASS and the route activated
  !
  ! We set up middlewares here...
  !
  !
  !* Jollof Framework (c) 2016
  !*
  !* {setup.php}
  !*
  !* NOTE: DON'T CHANGE THE ORDER OF THE MIDDLEWARES HERE
  !*       ADD YOUR CUSTOM MIDDLEWARES AT THE BOTTOM OF THIS
  !*       FILE
  !-------------------------------------------------------*/

  /*
   * Initilally, every client is logged in as a guest (only if there is no active user identity)
   */

  System::middleware('redirectIfUser', function($currentRoute, $auth){
      $result = TRUE;

       /*
        * route permissions (Access Reaffirmation)
        * ---------------------------------------
        * Feel free to modify this middleware to
        * suit the needs of the app
        */

        if(in_array($currentRoute, $auth->getGuestRoutes())){
          if(Auth::check($currentRoute)){
                $role = $auth->getUserRole();
                if(Request::method() == 'GET'){
                    if($currentRoute == '/account/reset-password'){

                        return Response::redirect('/account/forced-logout');
                    }

                    return Response::redirect('/' . strtolower($role));
                }else{
                    /*
                     * Modify this section if you would like to do something custom
                     * for other HTTP request types (e.g. POST, PUT, DELETE, PATCH)
                     */
                    return Response::text("Access-Control: You don't have permission to access this resource");
                }

          }
        }


      return $result;
  });

  System::middleware('redirectIfGuest', function($currentRoute, $auth){
      $result = TRUE;

      /*
       * route premissions (Access Control)
       * ----------------------------------
       * Feel free to modify this middleware to
       * suit the needs of the app
       */

      if(!(in_array($currentRoute, $auth->getGuestRoutes()))){
            if(!Auth::check($currentRoute)){
                $auth->setReturnToUrl($currentRoute);
                if(Request::method() == 'GET'){
                  return Response::redirect('/account/login' . '?return_to=' . rawurlencode($currentRoute));
                }
            }
      }

      return $result;
  });

  System::middleware('csrf', function($currentRoute){
      $result = TRUE;
      $token = '';

      /**
       * request forgery check (Trust Appraisal)
       *---------------------------------------
       * Feel free to modify this middleware to
       * suit the needs of the app
       */

      if(Request::method() == 'POST'){
              if(Request::isAjax()){
                  $token = array();
                  $token['_token'] = Request::rawHeader('X-CSRF-Token');
              }else{
                  $token = Request::input()->getFields(array('_token'));
              }
             if(isset($token)
                && $token['_token'] !== Session::token()){
                  $result = FALSE;
             }
      }

      return $result;
  });

  System::middleware('responseHeaders', function($currentRoute){
         $result = TRUE;
         $acceptable = Request::rawHeader('Accept');

        // if the browser request is for HTML payload/view/resource then ...

        if(index_of($acceptable, 'text/html') > -1){
          // don't allow this resource to be loaded into a frame (<iframe>, <frameset>) except by same origin {prevent click-jacking}

          // This overrides {JollofSecureHeaders} feature settings
          Response::header("X-Frame-Options",  "SAMEORIGIN");
        }

        return $result;
  });

?>
