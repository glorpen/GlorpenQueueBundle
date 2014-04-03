<?php

/**
 * This file is part of the GlorpenPropelBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license GPLv3
 */

namespace Glorpen\QueueBundle\Tests;

use Symfony\Component\Config\Loader\LoaderInterface;

use Symfony\Component\HttpKernel\Kernel;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpKernel\HttpKernelInterface;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Arkadiusz Dzięgiel
 */
class SymfonyServicesTest extends WebTestCase {
	
	public function setUp() {
		
	}

	public function testSomething(){
		$kernel = $this->createKernel();
		$kernel->boot();
		$c = $kernel->getContainer();
		
		$c->get('glorpen.queue');
	}
	
	protected static function getKernelClass()
	{
		return 'Glorpen\QueueBundle\Tests\TestKernel';
	}
	
}

class TestBundle extends Bundle {
    public function build(ContainerBuilder $container)
    {
    	$container->setDefinition('logger', new Definition('Symfony\Component\HttpKernel\Tests\Logger'));
    }
}

class TestKernel extends Kernel
{
	public function registerBundles()
	{
		$bundles = array(
				new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
				new TestBundle(),
				new \Glorpen\QueueBundle\GlorpenQueueBundle(),
		);
	
		return $bundles;
	}
	
	public function registerContainerConfiguration(LoaderInterface $loader)
	{
		$loader->load(__DIR__.'/Resources/config/config_'.$this->getEnvironment().'.yml');
	}
	
}
