<?php

/*!------------------------------------------------------
!
! This is the routes file for configuring all route urls
! and their corresponding HTTP verb, models and route
! parameters.
!
!
! * Jollof (c) Copyright 2016 - 2017
! *
! *
! * {setup.php}
! *
! *
----------------------------------------------------------*/

 Router::bind('/', array('inject' => array()));

 Router::bind('/webhook/git-payload/@gitaccountname/@gitprojectname', array('verb' => 'post', 'inject' => array(), 'params' => array('gitaccountname' => '/^([a-zA-Z0-9-]+)$/', 'gitprojectname' => '/[a-zA-Z-_]/')));

 Router::bind('/chats/messaging', array('verb' => 'post', 'inject' => array()));

 Router::bind('/admin', array('inject' => array()));

 Router::bind('/account/login/', array('inject' => array()));

 Router::bind('/account/register/', array('inject' => array()));

 Router::bind('/account/signin/@provider', array('inject' => array(), 'verb'=> 'post', 'params'=> array('provider' => '/^(?:(?:oauth-([a-z]+))|email)$/i')));

 Router::bind('/account/signup/@mode', array('inject' => array(), 'verb' => 'post', 'params' => array('mode' => '/^create$/i')));

