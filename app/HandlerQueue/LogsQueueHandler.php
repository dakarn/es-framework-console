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
use Kafka\RdKafkaMessageDecorator;
use Kafka\Message\Payload;
use App\Models\Queue\Body\LogsBody;
use ElasticSearch\ElasticQuery;
use ElasticSearch\ElasticSearch;

class LogsQueueHandler extends AbstractQueueHandler
{
	/**
	 * @var ConsumerTopic
	 */
	private $consumerTopic;

	public function prepare()
	{
		$this->queueParam = new QueueModel();
		$this->queueParam->setTopicName(Topics::LOGS);
		$this->queueParam->setGroupId(Groups::MY_CONSUMER_GROUP);
	}

	/**
	 * @return mixed|void
	 * @throws \Exception\FileException
	 */
	public function before()
	{
		$this->strategy = QueueManager::create()->getReceiver();

		$this->strategy
			->setParams($this->queueParam)
			->build();

		$this->consumerTopic = $this->strategy->getCreationObject()['consumerTopic'];
	}

	/**
	 * @return bool
	 * @throws \Exception\FileException
	 * @throws \Exception\HttpException
	 * @throws \Exception\ObjectException
	 */
	public function run(): bool
	{
		while (true) {

			$message = $this->consumerTopic->consume(0, 120*10000);
			$messageDecorator = new RdKafkaMessageDecorator($message);

			if ($messageDecorator->hasError()) {

				$data         = [];
				$payloadModel = $messageDecorator
						->setBodyEntity(LogsBodyList::class)
						->getPayloadEntity();

				/** @var LogsBody $logsBody */
				foreach ($payloadModel->getBody()->getAll() as $logsBody) {
					$data[] = [
						'index' => [
							'_index' => 'logs',
							'_type' => 'errorLog']
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
		}

		return true;
	}

	public function after()
	{

	}
}