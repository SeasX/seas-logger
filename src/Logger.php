<?php

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

class Logger implements LoggerInterface
{
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
     * @param string $message
     * @param array  $context
     */
    public function emergency($message, array $context = [])
    {
        SeasLog::emergency($message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     */
    public function alert($message, array $context = [])
    {
        SeasLog::alert($message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     */
    public function critical($message, array $context = [])
    {
        SeasLog::critical($message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     */
    public function error($message, array $context = [])
    {
        SeasLog::error($message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     */
    public function warning($message, array $context = [])
    {
        SeasLog::warning($message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     */
    public function notice($message, array $context = [])
    {
        SeasLog::notice($message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     */
    public function info($message, array $context = [])
    {
        SeasLog::info($message, $context);
    }

    /**
     * @param string $message
     * @param array  $context
     */
    public function debug($message, array $context = [])
    {
        SeasLog::debug($message, $context);
    }

    /**
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = [])
    {
        if ((int) $level < self::$RequestLevel) {
            return;
        }

        if (!array_key_exists($level, self::$levels)) {
            return;
        }

        $levelFunction = strtolower(self::$levels[$level]);

        SeasLog::$levelFunction($message, $context);
    }

    /**
     * @param string $basePath
     *
     * @return bool
     */
    public function setBasePath(string $basePath)
    {
        return SeasLog::setBasePath($basePath);
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return SeasLog::getBasePath();
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
        return SeasLog::setRequestID($request_id);
    }

    /**
     * 获取本次请求标识.
     *
     * @return string
     */
    public static function getRequestID()
    {
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
        return SeasLog::setLogger($module);
    }

    /**
     * 获取最后一次设置的模块目录.
     *
     * @return string
     */
    public static function getLastLogger()
    {
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
        return SeasLog::setDatetimeFormat($format);
    }

    /**
     * 返回当前DatetimeFormat配置格式.
     *
     * @return string
     */
    public static function getDatetimeFormat()
    {
        return SeasLog::getDatetimeFormat();
    }

    /**
     * 统计所有类型（或单个类型）行数.
     *
     * @param string $level
     * @param string $log_path
     * @param null   $key_word
     *
     * @return array
     */
    public static function analyzerCount($level = 'all', $log_path = '*', $key_word = null)
    {
        return SeasLog::analyzerCount($level, $log_path, $key_word);
    }

    /**
     * 以数组形式，快速取出某类型log的各行详情.
     *
     * @param        $level
     * @param string $log_path
     * @param null   $key_word
     * @param int    $start
     * @param int    $limit
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
        return SeasLog::getBuffer();
    }

    /**
     * 将buffer中的日志立刻刷到硬盘.
     *
     * @return bool
     */
    public static function flushBuffer()
    {
        return SeasLog::flushBuffer();
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
}
