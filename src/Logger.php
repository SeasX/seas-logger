<?php

namespace SeasX\SeasLogger;


use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function emergency($message, array $context = array())
    {
        // TODO: Implement emergency() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function alert($message, array $context = array())
    {
        // TODO: Implement alert() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function critical($message, array $context = array())
    {
        // TODO: Implement critical() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function error($message, array $context = array())
    {
        // TODO: Implement error() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function warning($message, array $context = array())
    {
        // TODO: Implement warning() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function notice($message, array $context = array())
    {
        // TODO: Implement notice() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function info($message, array $context = array())
    {
        // TODO: Implement info() method.
    }

    /**
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function debug($message, array $context = array())
    {
        // TODO: Implement debug() method.
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return mixed
     */
    public function log($level, $message, array $context = array())
    {
        // TODO: Implement log() method.
    }


}