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


class Notification extends Job {

	public function sendMailToUser() {}

    public function sendMailToAdmin() {}

}


/* 
	Resque::enqueue(
				'default', 
				'Jollof\QueueService\Notification', 
				array(
					'method' => 'sendMailToAdmin', 
					'args'	=> array( 
					    'to' => 'mail@jollofadmin.com', 
					    'subject' => 'hi!', 
					    'body' => 'this is a test content'
					)
	);
*/


?>