<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Kafka;

class Broker
{
    /**
     * @var int
     */
    private $groupBrokerId;
    /**
     * @var mixed[][]
     */
    private $topics = [];
    /**
     * @var string[]
     */
    private $brokers = [];

    /**
     * Broker constructor.
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        foreach ($configs as $name => $value) {
            if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }
    }

    /**
     * @return int
     */
    public function getGroupBrokerId(): int
    {
        return $this->groupBrokerId;
    }

    /**
     * @param int $brokerId
     */
    public function setGroupBrokerId(int $brokerId): void
    {
        $this->groupBrokerId = $brokerId;
    }

    /**
     * @param array $topics
     * @param array $brokersResult
     * @return bool
     */
    public function setData(array $topics, array $brokersResult): bool
    {
        $brokers = [];

        foreach ($brokersResult as $value) {
            $brokers[$value['nodeId']] = $value['host'] . ':' . $value['port'];
        }

        $changed = false;

        if (serialize($this->brokers) !== serialize($brokers)) {
            $this->brokers = $brokers;

            $changed = true;
        }

        $newTopics = [];
        foreach ($topics as $topic) {
            if ((int)$topic['errorCode'] !== Protocol::NO_ERROR) {
                continue;
            }

            $item = [];

            foreach ($topic['partitions'] as $part) {
                $item[$part['partitionId']] = $part['leader'];
            }

            $newTopics[$topic['topicName']] = $item;
        }

        if (serialize($this->topics) !== serialize($newTopics)) {
            $this->topics = $newTopics;

            $changed = true;
        }

        return $changed;
    }

    /**
     * @return array
     */
    public function getTopics(): array
    {
        return $this->topics;
    }

    /**
     * @return string[]
     */
    public function getBrokers(): array
    {
        return $this->brokers;
    }

    public function clear(): void
    {
        $this->brokers = [];
    }
}