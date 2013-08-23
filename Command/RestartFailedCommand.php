<?php

namespace Glorpen\QueueBundle\Command;

use Glorpen\QueueBundle\Services\Queue;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Command\Command;

class RestartFailedCommand extends ContainerAwareCommand {
	
	protected function configure(){
		$this
		->setName('queue:restart-failed')
		->setDescription('Restarts failed tasks in queue')
		;
	}
	
	protected function execute(InputInterface $input, OutputInterface $output){
		$queue = $this->getContainer()->get('glorpen.queue');
		
		$count = $queue->restartFailed();
		$output->writeln(sprintf('%d failed tasks marked as pending', $count));
	}
}
