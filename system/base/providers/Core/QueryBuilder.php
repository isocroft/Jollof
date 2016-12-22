<?php

/*!
 * Jollof (c) Copyright 2016
 *
 * {QueryBuilder.php}
 *
 */

namespace Providers\Core;

use \PDO;
use Providers\Core\QueryExtender as QueryExtender;

class QueryBuilder {

	/**
	 * @var Providers\Core\QueryExtender
	 */

    protected $extender;

    /**
     * @var array
     */

    protected $schemaAttribs;

    /**
	 * Constructor
	 *
	 *
	 * @param PDO $connection
	 * @param array $paramTypes
	 * @api
	 */

	public function __construct(PDO $connection, array $paramTypes){

       $this->extender = new QueryExtender($connection, $paramTypes);

	}

	/**
	 *
	 *
	 *
	 *
	 *
	 * @return void
	 */

	public function setAttributes(array $attribs){

        $this->extender->setAttributes($attribs);
	}

	/**
	 * Executes an SELECT query
	 *
	 *
	 *
	 *
	 * @return \Providers\Core\QueryExtender
	 */

	public function select(array $columns, array $clauseProps, $conjunction){

        return $this->extender->get($columns, $clauseProps, $conjunction);
	}

	/**
	 * Executes an INSERT query
	 *
	 *
	 *
	 *
	 * @return \Providers\Core\QueryExtender
	 */

	public function insert(array $values, array $clauseProps){

       return $this->extender->set(array_keys($values), array_values($values), $clauseProps);
	}

	/**
	 * Executes an UPDATE query
	 *
	 *
	 *
	 *
	 * @return \Providers\Core\QueryExtender
	 */

	public function update(array $columnValues, array $clauseProps, $conjunction){

        return $this->extender->let($columnValues, $clauseProps);
	}

	/**
	 * Executes an DELETE query
	 *
	 *
	 *
	 *
	 * @return \Providers\Core\QueryExtender
	 */

	public function delete(array $columns, array $clauseProps){

       return $this->extender->del($columns, $clauseProps);
	}

	
}