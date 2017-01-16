<?php   

  /*!------------------------------------------------------
    ! 
    ! This is the config section for all ENV related settings
    !
    !
    !
    !
    ! * Jollof (c) Copyright 2016
    ! *
    ! *
    ! *
    ! *
    ! *
    --------------------------------------------------------*/


    return array (

        "app_environment" => "dev", #options: ("dev", "prod")

        "encryption_scheme" => "mcrypt", #options: ("mcrypt", "scrypt")

        "app_session" => array(

             'session_driver' => "#native",  #options: ("#native", "#redis")

             'session_name' => "JOLLOF_SESS_ID", # change the session name
          
              'sessions_host' => '127.0.0.1',
          
              'sessions_port' => 6379
        ),

        "app_errors" => array(

              'report_fatal' => TRUE,

              'reporter_settings' => array(

                   'host' => 'http://localhost',

                   'driver' => '#native', #options: ("#native", "#bugsnag")

                   'key' => '...', # <enter API Key for BugSnag here - if any>

                   'meta_data' => array( # <enter Report Meta Data here - if any>

                    )
              )
        ),

        "app_paths" => array (

            'base' => __DIR__ . '/../',
            
            'public' => __DIR__ . '/../public',

            'storage' =>  __DIR__ . '/../storage',

            'packages' => __DIR__ . '/../packages', # change the packages folder

            'views' => __DIR__ . '/../views' # change the views folder

        ),

        "app_security" => array(

            'strict_mode' => FALSE, #options: (FALSE, TRUE) ;

            'csp' => FALSE, // #options: (FALSE, TRUE, array(...)) ; Content-Security-Policy

            'hpkp' => FALSE, // #options: (FALSE, TRUE, array(...))

            'cspro' => FALSE, // #options: (FALSE, TRUE, array(...)) ;Content-Security-Policy-Reporting-Only:

            'noncify-inline-source' => TRUE // Generates a nonce value for each <script> and <style> tag code in your views 

        ),

        "app_cache" => array(

             'cache_driver' => "#memcached", #options: ("#memcached")

             'host' => '127.0.0.1',

             'port' => 11211

        ),     

        "app_auth" => array (
 
            'auth_users_model' => 'User',

            'auth_throttles_model' => 'UserThrottle',

            'auth_roles_model' => 'UserRole',

            'jwt_enabled' => true,

            'jwt_as_signed_cookie' => true,

            'throttle_enabled' => true,

            'extension' => array(

            ),

            'guest_routes' => array( # These routes can be accessed only if the user is not logged in (guest).
                 '/',
                 '/account/login/',
                 '/account/register/',
                 '/account/signup/@mode/',
                 '/account/signin/@provider/'
            )          
        ),

        "app_cookies" => array(
             
            'secure' => false, # {secure} HTTP or HTTPS

            'server_only' => true, # {httpOnly} hidden from client-side or not hidden from client-side 

            'domain_factor' => 'localhost', # {domain} 

            'max_life' =>  246000 # {expires} 
        ),

        "app_uploads" => array(

            'temp_upload_dir'=> NULL,

            'uploads_enabled'=> true, #options: (false, true)

            'upload_settings' => array( 
                    
                'upload_driver'=> "#native", #option: ('#aws-s3', '#imgix', '#cloudinary', '#native')

                'driver_id' => "...", # <enter Driver (Imgix Account ID / Cloudinary Cloud Name) here - if any>

                'key' => "...", # <enter API Key here - if any>

                'secret' => "...", # <enter API Secret here - if any>

                'region' => "...", # <enter Storage Location Name here - if any>

                'bucket' => "..." # <enter Storage Name here - if any>
            ), 

            'can_extract_zip' => true, #options: (false, true)

            'max_upload_size' => 1000000 /* 100MB */
        ),

        "app_mails" => array(

              'mail_driver' => "#mailgun", #options: ("#native", "#mailgun", "#php-mailer")

              'protocol' => "SMTP", #options: ("SMTP", "SMTP=POP3")

              'encryption' => true,

              'encryption_type' => 'tls',
              
              'mail_server' => 'smtp.mailgun.org',

              'key' => '...', # <enter API Key here - if any>
              
              'username' => "...",
              
              'password' => "...",

              'port' => 587
        ),

        "app_messaging" => array(

             'messenger_driver' => '#twilio', #options: ("#twilio")

             'driver_id' => '...', # <enter Twilio Account SID here - if any> [https://twilio.com/console]

             'token' => '...', # <enter Twilio Auth Token here - if any> [https://twilio.com/console]

             'number' => '...' # <enter Twilio Mobile/Phone Number here - if any> [https://twilio.com/console]
        ),

        "app_connection" => array(

             'connector_driver' => '#pusher', #options: ("#local", "#pusher")

             'text_events_enabled' => true,

             'sockets_enabled' => true,

             'key' => '...', # <enter Pusher API Key here - if any>

             'secret' => '...', # <enter Pusher API Secret here - if any>

             'driver_id' => '...' # <enter Pusher App Id here - if any>

        ),

        "image_processing_enabled" => false #options: (true, false)

    );

?>
