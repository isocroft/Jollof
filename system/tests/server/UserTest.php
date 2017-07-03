<?php

// namespace App\UnitTesting;

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


class UserTest extends TestCase {

    /**
     * @var User -
     */

     protected $user;

    /**
     * Constructor.
     *
     * @param void
     *
     * @scope public
     */

     public function __construct(){

          parent::__construct(array('User')); 

     }

     public function setUp(){

         parent::setUp();
      
         $this->user = $this->mockObjects['User'];
          
     }

     public function testUserTableName(){

            $attributes = $this->user->getAttributes();

            $this->assertEquals('tbl_app_user', $attributes['table']);
     }


     public function __call($method, $args){

         if (in_array($method, ['get', 'post', 'put', 'patch', 'delete']))
         {
               return $this->call($method, $args[0]);
         }
 
         throw new BadMethodCallException;
     }

}


?>