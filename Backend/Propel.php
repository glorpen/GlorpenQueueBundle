<?php

namespace Glorpen\QueueBundle\Backend;

use Glorpen\QueueBundle\BackendInterface;

/**
 * @author Arkadiusz Dzięgiel
 */
class Propel implements BackendInterface {
	
    protected $omClass;
    
    public function __construct($omClass = 'Glorpen\QueueBundle\Model\Propel\Task'){
        $this->omClass = $omClass;
    }
    
    protected function getQuery(){
        $cls = $this->omClass.'Query';
        return $cls::create();
    }
    
	/**
	 * @param Task $task
	 */
	public function create($service, $method, array $args, $executeOn = 'now', $name = null) {
		
	    if($name){
	        $this->getQuery()->filterByName($name)->delete();
	    }
	    
	    $t = new $this->omClass;
		$t->setArgs($args);
		$t->setService($service);
		$t->setMethod($method);
		$t->setExecuteOn($executeOn);
		$t->setStatus(self::STATUS_PENDING);
		$t->setName($name);
		$t->save();
		
		return $t;
	}
	
	public function getLocked(){
		$ret = array();
		$ts = $this->getQuery()->filterByStatus(self::STATUS_LOCKED)->find();
		
		foreach($ts as $t){
		    $ret[] = new PropelTask($t);
		}
		return $ret;
	}
	
	public function restartFailed(){
		return $this->getQuery()
		->filterByStatus(self::STATUS_FAILURE)
		->update(array('Status'=>self::STATUS_PENDING));
	}
	
	public function startPending($limit) {
		$con = \Propel::getConnection();
		$con->beginTransaction();
		
		$now = new \DateTime();
		$tasks = $this->getQuery()
		->filterByExecuteOn($now, \Criteria::LESS_EQUAL)
		->filterByStatus(self::STATUS_PENDING)
		->orderByPriority(\Criteria::ASC)
		->orderByExecuteOn(\Criteria::ASC)
		->limit($limit)
		->find($con);
		
		$ret = array();
		foreach($tasks as $task){
			$task->setStatus(self::STATUS_LOCKED);
			$task->setStartedOn('now');
			$task->setPid(posix_getpid());
			$task->save($con);
			$ret[] = new PropelTask($task);
		}
		$con->commit();
		return $ret;
	}
	
	/**
	 * @param PropelTask $task
	 */
	public function markDone($task, $status) {
		$model = $task->getModel();
		$model->setStatus($status);
		$model->setExecutionTime($task->getExecutionTime());
		$model->save();
	}
	
	public function markStarted($task){
	    $model = $task->getModel();
	    $model->setStartedOn('now');
	    $model->save();
	}
	
	public function cleanup(){
		return $this->getQuery()
		->filterByStatus(self::STATUS_OK)
		->delete();
	}
	
	public function setProgress($task, $progress){
	    $task->setProgress($progress);
	    $task->save();
	}
	
	public function findTask($id){
	    return $this->getQuery()
	    ->filterByPrimaryKey($id)
	    ->_or()
	    ->filterByName($id)
	    ->findOne();
	}
	
}