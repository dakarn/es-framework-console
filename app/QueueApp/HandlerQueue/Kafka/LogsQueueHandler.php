<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.05.2019
 * Time: 23:15
 */

namespace App\HandlerQueue\Kafka;

use ES\App\QueueApp\Kafka\MessageDecorators\LogsMessageDecorator;
use ES\App\QueueApp\Models\Body\LogsBodyList;
use ElasticSearchCommands\ElasticSearchLogger;
use ES\Kernel\Exception\FileException;
use ES\Kernel\Exception\ObjectException;
use ES\Kernel\Kafka\Groups;
use ES\Kernel\Kafka\Topics;
use ES\Kernel\QueueManager\AbstractQueueHandler;
use ES\Kernel\QueueManager\QueueManager;
use ES\Kernel\QueueManager\QueueModel;
use RdKafka\ConsumerTopic;
use ES\Kernel\Kafka\Message\RdKafkaMessageDecorator;

class LogsQueueHandler extends AbstractQueueHandler
{
	/**
	 * @var ConsumerTopic
	 */
	private $consumerTopic;

	/**
	 * @return mixed|void
	 */
	public function before()
	{
		$this->queueParam = new QueueModel();
		$this->queueParam->setTopicName(Topics::LOGS);
		$this->queueParam->setGroupId(Groups::MY_CONSUMER_GROUP);

		$this->strategy = QueueManager::create()->getReceiver();

		$this->strategy
			->setParams($this->queueParam)
			->build();

		$this->consumerTopic = $this->strategy->getCreatedObject()['consumerTopic'];
	}

	/**
	 * @return LogsMessageDecorator|mixed
	 */
	public function getMessage()
	{
		$message = $this->consumerTopic->consume(0, 120*10000);

		$messageDecorator = new LogsMessageDecorator($message);
		$messageDecorator->setBodyAsList(LogsBodyList::class);

		return $messageDecorator;
	}

	/**
	 * @param RdKafkaMessageDecorator $messageDecorator
	 * @return bool
	 * @throws FileException
	 * @throws \HttpException
	 * @throws ObjectException
	 * @throws \ES\Kernel\Exception\HttpException
	 */
	public function executeTask($messageDecorator): bool
	{
		if (empty($messageDecorator->getPayloadSource())) {
		    return false;
        }

		$loggerES = new ElasticSearchLogger();
		$loggerES->saveLogs($messageDecorator->getPayloadEntity());

		return true;
	}

	public function after()
	{

	}
}