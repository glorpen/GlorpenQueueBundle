<?php

namespace Glorpen\QueueBundle\Backend;

use Glorpen\QueueBundle\BackendInterface;

class Propel implements BackendInterface {
	
	/**
	 * @param Task $task
	 */
	public function add(Task $task) {
		// TODO: Auto-generated method stub
	}
	
	public function lockPending() {
		// TODO: Auto-generated method stub
	}
	
	public function setStatus($status) {

	}

}