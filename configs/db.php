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


    return array(

      "db_engine" => "mysql", # ("mysql", "pgsql", "mssql" "sqlite", "mongo")

      "msr_enabled" => false, // Master-Slave Replication

      "engines" => array(

            "mysql" => array (

                     "hostname" => "********", # Database Host Name

                     "accessname" => "*******", #  Database Name

          	         "driver" =>  "PDO",

                     "port" => "", # Database Port

                	   "charset"   => 'utf8',

                	   "collation" => 'utf8_unicode_ci',

                     "settings" =>  array(


                				      PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,

                              PDO::ATTR_EMULATE_PREPARES => true,

                				      PDO::ATTR_AUTOCOMMIT => false,

                              /* PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", */

                              PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                              PDO::ATTR_CASE => PDO::CASE_NATURAL,

                              PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,

                              PDO::ATTR_STRINGIFY_FETCHES => false,

                              PDO::ATTR_PERSISTENT  => false
                      )
          ),

          "pgsql" => array(
          
                      "hostname" => "********", # Database Host Name

                      "accessname" => "*******", #  Database Name

                      "driver" =>  "PDO",

                      "port" => "", # Database Port

                      "charset"   => 'utf8',

                      "collation" => 'utf8_unicode_ci',

                      "settings" => array(

                      )
          ),  

          "sqlite" => array (

                      "accessname" => __DIR__ . "../*******", #  Database Name (Path)

                      "driver" =>  "PDO"
           ),

          "mssql" => array(

                      "hostname" => "********", # Database Host Name

                      "accessname" => "*******", #  Database Name

                      "driver" =>  "PDO",

                      "port" => "", # Database Port
           ),

          /* First: Download MongoDB Driver from 
                      
                    [https://github.com/mongodb/mongo-php-driver/downloads]

             Second: extract the zip file and copy out [php_mongo.dll]

             Last: include line [extension=php_mongo.dll] in php.ini file
          */

          "mongo" => array(

                      "hostname" => "********", # Database Host Name

                      "accessname" => "*******", #  Database Name

                      "driver" =>  "mongo",

                      "port" => "27017" # Database Port

          )

     )

);


?>
