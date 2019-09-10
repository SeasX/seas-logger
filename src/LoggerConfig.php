<?php
declare(strict_types=1);

namespace Seasx\SeasLogger;

use Co;
use Exception;
use Psr\Log\InvalidArgumentException;

/**
 * Class LoggerConfig
 * @package Seasx\SeasLogger
 */
class LoggerConfig extends AbstractConfig
{
    /** @var array */
    protected static $buffer = [];
    /** @var string */
    protected static $datetime_format = "Y-m-d H:i:s";
    /** @var array */
    private static $supportTemplate = [
        '%W',
        '%L',
        '%M',
        '%T',
        '%t',
        '%Q',
        '%H',
        '%P',
        '%D',
        '%R',
        '%m',
        '%I',
        '%F',
        '%U',
        '%u',
        '%C',
        '%A'
    ];
    /** @var int */
    protected $isMicroTime = 3;
    /** @var bool */
    protected $useBasename = true;

    /**
     * LoggerConfig constructor.
     * @param array $target
     * @param array $template
     * @param array $configs
     */
    public function __construct(
        array $target,
        array $configs = [],
        array $template = ['%T', '%L', '%R', '%m', '%I', '%Q', '%F', '%U', '%A', '%M']
    ) {
        foreach ($template as $tmp) {
            if (!in_array($tmp, self::$supportTemplate)) {
                throw new InvalidArgumentException("$tmp not supported!");
            }
        }
        $this->template = $template;
        parent::__construct($target, $configs);
    }

    /**
     * @return string
     */
    public static function getDatetimeFormat(): string
    {
        return static::$datetime_format;
    }

    /**
     * @param string $format
     * @return bool
     */
    public static function setDatetimeFormat(string $format): bool
    {
        if (date($format, time()) !== false) {
            static::$datetime_format = $format;
            return true;
        }
        return false;
    }

    /**
     * 获得当前日志buffer中的内容.
     *
     * @return array
     */
    public static function getBuffer(): array
    {
        return static::$buffer;
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
        $msg = [];
        $module = ArrayHelper::getValue($context, 'module', 'System');
        foreach ($this->template as $tmp) {
            switch ($tmp) {
                case '%W':
                    $msg[] = ArrayHelper::getValue($template, $tmp, -1);
                    break;
                case '%L':
                    $msg[] = $level;
                    break;
                case '%M':
                    $msg[] = str_replace($this->split, ' ', empty($context) ? $message : strtr($message, $context));
                    break;
                case '%T':
                case '%t':
                    if ($this->isMicroTime > 0) {
                        $micsec = $this->isMicroTime > 3 ? 3 : $this->isMicroTime;
                        $mtimestamp = sprintf("%.{$micsec}f", microtime(true)); // 带毫秒的时间戳
                        $timestamp = floor($mtimestamp); // 时间戳
                        $milliseconds = round(($mtimestamp - $timestamp) * 1000); // 毫秒
                    } else {
                        $timestamp = time();
                        $milliseconds = 0;
                    }
                    if ($tmp === '%T') {
                        $msg[] = date(static::$datetime_format, (int)$timestamp) . '.' . (int)$milliseconds;
                    } else {
                        $msg[] = date(static::$datetime_format, (int)$timestamp);
                    }
                    break;
                case '%Q':
                    $msg[] = ArrayHelper::getValue($template, $tmp, uniqid());
                    break;
                case '%H':
                    $msg[] = ArrayHelper::getValue($template, $tmp, $_SERVER['HOSTNAME']);
                    break;
                case '%P':
                    $msg[] = ArrayHelper::getValue($template, $tmp, getmypid());
                    break;
                case '%D':
                    $msg[] = ArrayHelper::getValue($template, $tmp, 'cli');
                    break;
                case '%R':
                    $msg[] = ArrayHelper::getValue($template, $tmp, $_SERVER['SCRIPT_NAME']);
                    break;
                case '%m':
                    $method = ArrayHelper::getValue($template, $tmp);
                    $msg[] = $method ? strtoupper($method) : $_SERVER['SHELL'];
                    break;
                case '%I':
                    $msg[] = ArrayHelper::getValue($template, $tmp, 'local');
                    break;
                case '%F':
                case '%C':
                    $trace = Co::getBackTrace(Co::getCid(), DEBUG_BACKTRACE_IGNORE_ARGS,
                        $this->recall_depth + 2);
                    if ($tmp === '%F') {
                        $trace = $trace[$this->recall_depth];
                        $msg[] = $this->useBasename ? basename($trace['file']) . ':' . $trace['line'] : $trace['file'] . ':' . $trace['line'];
                    } else {
                        $trace = $trace[$this->recall_depth + 1];
                        $msg[] = $trace['class'] . $trace['type'] . $trace['function'];
                    }
                    break;
                case '%U':
                    $msg[] = memory_get_usage();
                    break;
                case '%u':
                    $msg[] = memory_get_peak_usage();
                    break;
                case '%A':
                    $customerTemplate = ArrayHelper::getValue($context, 'template',
                            []) ?? ArrayHelper::getValue($template, $tmp,
                            []);
                    switch ($this->customerType) {
                        case AbstractConfig::TYPE_JSON:
                            $msg[] = json_encode($customerTemplate, JSON_UNESCAPED_UNICODE);
                            break;
                        case AbstractConfig::TYPE_FIELD:
                        default:
                            $msg[] = implode($this->split, $customerTemplate);
                    }
                    break;
            }
        }
        $color = ArrayHelper::getValue($template, '%c');
        $color && $msg['%c'] = $color;
        $key = $this->appName . '_' . $module;
        static::$buffer[$key][] = $msg;
        $this->flush();
    }

    /**
     * @param bool $flush
     * @throws Exception
     */
    public function flush(bool $flush = false): void
    {
        if (!empty(static::$buffer) && $flush || ($this->bufferSize !== 0 && $this->bufferSize <= count(static::$buffer))) {
            foreach ($this->targetList as $index => $target) {
                rgo(function () use ($target, $flush) {
                    $target->export(static::$buffer);
                });
            }
            array_splice(static::$buffer, 0);
        }
    }
}