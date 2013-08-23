<?php 

namespace Glorpen\QueueBundle\Queue;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Arkadiusz Dzięgiel
 */
abstract class Task {
	
	private $executionTime;
	
	abstract public function getService();
	abstract public function getMethod();
	abstract public function getArgs();
	abstract public function getWhen();
	abstract public function getPriority();
	abstract public function getStartTime();
	
	abstract public function getStatus();
	
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
		return $this->getService().':'.$this->getMethod();
	}
}
