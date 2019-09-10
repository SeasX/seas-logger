<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Targets;

use Psr\Log\LogLevel;
use Seasx\SeasLogger\ArrayHelper;
use Seasx\SeasLogger\ConsoleColor;

/**
 * Class StyleTarget
 * @package Seasx\SeasLogger\Targets
 */
class StyleTarget extends AbstractTarget
{
    const COLOR_RANDOM = 'random';
    const COLOR_DEFAULT = 'default';
    const COLOR_LEVEL = 'level';
    /** @var ConsoleColor */
    private $color;
    /** @var array */
    private $colorTemplate = [
        'magenta',
        self::COLOR_LEVEL,
        self::COLOR_LEVEL,
        'dark_gray',
        'dark_gray',
        self::COLOR_RANDOM,
        self::COLOR_LEVEL,
        'dark_gray',
        self::COLOR_LEVEL
    ];
    private $default = 'none';
    /** @var string */
    private $splitColor = 'cyan';

    /**
     * StyleTarget constructor.
     * @param array $levelList
     * @param ConsoleColor|null $color
     */
    public function __construct(array $levelList = [], ?ConsoleColor $color = null)
    {
        if ($color === null) {
            $color = new ConsoleColor();
        }
        $this->color = $color;
        $this->levelList = $levelList;
    }

    /**
     * @param array $messages
     */
    public function export(array $messages): void
    {
        foreach ($messages as $message) {
            foreach ($message as $msg) {
                if (is_string($msg)) {
                    switch (ini_get('seaslog.appender')) {
                        case '2':
                        case '3':
                            $msg = trim(substr($msg, $this->str_n_pos($msg, ' ', 6)));
                            break;
                    }
                    $msg = explode($this->split, trim($msg));
                    $ranColor = $this->default;
                } else {
                    $ranColor = ArrayHelper::remove($msg, '%c');
                }
                if (!empty($this->levelList) && !in_array(strtolower($msg[$this->levelIndex]), $this->levelList)) {
                    continue;
                }
                if (empty($ranColor)) {
                    $ranColor = $this->default;
                } elseif (is_array($ranColor) && isset($ranColor['console'])) {
                    $ranColor = $ranColor['console'];
                } else {
                    $ranColor = $this->default;
                }
                $context = [];
                foreach ($msg as $index => $msgValue) {
                    $level = $this->getLevelColor(trim($msg[$this->levelIndex]));
                    if (isset($this->colorTemplate[$index])) {
                        $color = $this->colorTemplate[$index];
                        $msgValue = is_string($msgValue) ? trim($msgValue) : (string)$msgValue;
                        switch ($color) {
                            case self::COLOR_LEVEL:
                                $context[] = $this->color->apply($level, $msgValue);
                                break;
                            case self::COLOR_DEFAULT:
                                $context[] = $this->color->apply($this->default, $msgValue);
                                break;
                            case self::COLOR_RANDOM:
                                $context[] = $this->color->apply($ranColor, $msgValue);
                                break;
                            default:
                                $context[] = $this->color->apply($color, $msgValue);
                        }
                    } else {
                        $context[] = $this->color->apply($level, $msgValue);
                    }
                }
                if (isset($context)) {
                    echo implode(' ' . $this->color->apply($this->splitColor, '|') . ' ', $context) . PHP_EOL;
                }
            }
        }
    }

    /**
     * @param string $level
     * @return string
     */
    private function getLevelColor(string $level): string
    {
        switch (strtolower($level)) {
            case LogLevel::INFO:
                return "green";
            case LogLevel::DEBUG:
                return 'dark_gray';
            case LogLevel::ERROR:
                return "red";
            case LogLevel::WARNING:
                return 'yellow';
            default:
                return 'light_red';
        }
    }

}