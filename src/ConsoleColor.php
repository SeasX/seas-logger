<?php
declare(strict_types=1);

namespace Seasx\SeasLogger;

use InvalidArgumentException;

/**
 * Class ConsoleColor
 * @package Seasx\SeasLogger
 */
class ConsoleColor
{
    const FOREGROUND = 38,
        BACKGROUND = 48;
    const COLOR256_REGEXP = '~^(bg_)?color_([0-9]{1,3})$~';
    const RESET_STYLE = 0;
    /** @var bool */
    private $isSupported;
    /** @var bool */
    private $forceStyle = false;
    /** @var array */
    private $styles = array(
        'none' => null,
        'bold' => '1',
        'dark' => '2',
        'italic' => '3',
        'underline' => '4',
        'blink' => '5',
        'reverse' => '7',
        'concealed' => '8',
        'default' => '39',
        'black' => '30',
        'red' => '31',
        'green' => '32',
        'yellow' => '33',
        'blue' => '34',
        'magenta' => '35',
        'cyan' => '36',
        'light_gray' => '37',
        'dark_gray' => '90',
        'light_red' => '91',
        'light_green' => '92',
        'light_yellow' => '93',
        'light_blue' => '94',
        'light_magenta' => '95',
        'light_cyan' => '96',
        'white' => '97',
        'bg_default' => '49',
        'bg_black' => '40',
        'bg_red' => '41',
        'bg_green' => '42',
        'bg_yellow' => '43',
        'bg_blue' => '44',
        'bg_magenta' => '45',
        'bg_cyan' => '46',
        'bg_light_gray' => '47',
        'bg_dark_gray' => '100',
        'bg_light_red' => '101',
        'bg_light_green' => '102',
        'bg_light_yellow' => '103',
        'bg_light_blue' => '104',
        'bg_light_magenta' => '105',
        'bg_light_cyan' => '106',
        'bg_white' => '107',
    );

    /**
     * ConsoleColor constructor.
     * @param bool $forceStyle
     */
    public function __construct(bool $forceStyle = true)
    {
        $this->forceStyle = $forceStyle;
        $this->isSupported = $this->isSupported();
    }

    /**
     * @return bool
     */
    public function isSupported(): bool
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            if (function_exists('sapi_windows_vt100_support') && @sapi_windows_vt100_support(STDOUT)) {
                return true;
            } elseif (getenv('ANSICON') !== false || getenv('ConEmuANSI') === 'ON') {
                return true;
            }
            return false;
        } else {
            return function_exists('posix_isatty') && @posix_isatty(STDOUT);
        }
    }

    /**
     * @param string $style
     * @param string $text
     * @return string
     */
    public function apply(string $style, string $text): string
    {
        if (empty($style)) {
            return $text;
        }
        if (!$this->isStyleForced() && !$this->isSupported()) {
            return $text;
        }
        if ($this->isValidStyle($style)) {
            $sequences = $this->styleSequence($style);
        } else {
            throw new InvalidArgumentException($style);
        }
        if (empty($sequences)) {
            return $text;
        }
        return $this->escSequence($sequences) . $text . $this->escSequence((string)self::RESET_STYLE);
    }

    /**
     * @return bool
     */
    public function isStyleForced(): bool
    {
        return $this->forceStyle;
    }

    /**
     * @param string $style
     * @return string
     */
    private function styleSequence(string $style): ?string
    {
        if (array_key_exists($style, $this->styles)) {
            return $this->styles[$style];
        }
        if (!$this->are256ColorsSupported()) {
            return null;
        }
        preg_match(self::COLOR256_REGEXP, $style, $matches);
        $type = $matches[1] === 'bg_' ? self::BACKGROUND : self::FOREGROUND;
        $value = $matches[2];
        return "$type;5;$value";
    }

    /**
     * @return bool
     */
    public function are256ColorsSupported(): bool
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return function_exists('sapi_windows_vt100_support') && @sapi_windows_vt100_support(STDOUT);
        } else {
            return strpos(getenv('TERM'), '256color') !== false;
        }
    }

    /**
     * @param string $style
     * @return bool
     */
    private function isValidStyle(string $style): bool
    {
        return array_key_exists($style, $this->styles) || preg_match(self::COLOR256_REGEXP, $style);
    }

    /**
     * @param string|int $value
     * @return string
     */
    private function escSequence(string $value): string
    {
        return "\033[{$value}m";
    }

    /**
     * @param bool $forceStyle
     */
    public function setForceStyle(bool $forceStyle)
    {
        $this->forceStyle = (bool)$forceStyle;
    }

    /**
     * @return array
     */
    public function getPossibleStyles(): array
    {
        return array_keys($this->styles);
    }
}