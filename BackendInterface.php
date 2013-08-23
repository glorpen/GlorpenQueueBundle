<?php 

namespace Glorpen\QueueBundle;

interface BackendInterface {
	
	const STATUS_OK = 'ok';
	const STATUS_FAILURE = 'fail';
	const STATUS_LOCKED = 'lock';
	const STATUS_PENDING = 'wait';
	
	public function add(Task $task);
	/**
	 * @return array of Task
	 */
	public function lockPending();
	public function setStatus($status);
}