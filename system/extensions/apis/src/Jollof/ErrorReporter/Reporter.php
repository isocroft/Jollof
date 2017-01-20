<?php

namespace Jollof\ErrorReporter;

/**
 * Jollof (c) Copyright 2016
 *
 *
 * @package    Jollof\ErrorReporter
 * @version    0.9.9
 * @author     Ifeora Okechukwu.
 * @license    MIT License
 * @copyright   Mobicent, Ltd.
 */

use \Comms;

class Reporter {

    /*
     * @var Comms
     */

	protected $communicator;

    /**
     * Constructor
     *
     * @param Comms $communicator
     */

    public function __construct(Comms $communicator){

    	$this->communicator = $communicator;

    }

    /**
     * Sends the specific error details to the destination server/sservice endpoint
     *
     * @param string $host
     * @param array $descriptors
     * @param callable $callback
     * @return mixed $result
     */

    public function sendError($host, array $descriptors, callable $callback){

    		if(!is_callable(($callback))){

    			return NULL;
    		}

    		$result = $callback($this->communicator->curl($host, 80, $descriptors));

            return $result;

    }
}


?>
