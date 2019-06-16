<?php

use ES\Kernel\Kafka\Topics;
use ES\Kernel\QueueManager\QueueManager;
use ES\Kernel\QueueManager\ReceiverStrategy\KafkaReceiverStrategy;
use App\HandlerQueue\Kafka\LogsQueueHandler;

include_once  '../../../bootstrap-console.php';

QueueManager::create()
	->setReceiver(new KafkaReceiverStrategy())
	->setQueueHandler(Topics::LOGS, new LogsQueueHandler())
	->runHandler(Topics::LOGS);