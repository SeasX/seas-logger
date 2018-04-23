<?php

namespace Seasx\SeasLogger;


use Psr\Log\LoggerInterface;
use \SeasLog;

class Logger implements LoggerInterface
{
    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = array())
    {
        SeasLog::emergency($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = array())
    {
        SeasLog::alert($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = array())
    {
        SeasLog::critical($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array())
    {
        SeasLog::error($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array())
    {
        SeasLog::warning($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = array())
    {
        SeasLog::notice($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = array())
    {
        SeasLog::info($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = array())
    {
        SeasLog::debug($message, $context);
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        SeasLog::log($level, $message, $context);
    }


}