<?php

  /*!------------------------------------------------------
    !
    ! This is the config section for all DB related settings
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

    # ("mysql", "sqlite", "mongodb")

    return array(

      "msr_enabled" => false, // Master-Slave Replication

      "engines" => array(

            "mysql" => array (

                     "hostname" => "********", # Database Host Name

                     "accessname" => "*******", #  Database Name

          	         "driver" =>  "PDO",

                	   "charset"   => 'utf8',

                	   "collation" => 'utf8_unicode_ci',

                     "settings" =>  array(


                				      PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,

                              PDO::ATTR_EMULATE_PREPARES => true,

                				      PDO::ATTR_AUTOCOMMIT => false,

                              PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",

                              PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                              PDO::ATTR_CASE => PDO::CASE_NATURAL,

                              PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,

                              PDO::ATTR_STRINGIFY_FETCHES => false,

                              PDO::ATTR_PERSISTENT  => false
                      )
          ),

          "pgsql" => array(
          
          ),  

          "sqlite" => array (


           ),

          "mongodb" => array(


          )

     )

);


?>
