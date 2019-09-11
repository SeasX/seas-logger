<?php
declare(strict_types=1);

namespace Seasx\SeasLogger;

use Seasx\SeasLogger\targets\AbstractTarget;
use Swoole\Timer;

/**
 * Interface ConfigInterface
 * @package Seasx\SeasLogger
 */
abstract class AbstractConfig
{
    const TYPE_JSON = 'json';
    const TYPE_FIELD = 'field';
    /** @var array */
    protected static $supportField = [
        self::TYPE_JSON,
        self::TYPE_FIELD
    ];
    /** @var string */
    protected $split = ' | ';
    /** @var int */
    protected $bufferSize = 1;
    /** @var AbstractTarget[] */
    protected $targetList = [];
    /** @var int */
    protected $tick = 0;
    /** @var int */
    protected $recall_depth = 0;
    /** @var callable */
    protected $userTemplate;
    /** @var string */
    protected $appName = 'Seaslog';
    /** @var array */
    protected $template;
    /** @var string */
    protected $customerType = self::TYPE_FIELD;

    /**
     * AbstractConfig constructor.
     * @param array $target
     * @param array $configs
     */
    public function __construct(array $target, array $configs = [])
    {
        foreach ($configs as $name => $value) {
            if (property_exists($this, $name) && $name !== 'targetList') {
                $this->$name = $value;
            }
        }
        $this->targetList = $target;
        foreach ($this->targetList as $target) {
            $target->setTemplate($this->template)->setCustomerFieldType($this->customerType)->setSplit($this->split);
        }
        register_shutdown_function(function () {
            $this->flush(true);
        });
        $this->tick > 0 && Timer::tick($this->tick * 1000, [$this, 'flush'], [true]);
    }

    /**
     * @return array
     */
    public function getTemplate(): array
    {
        return $this->template;
    }

    /**
     * @param bool $flush
     */
    abstract public function flush(bool $flush = false): void;

    /**
     * @return array
     */
    abstract public function getBuffer(): array;

    /**
     * @return string
     */
    abstract public function getDatetimeFormat(): string;

    /**
     * @param string $format
     * @return bool
     */
    abstract public function setDatetimeFormat(string $format): bool;
    /**
     * @return array
     */
    public static function getSupportFieldType(): array
    {
        return static::$supportField;
    }

    /**
     * @param callable $userTemplate
     */
    public function registerTemplate(callable $userTemplate): void
    {
        $this->userTemplate = $userTemplate;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    abstract public function log(string $level, string $message, array $context = []): void;

    /**
     * @return array
     */
    protected function getUserTemplate(): array
    {
        if ($this->userTemplate) {
            $template = call_user_func($this->userTemplate);
            $template = $template ?? [];
        } else {
            $template = [];
        }
        return $template;
    }
}