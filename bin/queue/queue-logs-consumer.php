<?php

use ElasticSearch\ElasticSearch;
use ElasticSearch\ElasticQuery;

include_once  '../../vendor/autoload.php';

$conf = new RdKafka\Conf();

$conf->set('group.id', 'myConsumerGroup');

$rk = new RdKafka\Consumer($conf);
$rk->addBrokers(\Configs\Config::get('kafka')['host']);

$topicConf = new RdKafka\TopicConf();
$topicConf->set('auto.commit.interval.ms', 100);
$topicConf->set('offset.store.method', 'file');
$topicConf->set('offset.store.path', sys_get_temp_dir());
$topicConf->set('auto.offset.reset', 'smallest');

$topic = $rk->newTopic("test", $topicConf);
$topic->consumeStart(0, RD_KAFKA_OFFSET_STORED);

while (true) {
	$message = $topic->consume(0, 120*10000);

	if ($message->err === RD_KAFKA_RESP_ERR_NO_ERROR) {

		$body = json_decode($message->payload, true);

		$data[] = [
			'index' => ['_index' => 'logs', '_type' => 'errorLog']
		];
		$data[] = [
			'level'   => \ucfirst($body['level']),
			'time'    => $body['time'],
			'message' => $body['message'],
		];

		$es = ElasticSearch::create()
			->bulk()
			->setBulkArray($data);

		ElasticQuery::create()->execute($es);
	}
}