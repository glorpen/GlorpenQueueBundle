<?php

namespace Glorpen\QueueBundle\Command;

use Glorpen\QueueBundle\Services\Queue;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Command\Command;

class RunCommand extends ContainerAwareCommand {
	
	protected function configure(){
		$this
		->setName('queue:run')
		->setDescription('Executes pending items in queue')
		->addOption('limit','l', InputArgument::OPTIONAL, 'Limit tasks to execute', 5)
		;
	}
	
	protected function execute(InputInterface $input, OutputInterface $output){
		
		$queue = $this->getContainer()->get('glorpen.queue');
		
		/*
		for($i=0;$i<10;$i++){
			$task = $queue->createTask();
			$task->setService('test');
			$task->setMethod('method');
			$queue->addTask($task);
		}
		*/
		
		$queue->run($input->getOption('limit'));
	}
}
