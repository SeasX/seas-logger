<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Kafka;

use Exception;
use function sprintf;

/**
 * Class InvalidRecordInSet
 * @package Seasx\SeasLogger\Kafka
 */
final class InvalidRecordInSet extends Exception
{
    /**
     * @return InvalidRecordInSet
     */
    public static function missingTopic(): self
    {
        return new self('You have to set "topic" to your message.');
    }

    /**
     * @param string $topic
     * @return InvalidRecordInSet
     */
    public static function nonExististingTopic(string $topic): self
    {
        return new self(sprintf('Requested topic "%s" does not exist. Did you forget to create it?', $topic));
    }

    /**
     * @return InvalidRecordInSet
     */
    public static function topicIsNotString(): self
    {
        return new self('Topic must be string.');
    }

    public static function missingValue(): self
    {
        return new self('You have to set "value" to your message.');
    }

    /**
     * @return InvalidRecordInSet
     */
    public static function valueIsNotString(): self
    {
        return new self('Value must be string.');
    }
}
