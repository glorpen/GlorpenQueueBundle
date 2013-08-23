<?php 

namespace Glorpen\QueueBundle\Queue;

class Task {
	public function getService();
	public function getMethod();
	public function getArgs();
	public function getWhen();
	public function getPriority();
	
	public function execute(ContainerInterface $container){
		return call_user_func_array(array($container->get($this->getService()), $this->getMethod()), $this->getArgs());
	}
}
