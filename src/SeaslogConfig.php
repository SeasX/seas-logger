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
        $this->template = explode($this->split, ini_get('seaslog.default_template'));
        ini_set('seaslog.recall_depth', (string)$this->recall_depth);
        parent::__construct($target, $configs);
    }

    /**
     * @return array
     */
    public function getBuffer(): array
    {
        $buffer = Seaslog::getBuffer();
        return $buffer !== false ? $buffer : [];
    }

    public function getDatetimeFormat(): string
    {
        return SeasLog::getDatetimeFormat();
    }

    public function setDatetimeFormat(string $format): bool
    {
        return SeasLog::setDatetimeFormat($format);
    }


    /**
     * @param string $level
     * @param string $message
     * @param array $context
     * @throws Exception
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $template = $this->getUserTemplate();
        $module = ArrayHelper::remove($context, 'module');
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
        if (!empty($template = ArrayHelper::remove($context, 'template', []) ?? ArrayHelper::remove($template, '%A',
                []))) {
            switch ($this->customerType) {
                case AbstractConfig::TYPE_JSON:
                    $template = json_encode($template, JSON_UNESCAPED_UNICODE);
                    break;
                case AbstractConfig::TYPE_FIELD:
                    $template = implode($this->split, $template);
            }
            $message = $template . $this->split . $message;
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
        if (method_exists('Seaslog', 'getBufferCount')) {
            if (($flush || ($total = Seaslog::getBufferCount()) >= $this->bufferSize) && ($buffer = Seaslog::getBuffer()) !== false) {
                Seaslog::flushBuffer(0);
                foreach ($this->targetList as $index => $target) {
                    rgo(function () use ($target, $buffer, $flush) {
                        $target->export($buffer);
                    });
                }
                unset($buffer);
            }
        } elseif ($flush) {
            Seaslog::flushBuffer();
        }
    }
}