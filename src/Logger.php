<?php
declare(strict_types=1);
/*
 * This file is part of the seasx/seas-logger.
 *
 * (c) Panda <itwujunze@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Seasx\SeasLogger;

use Psr\Log\LoggerInterface;
use SeasLog;
use Seasx\SeasLogger\Exceptions\NotSupportedException;
use function extension_loaded;

/**
 * Class Logger
 * @package Seasx\SeasLogger
 */
class Logger implements LoggerInterface
{
    const CONTEXT_KEY = 'logger.default';
    /**
     * All level.
     */
    const ALL = -2147483647;
    /**
     * Detailed debug information.
     */
    const DEBUG = 100;
    /**
     * Interesting events.
     *
     * Examples: User logs in, SQL logs.
     */
    const INFO = 200;
    /**
     * Uncommon events.
     */
    const NOTICE = 250;
    /**
     * Exceptional occurrences that are not errors.
     *
     * Examples: Use of deprecated APIs, poor use of an API,
     * undesirable things that are not necessarily wrong.
     */
    const WARNING = 300;
    /**
     * Runtime errors.
     */
    const ERROR = 400;
    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     */
    const CRITICAL = 500;
    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc.
     * This should trigger the SMS alerts and wake you up.
     */
    const ALERT = 550;
    /**
     * Urgent alert.
     */
    const EMERGENCY = 600;
    /**
     * request Level limit.
     */
    public static $RequestLevel = self::ALL;
    /**
     * Logging levels from syslog protocol defined in RFC 5424.
     *
     * This is a static variable and not a constant to serve as an extension point for custom levels
     *
     * @var string[] Logging levels with the levels as key
     */
    protected static $levels = [
        self::DEBUG => 'DEBUG',
        self::INFO => 'INFO',
        self::NOTICE => 'NOTICE',
        self::WARNING => 'WARNING',
        self::ERROR => 'ERROR',
        self::CRITICAL => 'CRITICAL',
        self::ALERT => 'ALERT',
        self::EMERGENCY => 'EMERGENCY',
    ];
    /** @var AbstractConfig */
    private static $config;

    /**
     * Logger constructor.
     * @param AbstractConfig|null $config
     */
    public function __construct(?AbstractConfig $config = null)
    {
        if ($config !== null && !extension_loaded('swoole')) {
            throw new NotSupportedException("This usage must have swoole version>=4");
        }
        static::$config = $config;
    }

    /**
     * 设置本次请求标识.
     *
     * @param string
     *
     * @return bool
     */
    public static function setRequestID($request_id)
    {
        if (static::$config instanceof LoggerConfig) {
            throw new NotSupportedException("LoggerConfig not support setRequestID");
        }
        return SeasLog::setRequestID($request_id);
    }

    /**
     * 获取本次请求标识.
     *
     * @return string
     */
    public static function getRequestID()
    {
        if (static::$config instanceof LoggerConfig) {
            throw new NotSupportedException("LoggerConfig not support getRequestID");
        }
        return SeasLog::getRequestID();
    }

    /**
     * 设置模块目录.
     *
     * @param $module
     *
     * @return bool
     */
    public static function setLogger($module)
    {
        if (static::$config instanceof LoggerConfig) {
            throw new NotSupportedException("LoggerConfig not support setLogger");
        }
        return SeasLog::setLogger($module);
    }

    /**
     * 获取最后一次设置的模块目录.
     *
     * @return string
     */
    public static function getLastLogger()
    {
        if (static::$config instanceof LoggerConfig) {
            throw new NotSupportedException("LoggerConfig not support getLastLogger");
        }
        return SeasLog::getLastLogger();
    }

    /**
     * 设置DatetimeFormat配置.
     *
     * @param $format
     *
     * @return bool
     */
    public static function setDatetimeFormat($format)
    {
        if (static::$config instanceof LoggerConfig) {
            LoggerConfig::setDatetimeFormat($format);
        }
        return SeasLog::setDatetimeFormat($format);
    }

    /**
     * 返回当前DatetimeFormat配置格式.
     *
     * @return string
     */
    public static function getDatetimeFormat()
    {
        if (static::$config instanceof LoggerConfig) {
            return LoggerConfig::getDatetimeFormat();
        }
        return SeasLog::getDatetimeFormat();
    }

    /**
     * 统计所有类型（或单个类型）行数.
     *
     * @param string $level
     * @param string $log_path
     * @param null $key_word
     *
     * @return array
     */
    public static function analyzerCount($level = 'all', $log_path = '*', $key_word = null)
    {
        if (static::$config instanceof LoggerConfig) {
            throw new NotSupportedException("LoggerConfig not support analyzerCount");
        }
        return SeasLog::analyzerCount($level, $log_path, $key_word);
    }

