<?php 

namespace Glorpen\QueueBundle;

/**
 * An interface for custom backend implementations.
 * @author Arkadiusz Dzięgiel
 */
interface BackendInterface {
	
	const STATUS_OK = 'ok';
	const STATUS_FAILURE = 'failed';
	const STATUS_LOCKED = 'locked';
	const STATUS_PENDING = 'pending';
	
	/**
	 * Adds a new task object to queue.
	 * @param object $task
	 */
	public function create($service, $method, array $args, $executeOn = 'now');
	
	/**
	 * Marks tasks as started and sets current PID.
	 * @return array of Task
	 */
	public function startPending($limit);
	
	/**
	 * Marks task as done.
	 * @param object $task
	 * @param string $status
	 */
	public function markDone($task, $status);
	
	/**
	 * Marks task as started.
	 * @param object $task
	 */	
	public function markStarted($task);
	
	/**
	 * Returns locked tasks
	 * @return array of Task locked tasks
	 */
	public function getLocked();
	
	/**
	 * Marks failed tasks as pending.
	 * @return integer number of restarted tasks
	 */
	public function restartFailed();
	
	/**
	 * Removes successfull tasks.
	 * @return integer number of removed tasks
	 */
	public function cleanup();
	
	public function setProgress($task, $progress);
	
	public function findTask($id);
	
}
