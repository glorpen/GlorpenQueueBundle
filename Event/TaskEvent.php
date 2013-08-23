<?php
namespace Glorpen\QueueBundle\Event;

use Glorpen\QueueBundle\Queue\Task;

use Symfony\Component\EventDispatcher\Event;

class TaskEvent extends Event {
	
	protected $task;
	
	public function __construct(Task $task){
		$this->task = $task;
	}
	
	/**
	 * @return Task
	 */
	public function getTask(){
		return $this->task;
	}
}