    /**
     * 以数组形式，快速取出某类型log的各行详情.
     *
     * @param        $level
     * @param string $log_path
     * @param null $key_word
     * @param int $start
     * @param int $limit
     * @param        $order    默认为正序 SEASLOG_DETAIL_ORDER_ASC，可选倒序 SEASLOG_DETAIL_ORDER_DESC
     *
     * @return array
     */
    public static function analyzerDetail(
        $level = SEASLOG_INFO,
        $log_path = '*',
        $key_word = null,
        $start = 1,
        $limit = 20,
        $order = SEASLOG_DETAIL_ORDER_ASC
    ) {
        if (static::$config instanceof LoggerConfig) {
            throw new NotSupportedException("LoggerConfig not support analyzerDetail");
        }
        return SeasLog::analyzerDetail(
            $level,
            $log_path,
            $key_word,
            $start,
            $limit,
            $order
        );
    }

    /**
     * 获得当前日志buffer中的内容.
     *
     * @return array
     */
    public static function getBuffer()
    {
        if (static::$config instanceof LoggerConfig) {
            return LoggerConfig::getBuffer();
        }
        return SeasLog::getBuffer();
    }

    /**
     * 将buffer中的日志立刻刷到硬盘.
     *
     * @return bool
     */
    public static function flushBuffer()
    {
        if (static::$config) {
            throw new NotSupportedException("This ENV not support flushBuffer");
        }
        return SeasLog::flushBuffer();
    }

    /**
     * Manually release stream flow from logger
     *
     * @param $type
     * @param string $name
     * @return bool
     */
    public static function closeLoggerStream($type = SEASLOG_CLOSE_LOGGER_STREAM_MOD_ALL, $name = '')
    {
        if (static::$config instanceof LoggerConfig) {
            throw new NotSupportedException("LoggerConfig not support closeLoggerStream");
        }
        if (empty($name)) {
            return SeasLog::closeLoggerStream($type);
        }

        return SeasLog::closeLoggerStream($type, $name);

    }

    /**
     * @return AbstractConfig
     */
    public function getConfig(): ?AbstractConfig
    {
        return static::$config;
    }

    /**
     * set request level for seaslog.
     *
     * @param int $level
     */
    public function setRequestLevel($level = self::ALL)
    {
        self::$RequestLevel = $level;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, array $context = array())
    {
        empty(static::$config) ? SeasLog::emergency($message, $context) : $this->log(self::EMERGENCY, $message,
            $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        if ((int)$level < self::$RequestLevel) {
            return;
        }

        if (!array_key_exists($level, self::$levels)) {
            return;
        }

        if (empty(static::$config)) {
            $levelFunction = strtolower(self::$levels[$level]);
            SeasLog::$levelFunction($message, $context);
        } else {
            if (is_string($message)) {
                static::$config->log(self::$levels[$level], $message, $context);
            } elseif (is_array($message)) {
                foreach ($message as $m) {
                    static::$config->log(self::$levels[$level], $m, $context);
                }
            }
        }
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function alert($message, array $context = array())
    {
        empty(static::$config) ? SeasLog::alert($message, $context) : $this->log(self::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function critical($message, array $context = array())
    {
        empty(static::$config) ? SeasLog::critical($message, $context) : $this->log(self::CRITICAL, $message,
            $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function error($message, array $context = array())
    {
        empty(static::$config) ? SeasLog::error($message, $context) : $this->log(self::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function warning($message, array $context = array())
    {
        empty(static::$config) ? SeasLog::warning($message, $context) : $this->log(self::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function notice($message, array $context = array())
    {
        empty(static::$config) ? SeasLog::notice($message, $context) : $this->log(self::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function info($message, array $context = array())
    {
        empty(static::$config) ? SeasLog::info($message, $context) : $this->log(self::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function debug($message, array $context = array())
    {
        empty(static::$config) ? SeasLog::debug($message, $context) : $this->log(self::DEBUG, $message, $context);
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        if (static::$config instanceof LoggerConfig) {
            throw new NotSupportedException(sprintf("LoggerConfig not support %s", __METHOD__));
        }
        return SeasLog::getBasePath();
    }

    /**
     * Create a custom SeasLog instance.
     *
     * @param array $config
     *
     * @return Logger
     */
    public function __invoke(array $config)
    {
        $logger = new Logger();
        if (!empty($config['path'])) {
            $logger->setBasePath($config['path']);
        }

        return $logger;
    }

    /**
     * @param string $basePath
     *
     * @return bool
     */
    public function setBasePath(string $basePath)
    {
        if (static::$config instanceof LoggerConfig) {
            throw new NotSupportedException(sprintf("LoggerConfig not support %s", __METHOD__));
        }
        return SeasLog::setBasePath($basePath);
    }
}