<?php

namespace Jollof\QueueService;

/**
 * Jollof Framework - (c) 2016
 *
 *
 * @author Ifeora Okechukwu
 * @license    MIT License
 * @copyright   Mobicent, Ltd.
 * @link htps://github.com/isocroft/Jollof
 */


abstract class Job {

	/**
     * Constructor.
     *
     * @param void
     *
     * @scope private
     */

	public function __construct(){

	}

	public function setUp() {
       
       	# Set up something before perform, like establishing a database connection
    }	

    public function perform() {

		$__method = array_shift($this->args);
        call_user_func_array(array(&$this, $__method), $this->args['args']);
    }

	public function tearDown() {
       
       	# Run after perform, like closing resources
    }


}


?>