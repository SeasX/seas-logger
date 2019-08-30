<?php
declare(strict_types=1);

if (!function_exists('rgo')) {
    /**
     * @param Closure $function
     * @param Closure|null $defer
     * @return int
     * @throws Exception
     */
    function rgo(Closure $function, ?Closure $defer = null): int
    {
        return go(function () use ($function, $defer) {
            try {
                if (is_callable($defer)) {
                    defer($defer);
                }
                $function();
            } catch (Throwable $throwable) {
                print_r($throwable->getTraceAsString());
                return 0;
            }
        });
    }
}