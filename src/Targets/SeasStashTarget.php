<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Targets;

use Exception;
use Seasx\SeasLogger\ArrayHelper;
use Seasx\SeasLogger\Kafka\Socket\Pool;

/**
 * Class SeasStashTarget
 * @package Seasx\SeasLogger\Targets
 */
class SeasStashTarget extends AbstractTarget
{
    /** @var Pool */
    private $clientPool;

    /**
     * SeasStashTarget constructor.
     * @param Pool $clientPool
     * @param array $levelList
     */
    public function __construct(Pool $clientPool, array $levelList = [])
    {
        $this->clientPool = $clientPool;
        $this->levelList = $levelList;
    }

    /**
     * @param array $messages
     * @throws Exception
     */
    public function export(array $messages): void
    {
        $connection = $this->clientPool->getConnection();
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
                            $module = substr($fileName, 0, strpos($fileName, '_', -1));
                    }
                    $msg = explode($this->split, trim($msg));
                    if (($diff = count($msg) - count($this->template)) > 0) {
                        array_splice($msg, -($diff + 1), $diff, [array_slice($msg, -($diff + 1), $diff)]);
                    }
                }
                if (!empty($this->levelList) && !in_array(strtolower($msg[$this->levelIndex]), $this->levelList)) {
                    continue;
                }
                ArrayHelper::remove($msg, '%c');
                $msg = $module . '@' . str_replace(PHP_EOL, '', implode($this->split, $msg)) . PHP_EOL;
                $connection->send($msg);
            }
        }
        $this->clientPool->release($connection);
    }
}