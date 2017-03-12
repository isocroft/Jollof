<?php

/*!
 * Jollof (c) Copyright 2016
 *
 *
 * {QueryBuilder.php}
 *
 */

use \Contracts\Policies\QueryProvider as QueryProvider;

namespace Providers\Core;

class QueryBuilder {

	/** 
	 * @var string - 
	 */

	protected $db_driver;

	/**
	 * @var object - Providers\Core\QueryProvider
	 */

    protected $provider;

    /**
     * @var array - 
     */

    protected $schemaAttribs;

    /**
	 * Constructor
	 *
	 *
	 *
	 * @param \Contracts\Policies\QueryProvider $provider
	 * @api
	 */

	public function __construct(QueryProvider $provider){

       		$this->provider = $provider;
	}

	/**
	 *
	 *
	 *
	 *
	 *
	 * @return void
	 */

	public function setAttributes(array $attribs = array()){

        $this->provider->setAttributes($attribs);
	}

	/**
	 * Executes an SELECT query
	 *
	 *
	 *
	 *
	 * @return \Contracts\Policies\QueryProvider
	 */

	public function select(array $columns, array $clauseProps, $conjunction){

        return $this->provider->get($columns, $clauseProps, $conjunction);
	}

	/**
	 * Executes an INSERT query
	 *
	 *
	 *
	 *
	 * @return \Contracts\Policies\QueryProvider
	 */

	public function insert(array $values, array $clauseProps){

       return $this->provider->set(array_keys($values), array_values($values), $clauseProps);
	}

	/**
	 * Executes an UPDATE query
	 *
	 *
	 *
	 *
	 * @return \Contracts\Policies\QueryProvider
	 */

	public function update(array $columnValues, array $clauseProps, $conjunction){

        return $this->provider->let($columnValues, $clauseProps);
	}

	/**
	 * Executes an DELETE query
	 *
	 *
	 *
	 *
	 * @return \Contracts\Policies\QueryProvider
	 */

	public function delete(array $columns, array $clauseProps){

       return $this->provider->del($columns, $clauseProps);
	}

	/**
	 *
	 *
	 *
	 * @return void 
	 *
	 */

	public function setDriver($db_driver){

		$this->db_driver = $db_driver;
	}

	/**
	 *
	 *
	 *
	 *
	 * @return object
	 */

	public function createEntity(array $schema){

		;
	}


}