<?php

namespace App\QueueApp\Models\Body;

class InvalidMessage
{
    private $sourceTopicName;
    private $message;

    /**
     * @return mixed
     */
    public function getSourceTopicName()
    {
        return $this->sourceTopicName;
    }

    /**
     * @param mixed $sourceTopicName
     */
    public function setSourceTopicName($sourceTopicName): void
    {
        $this->sourceTopicName = $sourceTopicName;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }
}