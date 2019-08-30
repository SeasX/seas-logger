<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Targets;

/**
 * Class EchoTarget
 * @package Seasx\SeasLogger\Targets
 */
class EchoTarget extends AbstractTarget
{
    /**
     * @param array $messages
     */
    public function export(array $messages): void
    {
        foreach ($messages as $message) {
            foreach ($message as $msg) {
                if (is_string($msg)) {
                    $msg = explode($this->split, trim($msg));
                }
                if (!empty($this->levelList) && !in_array(strtolower($msg[$this->levelIndex]), $this->levelList)) {
                    continue;
                }
                echo implode($this->split, $msg) . PHP_EOL;
            }
        }
    }
}