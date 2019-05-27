<?php

namespace ElasticSearchCommands;

use ES\App\QueueApp\Models\Body\LogsBody;
use ES\Kernel\ElasticSearch\ElasticQuery;
use ES\Kernel\ElasticSearch\ElasticSearch;
use ES\Kernel\Kafka\Message\Payload;
use ES\Kernel\Exception\FileException;
use ES\Kernel\Exception\HttpException;
use ES\Kernel\Exception\ObjectException;

class ElasticSearchLogger
{
    /**
     * @param Payload $payload
     * @throws FileException
     * @throws HttpException
     * @throws ObjectException
     */
    public function saveLogs(Payload $payload)
    {
        $data = [];

        /** @var LogsBody $logsBody */
        foreach ($payload->getBodyAsList()->getAll() as $logsBody) {
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
}