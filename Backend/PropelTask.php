<?php
namespace Glorpen\QueueBundle\Backend;

use Glorpen\QueueBundle\Queue\Task as BaseTask;

/**
 * @author Arkadiusz Dzięgiel
 */
class PropelTask extends BaseTask {
	
	protected $task;
	
	public function __construct($task){
		$this->task = $task;
	}
	
	public function getService() {
		return $this->task->getService();
	}
	public function getArgs() {
		return $this->task->getArgs();
	}
	public function getMethod() {
		return $this->task->getMethod();
	}
	public function getPid(){
	    return $this->task->getPid();
	}
	public function getId(){
	    $name = $this->task->getName();
	    if($name) return $name;
	    
	    return $this->task->getId();
	}
	public function getStatus(){
		return $this->task->getStatus();
	}
	
	public function getModel(){
		return $this->task;
	}
	
	public function getStartTime(){
	    return $this->task->getStartedAt();
	}
	
	public function getModelProgress(){
		return $this->task->getProgress();
	}
	
}
