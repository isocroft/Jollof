<?php

// namespace Framework\UnitTesting;

/**
 * Jollof Framework - (c) 2016
 *
 *
 * @author Jollof Community
 * @license    MIT License
 * @copyright   Mobicent, Ltd.
 * @link htps://github.com/isocroft/Jollof
 */

use Jollof\Tests\TestCase as TestCase;
use Providers\Core\App;

/**
 * @class AppTest
 */

class AppTest extends TestCase{

	/**
 	 * @var App
	 */

	protected $app;

	/**
     * Constructor.
     *
     * @param void
     *
     * @scope public
     */

	public function __construct(){

		parent::__construct();
	}

	public function setUp(){

		$this->app = new Providers\Core\App;
	}

	public function tearDown(){

		$this->app = null;
	}

	public function itShouldReportCLIMode(){

		$expected = TRUE;
		$this->assertSame($expected, $this->app->inCLIMode());
	}

	public function itShouldHaveAnEmptyContainer(){

		$expected = FALSE;
		$this->assertSame($expected, $this->app->hasInstances());
	}

}

?>
