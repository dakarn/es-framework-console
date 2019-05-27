<?php

namespace ElasticSearchCommands;

use App\QueueApp\Models\Body\LogsBody;
use ElasticSearch\ElasticQuery;
use ElasticSearch\ElasticSearch;
use Kafka\Message\Payload;

class ElasticSearchLogger
{
    /**
     * @param Payload $payload
     * @throws \Exception\FileException
     * @throws \Exception\HttpException
     * @throws \Exception\ObjectException
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