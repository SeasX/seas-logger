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
        /** @var \ArrayObject $context */
        $context = Co::getContext();
        $context[$name] = $value;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function get(string $name)
    {
        /** @var \ArrayObject $context */
        $context = Co::getContext();
        return isset($context[$name]) ? $context[$name] : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function has(string $name): bool
    {
        /** @var \ArrayObject $context */
        $context = Co::getContext();
        return isset($context[$name]);
    }

    /**
     * @param string $name
     */
    public static function delete(string $name): void
    {
        /** @var \ArrayObject $context */
        $context = Co::getContext();
        unset($context[$name]);
    }
}