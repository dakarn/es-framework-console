<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 04.05.2019
 * Time: 14:45
 */

namespace App\HandlerQueue;

use App\Models\Queue\Body\LogsBody;
use Kafka\AbstractKafkaHandler;
use Kafka\Message\Payload;
use ElasticSearch\ElasticSearch;
use ElasticSearch\ElasticQuery;

class LogsQueueHandlerOld extends AbstractKafkaHandler
{
	/**
	 * @throws \Exception\FileException
	 * @throws \Exception\HttpException
	 * @throws \Exception\ObjectException
	 */
	public function execute()
	{
		/** @var Payload $payloadModel */
		$payloadModel =
			$this->getMessageDecorator()
				->setBodyEntity(LogsBody::class)
				->getPayloadEntity();

		$data[] = [
			'index' => [
				'_index' => 'logs',
				'_type'  => 'errorLog']
		];
		$data[] = [
			'level'   => $payloadModel->getBody()->getLevel(),
			'time'    => $payloadModel->getBody()->getTime(),
			'message' => $payloadModel->getBody()->getMessage(),
		];

		$es = ElasticSearch::create()
			->bulk()
			->setBulkArray($data);

		ElasticQuery::create()->execute($es);

		echo $payloadModel->getBody()->getMessage() . PHP_EOL;
	}
}