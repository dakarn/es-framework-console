<?php

namespace ES\App\HandlerQueue\Kafka;

use App\QueueApp\Models\Body\InvalidMessage;
use ES\Kernel\Kafka\Message\RdKafkaMessageDecoratorInterface;
use ES\Kernel\QueueManager\AbstractQueueHandler;
use ES\Kernel\QueueManager\QueueModel;
use ES\Kernel\Kafka\Topics;
use ES\Kernel\QueueManager\QueueManager;
use ES\Kernel\Kafka\Groups;
use ES\Kernel\Kafka\Message\RdKafkaMessageDecorator;
use RdKafka\ConsumerTopic;

class InvalidMessageHandler  extends AbstractQueueHandler
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
        $this->queueParam->setTopicName(Topics::INVALID_MESSAGE);
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

        $messageDecorator = new RdKafkaMessageDecorator($message);
        $messageDecorator->setBody(InvalidMessage::class);

        return $messageDecorator;
    }

	/**
	 * @param $messageDecorator RdKafkaMessageDecoratorInterface
	 * @return bool
	 */
    public function executeTask($messageDecorator): bool
    {
        if (!$messageDecorator->hasError()) {
            return false;
        }

        $invalidMessage = $messageDecorator
            ->getPayloadEntity()
            ->getBody();

        return true;
    }

    public function after()
    {

    }
}