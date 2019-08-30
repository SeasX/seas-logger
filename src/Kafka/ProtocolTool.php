<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Kafka;

use Exception;
use Seasx\SeasLogger\Exceptions\NotSupportedException;

class ProtocolTool
{
    /**
     * protocol request code
     */
    public const PRODUCE_REQUEST = 0;
    public const METADATA_REQUEST = 3;

    /**
     * @var Protocol[]
     */
    protected static $objects = [];

    /**
     * @param string $version
     */
    public static function init(string $version): void
    {
        $class = [
            Protocol::PRODUCE_REQUEST => Produce::class,
            Protocol::METADATA_REQUEST => Metadata::class,
        ];

        foreach ($class as $key => $className) {
            self::$objects[$key] = new $className($version);
        }
    }

    /**
     * @param int $key
     * @param array $payloads
     * @return string
     * @throws Exception
     */
    public static function encode(int $key, array $payloads): string
    {
        if (!isset(self::$objects[$key])) {
            throw new NotSupportedException('Not support api key, key:' . $key);
        }

        return self::$objects[$key]->encode($payloads);
    }

    /**
     * @param int $key
     * @param string $data
     * @return array
     * @throws Exception
     */
    public static function decode(int $key, string $data): array
    {
        if (!isset(self::$objects[$key])) {
            throw new NotSupportedException('Not support api key, key:' . $key);
        }

        return self::$objects[$key]->decode($data);
    }
}
