<?php

namespace Jollof\DBSyncer;

/**
 * Jollof (c) Copyright 2016
 *
 *
 * @package    Jollof\DBSyncer
 * @version    0.9.9
 * @author     Ifeora Okechukwu.
 * @license    MIT License
 * @copyright   Mobicent, Ltd.
 */

class Sync {

	/**
	 * @var array
	 */

	protected $config;

	/**
     * Constructor
     *
     * @param array
     */

    public function __construct(array $syncConfig = array()){

    	$this->config = $syncConfig;
  
    }
}

?>