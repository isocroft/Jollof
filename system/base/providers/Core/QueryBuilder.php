<?php

/*!
 * Jollof (c) Copyright 2016
 *
 * {QueryBuilder.php}
 */

namespace Providers\Core;

use Providers\Core\QueryExtender;

class QueryBuilder {

    protected $extender;

    protected $schemaAttribs;

	public function __construct($connection, $paramTypes){

       $this->extender = new QueryExtender($connection, $paramTypes);

	}

	public function setAttributes(array $attribs){

        $this->extender->setAttributes($attribs);
	}

	public function select(array $columns, array $clauseProps, $conjunction){

        return $this->extender->get($columns, $clauseProps, $conjunction);
	}

	public function insert(array $values){

       return $this->extender->set(array_keys($values), array_values($values));
	}

	public function update(array $columns){

        return $this->extender->let($columns);
	}

	public function delete(array $columns){

       return $this->extender->del($columns);
	}

	
}