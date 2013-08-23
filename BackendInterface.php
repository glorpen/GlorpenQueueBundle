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
	public function add($task);
	
	/**
	 * Marks tasks as started.
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
	 * Returns newly created task.
	 */
	public function createTask();
	
	/**
	 * Marks old locked tasks as pending.
	 * @param \DateInterval $timeDiff
	 * @return integer number of unlocked tasks
	 */
	public function unlockCrashed(\DateInterval $timeDiff);
	
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
	
	/**
	 * Returns queue stats.
	 * @return array
	 */
	public function getStats();
}
