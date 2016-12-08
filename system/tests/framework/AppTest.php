<?php

namespace UnitTests;
use Providers\Core\App;

/**
 * Class AppTest
 */

class AppTest extends \PHPUnit_Framework_TestCase{

	/**
 	 * @var App
	 */

	protected $app;

	protected function setUp(){

		$this->app = new Providers\Core\App;
	}

	protected function tearDown(){

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
