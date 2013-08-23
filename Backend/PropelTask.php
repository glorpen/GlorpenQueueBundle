<?php
namespace Glorpen\QueueBundle\Backend;

use Glorpen\QueueBundle\Queue\Task as BaseTask;

use Glorpen\QueueBundle\Model\Propel\Task;

class PropelTask extends BaseTask {
	
	protected $task;
	
	public function __construct(Task $task){
		$this->task = $task;
	}
	
	
	public function getService() {
		return $this->task->getService();
	}
	public function getArgs() {
		return $this->getArgs();
	}
	public function getMethod() {
		return $this->task->getMethod();
	}
	public function getPriority() {
		return $this->task->getPriority();
	}
	public function getWhen() {
		return $this->task->getExecuteOn();
	}
	public function getStartTime() {
		return $this->task->getStartedOn();
	}

	
	public function getModel(){
		return $this->task;
	}
	
}