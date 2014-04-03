<?php 

namespace Glorpen\QueueBundle\Queue;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Glorpen\QueueBundle\BackendInterface;

/**
 * Base class for task objects.
 * @author Arkadiusz Dzięgiel
 */
abstract class Task {
	
	private $executionTime = 0;
	
	abstract public function getService();
	abstract public function getMethod();
	abstract public function getArgs();
	abstract public function getPid();
	abstract public function getStatus();
	abstract public function getId();
	
	abstract protected function getModelProgress();
	public function getProgress(){
	    return (float)$this->getModelProgress();
	}
	abstract public function getStartTime();
	
	public function getExecutionTime(){
		return $this->executionTime;
	}
	
	public function execute(ContainerInterface $container){
		$start = microtime(true);
		$exception = null;
		try {
			call_user_func_array(array($container->get($this->getService()), $this->getMethod()), $this->getArgs());
		} catch (\Exception $e){
			$exception = $e;
		};
		$this->executionTime = (int)(microtime(true) - $start);
		if ($exception) throw $exception;
	}
	
	public function getName(){
		return $this->getId().':'.$this->getService().':'.$this->getMethod();
	}
	
	public function isAlive(){
	    return posix_kill($this->getPid(), 0);
	}

}
