<?php

namespace Glorpen\QueueBundle\Tests\Services;

use Glorpen\QueueBundle\Services\Queue;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Glorpen\QueueBundle\BackendInterface;

class QueueTest extends \PHPUnit_Framework_TestCase {

	protected $service, $container, $backend;
	
	protected function setUp() {
		
		$this->container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
		->getMockForAbstractClass();
		
		$this->backend = $this->getMockBuilder('Glorpen\QueueBundle\BackendInterface')
		->getMockForAbstractClass();
		
		$this->dispatcher = new EventDispatcher();
		
		$this->service = new Queue($this->container, $this->backend, $this->dispatcher);
	}
	
	protected function getTask(){
		return $this->getMockBuilder('Glorpen\QueueBundle\Queue\Task')
		->getMockForAbstractClass();
	}

	public function testCreate() {
		$this->backend->expects($this->exactly(1))->method('create');
		$this->backend->expects($this->exactly(0))->method('findTask');
		
		$this->service->create('service.id', 'someMethod', array(1,2,3));
	}
	
	public function testCreateLockedNamed() {
		
		$task = $this->getTask();
		
		$task->expects($this->exactly(1))->method('getStatus')
		->will($this->returnValue(BackendInterface::STATUS_LOCKED));
		
		$this->backend
		->expects($this->exactly(1))->method('findTask')
		->will($this->returnValue($task));
	
		try {
			$this->service->create('service.id', 'someMethod', array(1,2,3), null, "name");
		} catch (\RuntimeException $e){
			$this->assertEquals('Task named name is currently running', $e->getMessage(), 'Locked named task');
		}
	}
	
	public function testCreateNamed() {
		$this->backend->expects($this->exactly(1))->method('create');
	
		$this->backend
		->expects($this->exactly(1))->method('findTask')
		->will($this->returnValue(null));
	
		$this->service->create('service.id', 'someMethod', array(1,2,3), null, "name");
	}

}
