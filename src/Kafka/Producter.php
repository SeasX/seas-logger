<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Kafka;

use Co;
use Exception;
use Seasx\SeasLogger\Kafka\Socket\Pool;

/**
 * Class Producter
 * @package Seasx\SeasLogger\Kafka
 */
class Producter
{
    /** @var array */
    private $msgBuffer = [];
    /** @var ProducterConfig */
    private $config;
    /** @var Broker */
    private $broker;
    /** @var Pool */
    private $pool;
    /** @var RecordValidator */
    private $recordValidator;

    /**
     * Producter constructor.
     * @param ProducterConfig $config
     * @param Broker $broker
     * @param Pool $pool
     */
    public function __construct(ProducterConfig $config, Broker $broker, Pool $pool)
    {
        $this->config = $config;
        $this->broker = $broker;
        $this->pool = $pool;
        $this->recordValidator = new RecordValidator();
        ProtocolTool::init($config->getBrokerVersion());
    }

    /**
     * @param array $recordSet
     * @param callable|null $callback
     * @throws Exception
     */
    public function send(array $recordSet, ?callable $callback = null): void
    {
        static $isInit = false;
        if (!$isInit) {
            $isInit = true;
            $this->syncMeta();
        }
        $requiredAck = $this->config->getRequiredAck();
        $timeout = $this->config->getTimeout();
        $compression = $this->config->getCompression();
        if (empty($recordSet)) {
            return;
        }

        $recordSet = array_merge($recordSet, array_splice($this->msgBuffer, 0));
        $sendData = $this->convertRecordSet($recordSet);
        foreach ($sendData as $brokerId => $topicList) {
            $connect = $this->pool->getConnection();
            $params = [
                'required_ack' => $requiredAck,
                'timeout' => $timeout,
                'data' => $topicList,
                'compression' => $compression,
            ];

            $requestData = ProtocolTool::encode(ProtocolTool::PRODUCE_REQUEST, $params);
            rgo(function () use ($connect, $requestData, $requiredAck, $callback) {
                if ($requiredAck !== 0) {
                    $connect->send($requestData);
                    $dataLen = Protocol::unpack(Protocol::BIT_B32, $connect->recv(4));
                    $recordSet = $connect->recv($dataLen);
                    $this->pool->release($connect);
                    $correlationId = Protocol::unpack(Protocol::BIT_B32, substr($recordSet, 0, 4));
                    $callback && $callback(ProtocolTool::decode(ProtocolTool::PRODUCE_REQUEST,
                        substr($recordSet, 4)));
                } else {
                    $connect->send($requestData);
                    $this->pool->release($connect);
                }
            });
        }
    }

    /**
     * @throws Exception
     */
    public function syncMeta(): void
    {
        $socket = $this->pool->getConnection();
        rgo(function () use ($socket) {
            while (true) {
                try {
                    $params = [];
                    $requestData = ProtocolTool::encode(ProtocolTool::METADATA_REQUEST, $params);
                    $socket->send($requestData);
                    $dataLen = Protocol::unpack(Protocol::BIT_B32, $socket->recv(4));
                    $data = $socket->recv($dataLen);
                    $correlationId = Protocol::unpack(Protocol::BIT_B32, substr($data, 0, 4));
                    $result = ProtocolTool::decode(ProtocolTool::METADATA_REQUEST, substr($data, 4));
                    if (!isset($result['brokers'], $result['topics'])) {
                        throw new Exception('Get metadata is fail, brokers or topics is null.');
                    }
                    $this->broker->setData($result['topics'], $result['brokers']);
                } finally {
                    Co::sleep(30);
                }
            }
        });
    }

    /**
     * @param string[][] $recordSet
     *
     * @return mixed[]
     * @throws InvalidRecordInSet
     */
    protected function convertRecordSet(array $recordSet): array
    {
        $sendData = [];
        while (empty($topics = $this->broker->getTopics())) {
            Co::sleep(0.5);
        }

        foreach ($recordSet as $record) {

            $this->recordValidator->validate($record, $topics);

            $topicMeta = $topics[$record['topic']];
            $partNums = array_keys($topicMeta);
            shuffle($partNums);

            $partId = isset($record['partId'], $topicMeta[$record['partId']]) ? $record['partId'] : $partNums[0];

            $brokerId = $topicMeta[$partId];
            $topicData = [];
            if (isset($sendData[$brokerId][$record['topic']])) {
                $topicData = $sendData[$brokerId][$record['topic']];
            }

            $partition = [];
            if (isset($topicData['partitions'][$partId])) {
                $partition = $topicData['partitions'][$partId];
            }

            $partition['partition_id'] = $partId;

            if (trim($record['key'] ?? '') !== '') {
                $partition['messages'][] = ['value' => $record['value'], 'key' => $record['key']];
            } else {
                $partition['messages'][] = $record['value'];
            }

            $topicData['partitions'][$partId] = $partition;
            $topicData['topic_name'] = $record['topic'];
            $sendData[$brokerId][$record['topic']] = $topicData;
        }

        return $sendData;
    }
}
