<?php
declare(strict_types=1);

namespace Seasx\SeasLogger;

use Exception;
use Seaslog;

/**
 * Class SeaslogConfig
 * @package Seasx\SeasLogger
 */
class SeaslogConfig extends AbstractConfig
{
    /**
     * SeaslogConfig constructor.
     * @param array $target
     * @param array $configs
     */
    public function __construct(array $target, array $configs = [])
    {
        parent::__construct($target, $configs);
        ini_set('seaslog.recall_depth', (string)$this->recall_depth);
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     * @throws Exception
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $template = $this->getTemplate();
        $module = ArrayHelper::remove($context, 'module', 'System');
        if ($module !== null) {
            Seaslog::setLogger($this->appName . '_' . $module);
        }
        isset($template['%Q']) && Seaslog::setRequestID($template['%Q']);
        foreach (array_filter([
            SEASLOG_REQUEST_VARIABLE_DOMAIN_PORT => isset($template['%D']) ? $template['%D'] : null,
            SEASLOG_REQUEST_VARIABLE_REQUEST_URI => isset($template['%R']) ? $template['%R'] : null,
            SEASLOG_REQUEST_VARIABLE_REQUEST_METHOD => isset($template['%m']) ? $template['%m'] : null,
            SEASLOG_REQUEST_VARIABLE_CLIENT_IP => isset($template['%I']) ? $template['%I'] : null
        ]) as $key => $value) {
            Seaslog::setRequestVariable($key, $value);
        }
        Seaslog::$level($message);
        $this->flush();
    }

    /**
     * @param bool $flush
     * @throws Exception
     */
    public function flush(bool $flush = false): void
    {
        $total = Seaslog::getBufferCount();
        if ($flush || $total >= $this->bufferSize) {
            $buffer = Seaslog::getBuffer();
            Seaslog::flushBuffer(0);
            foreach ($this->targetList as $index => $target) {
                rgo(function () use ($target, $buffer, $flush) {
                    $target->export($buffer);
                });
            }
            unset($buffer);
        }
    }
}