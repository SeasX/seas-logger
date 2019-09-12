<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Targets;


use Exception;
use Seasx\SeasLogger\AbstractConfig;
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
    /** @var string */
    private $topic;
    /** @var array */
    private $customerTmp = [];
    /** @var array */
    private $fieldTemplate = [];

    /**
     * KafkaTarget constructor.
     * @param Producter $client
     * @param array $levelList
     * @param string $topic
     * @param array $customerTmp
     * @param array $fieldTemplate
     */
    public function __construct(
        Producter $client,
        array $levelList = [],
        string $topic = 'seaslog',
        array $customerTmp = [],
        array $fieldTemplate = [
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
        $this->fieldTemplate = $fieldTemplate;
        $this->customerTmp = $customerTmp;
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
                $i = 0;
                foreach ($msg as $index => $msgValue) {
                    if ($this->template[$index] === '%A') {
                        switch ($this->customerType) {
                            case AbstractConfig::TYPE_JSON:
                                $msgValue = json_decode($msgValue, true);
                                break;
                            case AbstractConfig::TYPE_FIELD:
                            default:
                                $msgValue = explode($this->split, $msgValue);
                        }
                        foreach ($this->customerTmp as $tmpIndex => [$name, $type]) {
                            $this->makeLog($log, $name, $type, isset($msgValue[$tmpIndex]) ? $msgValue[$tmpIndex] : '');
                        }
                    } else {
                        [$name, $type] = $this->fieldTemplate[$i];
                        $this->makeLog($log, $name, $type, $msgValue);
                        $i++;
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

    /**
     * @param array $log
     * @param string $name
     * @param string $type
     * @param $value
     */
    private function makeLog(array &$log, string $name, string $type, $value): void
    {
        switch ($type) {
            case "timespan":
                $log[$name] = $value ? strtotime(explode('.', $value)[0]) : 0;
                break;
            case "int":
                $log[$name] = $value ? (int)$value : 0;
                break;
            case "string":
                $log[$name] = $value ? trim($value) : '';
                break;
            default:
                $log[$type][$name] = $value;
        }
    }
}