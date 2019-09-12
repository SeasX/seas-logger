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
        $cid = go(function () use ($function, $defer): int {
            try {
                if (is_callable($defer)) {
                    defer($defer);
                }
                $function();
                return 1;
            } catch (Throwable $throwable) {
                print_r($throwable->getTraceAsString());
                return 0;
            }
        });
        if (is_int($cid)) {
            return $cid;
        }
        return 0;
    }
}