<?php

namespace Jollof\ErrorReporter;

/**
 * Jollof (c) Copyright 2016
 *
 *
 * @package    Jollof\ErrorReporter
 * @version    0.0.4-beta.3
 * @author     Ifeora Okechukwu.
 * @license    MIT License
 * @copyright   Mobicent, Ltd.
 * @link http://github.com/isocroft/Jollof
 */

use \Exception;
use \Comms;

class Reporter {

    /**
     * @var Comms -
     */

	protected $communicator;

    /**
     * @var array -
     */

    protected $settings;

    /**
     * Constructor
     *
     * @param Comms $communicator
     */

    public function __construct(Comms $communicator, array $settings = array()){

        $this->settings = $settings;

        if(class_exists('Bugsnag_Client')){

             if($this->settings['driver'] == "#bugsnag"){
            
                $this->communicator = new \Bugsnag_Client($this->settings['key']);
                $this->communicator->setFilters([
                    'password'
                ]);

                $this->communicator->setBeforeNotifyFunction(function($error){
                      $error->setMetaData(
                         $this->settings['meta_data']
                      );
                });

             }else{

                 $this->communicator = $communicator;
             }

        }else{

            if($this->settings['driver'] == "#native"){

                $this->communicator = $communicator;

            }
        }
        
  
    }

    /**
     * Sends the specific error details to the destination reporter server/service endpoint
     *
     * @param string $host
     * @param array $descriptors
     * @param callable $callback
     * @return mixed $result
     */

    public function sendError(Exception $ex, array $descriptors, callable $callback){

            $host = $this->settings['host'];

    		if(!is_callable(($callback))){

    			return NULL;
    		}

            $className = get_class($this->communicator);

            if(index_of($className, 'Bugsnag') > -1){

                $result = $callback($this->communicator->notifyException($ex, NULL, 'error'));

            }else{

    		    $result = $callback($this->communicator->curl($host, 80, $descriptors));

            }

            return $ex;

    }
}


?>
