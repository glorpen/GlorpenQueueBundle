------------------
GlorpenQueueBundle
------------------

Bundle for offloading work to server side. Can add service tasks/jobs to queue for later execution.

Queue runs can safely overlap since tasks are locked upon acquiring by runner.

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
   $queue->create('my.tasks_container', 'myMethod', array("arg1", 2));

Then to execute use ``app/console queue:run``:

.. sourcecode::

   Starting task test:method
   Task 1:test:method ended after 0 seconds with status "failed"

Remember that:

- dataset is fetched on task execution, so it can change after you create task.
- task arguments are serialized and stored in choosen backend

Other useful commands:

- ``queue:restart-failed`` simply marks failed tasks as pending
- ``queue:update`` marks crashed (eg. on OOM) tasks as failed and removes succesfull tasks

Named tasks
-----------

To add new named task:

.. sourcecode:: php

   <?php
   $queue = $container->get("glorpen.queue");
   $queue->create('my.tasks_container', 'myMethod', array("arg1", 2), 'now', 'my_named_task');

Then you can retrieve it with:

.. sourcecode:: php

   <?php
   $queue = $container->get("glorpen.queue");
   $task = $queue->getTask('my_named_task');
   echo $task->getStatus();
   echo $task->getProgress();

When creating named task:

- completed or not started task with same name will be removed
- if old task is currently running an exception will be thrown

Metadata
--------

Inside executing task you can set its *current progress*:

.. sourcecode:: php

   <?php
   $queue = $container->get("glorpen.queue");
   $queue->setCurrentTaskProgress(50);
