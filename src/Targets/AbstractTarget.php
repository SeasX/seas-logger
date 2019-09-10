<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Targets;

use Seasx\SeasLogger\AbstractConfig;
use Seasx\SeasLogger\Exceptions\NotSupportedException;

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
    /** @var string */
    protected $customerType;
    /** @var array */
    protected $template = [];

    /**
     * AbstractTarget constructor.
     * @param array $levelList
     */
    public function __construct(array $levelList = [])
    {
        $this->levelList = $levelList;
    }

    /**
     * @param string $split
     * @return AbstractTarget
     */
    public function setSplit(string $split): self
    {
        $this->split = $split;
        return $this;
    }

    /**
     * @param array $template
     * @return AbstractTarget
     */
    public function setTemplate(array $template): self
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @param string $type
     * @return AbstractTarget
     */
    public function setCustomerFieldType(string $type): self
    {
        if (!in_array($type, AbstractConfig::getSupportFieldType())) {
            throw new NotSupportedException("The field type not support $type");
        }
        if ($this->customerType === null) {
            $this->customerType = $type;
        }

        return $this;
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