<?php

use ES\Kernel\Kafka\Topics;
use ES\Kernel\QueueManager\QueueManager;
use ES\Kernel\QueueManager\ReceiverStrategy\KafkaReceiverStrategy;
use ES\App\HandlerQueue\Kafka\FileLoaderQueueHandler;

include_once  '../../../bootstrap-console.php';

QueueManager::create()
    ->setReceiver(new KafkaReceiverStrategy())
    ->setQueueHandler(Topics::FILE_LOADER, new FileLoaderQueueHandler())
    ->runHandler(Topics::FILE_LOADER);