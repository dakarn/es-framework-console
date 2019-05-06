<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.05.2019
 * Time: 23:15
 */

namespace App\HandlerQueue;

use App\Models\Queue\Body\LogsBodyList;
use Kafka\Groups;
use Kafka\Topics;
use QueueManager\AbstractQueueHandler;
use QueueManager\QueueManager;
use QueueManager\QueueModel;
use RdKafka\ConsumerTopic;
use Kafka\Message\RdKafkaMessageDecorator;
use App\Models\Queue\Body\LogsBody;
use ElasticSearch\ElasticQuery;
use ElasticSearch\ElasticSearch;

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
	 * @return RdKafkaMessageDecorator|mixed
	 */
	public function getMessage()
	{
		$message = $this->consumerTopic->consume(0, 120*10000);
		return new RdKafkaMessageDecorator($message);
	}

	/**
	 * @param RdKafkaMessageDecorator $messageDecorator
	 * @return bool
	 * @throws \Exception\FileException
	 * @throws \Exception\HttpException
	 * @throws \Exception\ObjectException
	 */
	public function executeTask($messageDecorator): bool
	{
		if ($messageDecorator->hasError()) {

			$logsBodyList = $messageDecorator
				->setEntityList(LogsBodyList::class)
				->getPayloadEntity()
				->getObjectList();

			$data = [];

			/** @var LogsBody $logsBody */
			foreach ($logsBodyList->getAll() as $logsBody) {
				$data[] = [
					'index' => [
						'_index' => 'logs',
						'_type'  => 'errorLog']
				];
				$data[] = [
					'level'   => $logsBody->getLevel(),
					'time'    => $logsBody->getTime(),
					'message' => $logsBody->getMessage(),
				];
			}

			$es = ElasticSearch::create()
				->bulk()
				->setBulkArray($data);

			ElasticQuery::create()->execute($es);
		}

		return true;
	}

	public function after()
	{

	}
}