<?php

namespace Glorpen\QueueBundle\Services;

use Glorpen\QueueBundle\Event\TaskEvent;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Psr\Log\LoggerInterface;

use Monolog\Logger;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Glorpen\QueueBundle\Queue\Task;

use Glorpen\QueueBundle\BackendInterface;

/**
 * Main queue service.
 * @author Arkadiusz Dzięgiel
 */
class Queue {
	
	protected $backend;
	protected $container;
	protected $logger;
	protected $dispatcher;
	
	public function __construct(ContainerInterface $container, BackendInterface $backend, EventDispatcherInterface $dispatcher, LoggerInterface $logger = null){
		$this->backend = $backend;
		$this->container = $container;
		$this->logger = $logger;
		$this->dispatcher = $dispatcher;
	}
	
	private function log($level, $message, array $context = array()){
		if($this->logger)
		$this->logger->{$level}($message, $context);
	}
	
	public function addTask($taskObject){
		$this->log('debug', 'Adding new task');
		$this->backend->add($taskObject);
	}
	
	public function createTask(){
		$this->log('debug', 'Creating empty');
		return $this->backend->createTask();
	}
	
	public function run($limit = 5){
		$this->log('debug', 'Locking and fetching pending tasks');
		$tasks = $this->backend->startPending($limit);
		
		foreach($tasks as $task){
			$this->dispatcher->dispatch('glorpen.queue.task_start', new TaskEvent($task));
			try {
				$this->log('info', sprintf('Executing task %s', $task->getName()));
				if($task->execute($this->container)){
					$this->log('info', sprintf('Marking task %s as ok', $task->getName()));
					$this->backend->markDone($task, BackendInterface::STATUS_OK);
				}
			} catch(\Exception $e){
				$this->log('error', sprintf('Marking task %s as failed', $task->getName()), array('exception'=>$e));
				$this->backend->markDone($task, BackendInterface::STATUS_FAILURE);
			}
			$this->dispatcher->dispatch('glorpen.queue.task_end', new TaskEvent($task));
			$this->log('info', sprintf('Task %s ended after %d seconds', $task->getName(), $task->getExecutionTime()));
		}
	}
	
	public function unlockCrashed($timeDiff){
		$this->log('info', 'Unlocking crashed tasks');
		$count = $this->backend->unlockCrashed($timeDiff);
		$this->log('info', sprintf('Unlocked %d crashed tasks', $count));
		return $count;
	}
	
	public function restartFailed(){
		$this->log('info', 'Restarting failed tasks');
		$count = $this->backend->restartFailed();
		$this->log('info', sprintf('%d failed tasks marked as pending', $count));
		return $count;
	}
}
