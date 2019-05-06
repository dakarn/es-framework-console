<?php

use Kafka\Topics;
use QueueManager\QueueManager;
use QueueManager\ReceiverStrategy\KafkaReceiverStrategy;
use App\HandlerQueue\LogsQueueHandler;

include_once  '../../bootstrap-console.php';

QueueManager::create()
	->setReceiver(new KafkaReceiverStrategy())
	->setQueueHandler(Topics::LOGS, new LogsQueueHandler())
	->runHandler(Topics::LOGS);