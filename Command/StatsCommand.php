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
class StatsCommand extends ContainerAwareCommand {
	
	protected function configure(){
		$this
		->setName('queue:stats')
		->setDescription('Shows queue stats')
		;
	}
	
	protected function execute(InputInterface $input, OutputInterface $output){
		$queue = $this->getContainer()->get('glorpen.queue');
		
		$output->writeln('Current queue stats:');
		$output->writeln('');
		
		foreach($queue->getStats() as $name=>$count){
			$output->writeln("{$name}\t=> {$count}");
		}
		
		$output->writeln('');
	}
}
