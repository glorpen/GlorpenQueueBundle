<?php

namespace Glorpen\QueueBundle\Command;

use Glorpen\QueueBundle\Event\TaskEvent;

use Glorpen\QueueBundle\Services\Queue;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Command\Command;

/**
 * Executes tasks from queue.
 * @author Arkadiusz Dzięgiel
 */
class RunCommand extends ContainerAwareCommand {
	
	private $output;
	
	protected function configure(){
		$this
		->setName('queue:run')
		->setDescription('Executes pending items in queue')
		->addOption('limit','l', InputArgument::OPTIONAL, 'Limit tasks to execute', 5)
		;
	}
	
	public function handleTaskEvent(TaskEvent $event){
		if(strpos($event->getName(), 'task_start')!==false){
			$this->output->writeln(sprintf('Starting task %s', $event->getTask()->getName()));
		} else {
			$t = $event->getTask();
			$this->output->writeln(sprintf('Task %s ended after %d seconds with status "%s"',
					$t->getName(), $t->getExecutionTime(), $t->getStatus()
			));
		}
	}
	
	protected function execute(InputInterface $input, OutputInterface $output){
		
		$this->output = $output;
		
		$c = $this->getContainer();
		$queue = $c->get('glorpen.queue');
		
		$dispatcher = $c->get('event_dispatcher');
		$dispatcher->addListener('glorpen.queue.task_start', array($this, 'handleTaskEvent'));
		$dispatcher->addListener('glorpen.queue.task_end', array($this, 'handleTaskEvent'));
		
		$count = $queue->run($input->getOption('limit'));
		$output->writeln(sprintf("Executed %d tasks", $count));
	}
}
