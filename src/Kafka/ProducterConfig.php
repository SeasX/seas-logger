<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Kafka;

use Exception;
use Seasx\SeasLogger\Exceptions\NotSupportedException;
use function in_array;

/**
 * @method int getRequestTimeout()
 * @method int getProduceInterval()
 * @method int getTimeout()
 * @method int getRequiredAck()
 * @method bool getIsAsyn()
 * @method int getCompression()
 */
class ProducterConfig extends Config
{
    private const COMPRESSION_OPTIONS = [
        Produce::COMPRESSION_NONE,
        Produce::COMPRESSION_GZIP,
        Produce::COMPRESSION_SNAPPY,
    ];

    /**
     * @var mixed[]
     */
    protected static $defaults = [
        'requiredAck' => 1,
        'timeout' => 5000,
        'requestTimeout' => 6000,
        'compression' => Protocol::COMPRESSION_NONE,
    ];

    /**
     * @param int $requestTimeout
     * @throws Exception
     */
    public function setRequestTimeout(int $requestTimeout): void
    {
        if ($requestTimeout < 1 || $requestTimeout > 900000) {
            throw new NotSupportedException('Set request timeout value is invalid, must set it 1 .. 900000');
        }

        $this->options['requestTimeout'] = $requestTimeout;
    }

    /**
     * @param int $timeout
     * @throws Exception
     */
    public function setTimeout(int $timeout): void
    {
        if ($timeout < 1 || $timeout > 900000) {
            throw new NotSupportedException('Set timeout value is invalid, must set it 1 .. 900000');
        }

        $this->options['timeout'] = $timeout;
    }

    /**
     * @param int $requiredAck
     * @throws Exception
     */
    public function setRequiredAck(int $requiredAck): void
    {
        if ($requiredAck < -1 || $requiredAck > 1000) {
            throw new NotSupportedException('Set required ack value is invalid, must set it -1 .. 1000');
        }

        $this->options['requiredAck'] = $requiredAck;
    }

    /**
     * @param int $compression
     * @throws Exception
     */
    public function setCompression(int $compression): void
    {
        if (!in_array($compression, self::COMPRESSION_OPTIONS, true)) {
            throw new NotSupportedException('Compression must be one the Produce::COMPRESSION_* constants');
        }

        $this->options['compression'] = $compression;
    }
}
