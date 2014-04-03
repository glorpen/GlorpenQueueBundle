<?php

namespace Glorpen\QueueBundle\Command;

use Glorpen\QueueBundle\Services\Queue;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Command\Command;

/**
 * @author Arkadiusz Dzięgiel
 */
class UpdateCommand extends ContainerAwareCommand {
	
	protected function configure(){
		$this
		->setName('queue:update')
		->setDescription('Updates task status, removes old')
		;
	}
	
	protected function execute(InputInterface $input, OutputInterface $output){
		$queue = $this->getContainer()->get('glorpen.queue');
		
		$count = $queue->cleanup();
		$output->writeln(sprintf('Removed %d tasks', $count));
		
		$count = $queue->checkRunning();
		$output->writeln(sprintf('Marked %d tasks as crashed', $count));
		
	}
}
