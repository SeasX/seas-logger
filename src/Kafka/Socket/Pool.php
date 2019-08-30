<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Kafka\Socket;

use Co;
use Exception;
use SplQueue;

/**
 * Class Pool
 * @package Seasx\SeasLogger\Kafka\Socket
 */
final class Pool
{
    /** @var int */
    private $currentCount = 0;
    /** @var SplQueue */
    private $queue;
    /** @var int */
    private $maxWait = 0;
    /** @var int */
    private $retry = 3;
    /** @var int */
    private $waitReconnect = 1;
    /**
     * @var SplQueue
     */
    private $waitStack;
    /** @var int */
    private $active = 3;
    /** @var string */
    private $uri = 'localhost:9092';
    /** @var null |int */
    private $timeout = null;

    /**
     * Pool constructor.
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        foreach ($configs as $name => $value) {
            if (property_exists($this, $name)) {
                $this->$name = $value;
            }
        }
        $this->queue = new SplQueue();
        $this->waitStack = new SplQueue();
    }

    /**
     * @param SocketIO $connection
     */
    public function release(SocketIO $connection)
    {
        if ($this->queue->count() < $this->active) {
            $this->queue->push($connection);
            if ($this->waitStack->count() > 0) {
                $id = $this->waitStack->shift();
                Co::resume($id);
            }
        }
    }

    /**
     * @return SocketIO
     * @throws Exception
     */
    public function getConnection(): SocketIO
    {
        if (!$this->queue->isEmpty()) {
            return $this->queue->shift();
        }

        if ($this->currentCount >= $this->active) {
            if ($this->maxWait > 0 && $this->waitStack->count() > $this->maxWait) {
                throw new Exception('Connection pool queue is full');
            }
            $this->waitStack->push(Co::getCid());
            if (Co::suspend() == false) {
                $this->waitStack->pop();
                throw new Exception('Reach max connections! Can not pending fetch!');
            }
            return $this->queue->shift();
        }

        $connection = $this->createConnection();
        $this->currentCount++;
        if ($connection->check() === false) {
            $connection->reconnect();
        }
        return $connection;
    }

    /**
     * @return SocketIO
     * @throws Exception
     */
    public function createConnection(): SocketIO
    {
        $socket = new SocketIO();
        $socket->createConnection([
            'uri' => $this->uri,
            'retry' => $this->retry,
            'sleep' => $this->waitReconnect,
            'timeout' => $this->timeout
        ]);
        return $socket;
    }
}