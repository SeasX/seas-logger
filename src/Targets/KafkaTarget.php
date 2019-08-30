<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Targets;


use Exception;
use Seasx\SeasLogger\ArrayHelper;
use Seasx\SeasLogger\Kafka\Producter;

/**
 * Class KafkaTarget
 * @package Seasx\SeasLogger\Targets
 */
class KafkaTarget extends AbstractTarget
{
    /** @var Producter */
    private $client;
    /** @var array */
    private $template = [];
    /** @var string */
    private $topic;

    /**
     * KafkaTarget constructor.
     * @param Producter $client
     * @param string $topic
     * @param array $levelList
     * @param array $template
     */
    public function __construct(
        Producter $client,
        array $levelList = [],
        string $topic = 'seaslog',
        array $template = [
            ['datetime', 'timespan'],
            ['level', 'string'],
            ['request_uri', 'string'],
            ['request_method', 'string'],
            ['clientip', 'string'],
            ['requestid', 'string'],
            ['filename', 'string'],
            ['memoryusage', 'int'],
            ['message', 'string']
        ]
    ) {
        $this->client = $client;
        $this->topic = $topic;
        $this->template = $template;
        $this->levelList = $levelList;
    }

    /**
     * @param array $messages
     * @throws Exception
     */
    public function export(array $messages): void
    {
        foreach ($messages as $module => $message) {
            foreach ($message as $msg) {
                if (is_string($msg)) {
                    switch (ini_get('seaslog.appender')) {
                        case '2':
                        case '3':
                            $msg = trim(substr($msg, $this->str_n_pos($msg, ' ', 6)));
                            break;
                        case '1':
                        default:
                            $fileName = basename($module);
                            $module = substr($fileName, 0, strrpos($fileName, '_'));
                    }
                    $msg = explode($this->split, trim($msg));
                } else {
                    ArrayHelper::remove($msg, '%c');
                }
                if (!empty($this->levelList) && !in_array(strtolower($msg[$this->levelIndex]), $this->levelList)) {
                    continue;
                }
                $log = [
                    'appname' => $module,
                ];
                foreach ($msg as $index => $value) {
                    [$name, $type] = $this->template[$index];
                    switch ($type) {
                        case "string":
                            $log[$name] = trim($value);
                            break;
                        case "timespan":
                            $log[$name] = strtotime(explode('.', $value)[0]);
                            break;
                        case "int":
                            $log[$name] = (int)$value;
                            break;
                        default:
                            $log[$name] = trim($value);
                    }
                }
                $this->client->send([
                    [
                        'topic' => $this->topic,
                        'value' => json_encode($log),
                        'key' => ''
                    ]
                ]);
            }
        }
    }
}