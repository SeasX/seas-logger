<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Targets;

/**
 * Class AbstractTarget
 * @package Seasx\SeasLogger\Targets
 */
abstract class AbstractTarget
{
    /** @var string */
    protected $split = ' | ';
    /** @var array */
    protected $levelList = [];
    /** @var int */
    protected $levelIndex = 1;

    /**
     * AbstractTarget constructor.
     * @param array $levelList
     * @param string $split
     */
    public function __construct(array $levelList = [], string $split = ' | ')
    {
        $this->split = $split;
        $this->levelList = $levelList;
    }

    /**
     * @param array $messages
     */
    abstract public function export(array $messages): void;

    /**
     * @param string $str
     * @param string $find
     * @param int $n
     * @return int
     */
    protected function str_n_pos(string $str, string $find, int $n): int
    {
        $pos_val = 0;
        for ($i = 1; $i <= $n; $i++) {
            $pos = strpos($str, $find);
            $str = substr($str, $pos + 1);
            $pos_val = $pos + $pos_val + 1;
        }
        return $pos_val - 1;
    }
}