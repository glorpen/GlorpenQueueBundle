<?php

namespace Glorpen\QueueBundle\Command;

use Glorpen\QueueBundle\Services\Queue;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Command\Command;

class UnlockCommand extends ContainerAwareCommand {
	
	protected function configure(){
		$this
		->setName('queue:unlock')
		->setDescription('Unlocks crashed tasks in queue')
		->addOption('timediff','t', InputArgument::OPTIONAL, 'How old tasks should be considered', '1d 12h')
		;
	}
	
	protected function execute(InputInterface $input, OutputInterface $output){
		$queue = $this->getContainer()->get('glorpen.queue');
		
		$count = $queue->unlockCrashed(\DateInterval::createFromDateString($input->getOption('timediff')));
		$output->writeln(sprintf('Unlocked %d tasks', $count));
	}
}
