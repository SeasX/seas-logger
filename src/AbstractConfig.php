<?php
declare(strict_types=1);

namespace Seasx\SeasLogger;

use Seasx\SeasLogger\targets\AbstractTarget;
use Seasx\SeasLogger\Targets\StyleTarget;
use Swoole\Timer;

/**
 * Interface ConfigInterface
 * @package Seasx\SeasLogger
 */
abstract class AbstractConfig
{
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

    /**
     * AbstractConfig constructor.
     * @param array $target
     * @param array $configs
     */
    public function __construct(array $target, array $configs = [])
    {
        $this->targetList = $target;
        if (empty($this->targetList)) {
            $this->targetList = [
                'echo' => new StyleTarget()
            ];
        }
        foreach ($configs as $name => $value) {
            if (property_exists($this, $name) && $name !== 'targetList') {
                $this->$name = $value;
            }
        }
        register_shutdown_function(function () {
            $this->flush(true);
        });
        $this->tick > 0 && Timer::tick($this->tick * 1000, [$this, 'flush'], [true]);
    }

    /**
     * @param bool $flush
     */
    abstract public function flush(bool $flush = false): void;

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
    protected function getTemplate(): array
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