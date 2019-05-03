<?php

use Kafka\Kafka;
use Kafka\ConfigureConnect;
use Configs\Config;
use Kafka\Topics;
use Kafka\Groups;

include_once  '../../vendor/autoload.php';

$connectConfig = new ConfigureConnect([Config::get('kafka', 'host')], Topics::LOGS, Groups::MY_CONSUMER_GROUP);

Kafka::create()
	->setConfigureConnect($connectConfig)
	->getConsumer()
	->waitMessage();
