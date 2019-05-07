<?php

use Kafka\Topics;
use QueueManager\QueueManager;
use QueueManager\ReceiverStrategy\KafkaReceiverStrategy;
use App\HandlerQueue\FileLoaderQueueHandler;

include_once  '../../../bootstrap-console.php';

QueueManager::create()
    ->setReceiver(new KafkaReceiverStrategy())
    ->setQueueHandler(Topics::FILE_LOADER, new FileLoaderQueueHandler())
    ->runHandler(Topics::FILE_LOADER);