<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Kafka;

use function is_string;
use function trim;

/**
 * @internal
 */
final class RecordValidator
{
    /**
     * @param array $record
     * @param array $topicList
     * @throws InvalidRecordInSet
     */
    public function validate(array $record, array $topicList): void
    {
        if (!isset($record['topic'])) {
            throw InvalidRecordInSet::missingTopic();
        }

        if (!is_string($record['topic'])) {
            throw InvalidRecordInSet::topicIsNotString();
        }

        if (trim($record['topic']) === '') {
            throw InvalidRecordInSet::missingTopic();
        }

        if (!isset($topicList[$record['topic']])) {
            throw InvalidRecordInSet::nonExististingTopic($record['topic']);
        }

        if (!isset($record['value'])) {
            throw InvalidRecordInSet::missingValue();
        }

        if (!is_string($record['value'])) {
            throw InvalidRecordInSet::valueIsNotString();
        }

        if (trim($record['value']) === '') {
            throw InvalidRecordInSet::missingValue();
        }
    }
}
