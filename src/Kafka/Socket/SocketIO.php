<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Kafka\Socket;

use Co;
use Co\Socket;
use Exception;

/**
 * Class SocketIO
 * @package Seasx\SeasLogger\Kafka\Socket
 */
class SocketIO
{
    /** @var bool */
    private $recv = false;
    /** @var Socket */
    private $connection;
    /** @var array */
    private $config = [];

    /**
     * @param string $data
     * @param float $timeout
     * @return int
     * @throws Exception
     */
    public function send(string $data, float $timeout = -1): int
    {
        $ln = strlen($data);
        while ($data && $ln > 0) {
            $result = $this->connection->sendAll($data, $timeout);
            if (!is_int($result)) {
                $this->reconnect();
            }
            $data = substr($data, $result);
        }
        $this->recv = false;
        return $ln;
    }

    /**
     * @throws Exception
     */
    public function reconnect(): void
    {
        $this->createConnection();
    }

    /**
     * @param array $config
     * @throws Exception
     */
    public function createConnection(array $config = []): void
    {
        !empty($config) && ($this->config = $config);
        $client = new Socket(AF_INET, SOCK_STREAM, 0);
        list($host, $port) = explode(':', $this->config['uri']);
        $maxRetry = $this->config['retry'];
        $reconnectCount = 0;
        while (true) {
            $isConnect = $this->config['timeout'] ? $client->connect($host, (int)$port,
                $this->config['timeout']) : $client->connect($host, (int)$port);
            if (!$isConnect) {
                $reconnectCount++;
                if ($maxRetry > 0 && $reconnectCount >= $maxRetry) {
                    $error = sprintf('Service connect fail error=%s host=%s port=%s', socket_strerror($client->errCode),
                        $host, $port);
                    throw new Exception($error);
                }
                Co::sleep($this->config['sleep']);
            } else {
                break;
            }
        }
        $this->connection = $client;
    }

    /**
     * @param int $length
     * @param float $timeout
     * @return string
     */
    public function recv(int $length = 65535, float $timeout = -1): string
    {
        $data = $this->connection->recvAll($length, $timeout);
        return $data;
    }

    /**
     * @return bool
     */
    public function check(): bool
    {
        return $this->connection->errCode === 0;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        return $this->connection->close();
    }
}