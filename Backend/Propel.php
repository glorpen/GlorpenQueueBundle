<?php

namespace Glorpen\QueueBundle\Backend;

use Glorpen\QueueBundle\Model\Propel\TaskPeer;

use Glorpen\QueueBundle\Model\Propel\TaskQuery;

use Glorpen\QueueBundle\Model\Propel\Task;

use Glorpen\QueueBundle\BackendInterface;

class Propel implements BackendInterface {
	
	/**
	 * @param Task $task
	 */
	public function add($task) {
		$task->save();
	}
	
	public function createTask(){
		$t = new Task();
		$t->setArgs(array());
		$t->setExecuteOn('now');
		$t->setStatus(self::STATUS_PENDING);
		return $t;
	}
	
	public function unlockCrashed(\DateInterval $timeDiff){
		
		$timeLimit = new \DateTime();
		$timeLimit->sub($timeDiff);
		
		return TaskQuery::create()
		->filterByQueueStartedOn($timeLimit, TaskQuery::LESS_THAN)
		->filterByStatus(TaskPeer::STATUS_LOCKED)
		->update(array(
				'Status' => TaskPeer::STATUS_PENDING,
				'QueueStartedOn' => null
		));
	}
	
	public function restartFailed(){
		return TaskQuery::create()
		->filterByStatus(TaskPeer::STATUS_FAILED)
		->update(array('Status'=>TaskPeer::STATUS_PENDING));
	}
	
	public function startPending($limit) {
		
		$con = \Propel::getConnection();
		$con->beginTransaction();
		
		$now = new \DateTime();
		$tasks = TaskQuery::create()
		->filterByExecuteOn($now, TaskQuery::LESS_EQUAL)
		->filterByStatus(TaskPeer::STATUS_PENDING)
		->orderByPriority(TaskQuery::ASC)
		->orderByExecuteOn(TaskQuery::ASC)
		->limit($limit)
		->find($con);
		
		$ret = array();
		foreach($tasks as $task){
			$task->setStatus(self::STATUS_LOCKED);
			$task->setQueueStartedOn('now');
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

}