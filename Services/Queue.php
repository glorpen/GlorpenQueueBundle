<?php

namespace Glorpen\QueueBundle\Services;

use Glorpen\QueueBundle\Queue\Task;

use Glorpen\QueueBundle\BackendInterface;

class Queue {
	
	protected $backend;
	
	public function __construct(BackendInterface $backend){
		$this->backend = $backend;
	}
	
	public function registerTask(Task $task){
		$this->backend->add($task);
	}
	
	public function run(){
		$tasks = $this->backend->lockPending();
		
		foreach($tasks as $task){
			try {
				if($task->execute()){
					$this->backend->setStatus($task, BackendInterface::STATUS_OK);
				}
			} catch(\Exception $e){
				$this->backend->setStatus($task, BackendInterface::STATUS_OK);
			}
		}
	}
}