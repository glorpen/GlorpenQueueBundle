------------------
GlorpenQueueBundle
------------------

Bundle for offloading work to server side.

For forking and other funnies:

- https://bitbucket.org/glorpen/glorpenqueuebundle
- https://github.com/glorpen/GlorpenQueueBundle

Installation
============

- add requirements to composer.json:

.. sourcecode:: json

   {
       "require": {
           "glorpen/queue-bundle": "*"
       }
   }
   

- enable the bundle in your **AppKernel** class

*app/AppKernel.php*

.. sourcecode:: php

    <?php
    
    class AppKernel extends AppKernel
    {
       public function registerBundles()
       {
           $bundles = array(
               ...
               new Glorpen\QueueBundle\GlorpenQueueBundle(),
               ...
           );
       }
    }

- choose backend in config.yml

Backends
========

Propel
------

Bundle configuration:

.. sourcecode:: yaml

   glorpen_queue:
       backend: propel


Usage
=====

To add new task:

.. sourcecode:: php

   <?php
   $queue = $container->get("glorpen.queue");
   $task = $queue->createTask();
   $task->setService('test');
   //$task->setArgs(array(3, false, array(), ...));
   $task->setMethod('method');
   $queue->addTask($task);

Then to execute use ``app/console queue:run``:

.. sourcecode::

   Starting task test:method
   Task test:method ended after 0 seconds with status "failed"

Remember that:

- dataset is fetched on task execution, so it can change after you create task.
- task arguments are serialized and stored in choosen backend

Other useful commands:

- ``queue:restart-failed`` simply marks failed tasks as pending
- ``queue:unlock`` unlocks crashed (eg. on OOM) tasks
