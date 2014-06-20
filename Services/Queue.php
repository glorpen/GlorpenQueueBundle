<?php

namespace Glorpen\QueueBundle\Services;

use Glorpen\QueueBundle\Event\TaskEvent;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
	
	private $task;
	
	public function __construct(ContainerInterface $container, BackendInterface $backend, EventDispatcherInterface $dispatcher, /*Psr\Log\LoggerInterface*/ $logger = null){
		$this->backend = $backend;
		$this->container = $container;
		$this->logger = $logger;
		$this->dispatcher = $dispatcher;
	}
	
	private function log($level, $message, array $context = array()){
		if($this->logger){
			if($this->logger instanceof \Psr\Log\LoggerInterface){
				$this->logger->{$level}($message, $context);
			} else if($this->logger instanceof \Monolog\Logger){
				$this->logger->{'add'.ucfirst($level)}($message, $context);
			}
		}
	}
	
	public function create($service, $method, array $args, $executeOn = 'now', $name = null){
		if($name){
		    $t = $this->backend->findTask($name);
		    if($t && $t->getStatus() == BackendInterface::STATUS_LOCKED){
		        throw new \RuntimeException("Task named $name is currently running");
		    }
		}
		$this->log('debug', 'Adding new task');
		$this->backend->create($service, $method, $args, $executeOn, $name);
	}
	
	public function run($limit = 5){
		$this->log('debug', 'Locking and fetching pending tasks');
		$tasks = $this->backend->startPending($limit);
		
		foreach($tasks as $task){
		    $this->task = $task;
			$this->dispatcher->dispatch('glorpen.queue.task_start', new TaskEvent($task));
			try {
				$this->log('info', sprintf('Executing task %s', $task->getName()));
				$this->backend->markStarted($task);
				$task->execute($this->container);
				$this->log('info', sprintf('Marking task %s as ok', $task->getName()));
				$this->backend->markDone($task, BackendInterface::STATUS_OK);
			} catch(\Exception $e){
				$this->log('error', sprintf('Marking task %s as failed', $task->getName()), array('exception'=>$e));
				$this->backend->markDone($task, BackendInterface::STATUS_FAILURE);
			}
			$this->dispatcher->dispatch('glorpen.queue.task_end', new TaskEvent($task));
			$this->log('info', sprintf('Task %s ended after %d seconds', $task->getName(), $task->getExecutionTime()));
		}
		
		$this->task = null;
		
		return count($tasks);
	}
	
	public function checkRunning(){
		$this->log('info', 'Checking crashed tasks');
		$count = 0;
		$locked = $this->backend->getLocked();
		foreach($locked as $t){
		    /* @var $t Task */
		    if(!$t->isAlive()){
		        $this->backend->markDone($t, BackendInterface::STATUS_FAILURE);
		        $count++;
		    }
		}
		$this->log('info', sprintf('Found %d crashed tasks', $count));
		return $count;
	}
	
	public function restartFailed(){
		$this->log('info', 'Restarting failed tasks');
		$count = $this->backend->restartFailed();
		$this->log('info', sprintf('%d failed tasks marked as pending', $count));
		return $count;
	}
	
	public function cleanup(){
		$this->log('info', 'Removing successfull tasks');
		$count = $this->backend->cleanup();
		$this->log('info', sprintf('%d tasks removed', $count));
		return $count;
	}
	
	public function setCurrentTaskProgress($percentage){
	    $this->backend->setProgress($this->task, $percentage);
	}
	
	public function addCurrentTaskLog($msg){
	    $this->backend->addLog($this->task, $msg);
	}
	
	public function getTask($id){
	    return $this->backend->findTask($id);
	}
}
