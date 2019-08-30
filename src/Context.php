<?php
declare(strict_types=1);

namespace Seasx\SeasLogger;


use Co;

/**
 * Class Context
 * @package Seasx\SeasLogger
 */
class Context
{
    /**
     * @param string $name
     * @param $value
     */
    public static function set(string $name, $value): void
    {
        Co::getContext()[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function get(string $name)
    {
        return isset(Co::getContext()[$name]) ? Co::getContext()[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function has(string $name): bool
    {
        return isset(Co::getContext()[$name]);
    }

    /**
     * @param string $name
     */
    public static function delete(string $name): void
    {
        unset(Co::getContext()[$name]);
    }
}