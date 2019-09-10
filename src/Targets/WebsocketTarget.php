<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Targets;

use Exception;
use Psr\Log\LogLevel;
use Seasx\SeasLogger\ArrayHelper;
use Seasx\SeasLogger\HtmlColor;
use Swoole\Server;

/**
 * Class WebsocketTarget
 * @package Seasx\SeasLogger\Targets
 */
class WebsocketTarget extends AbstractTarget
{
    const COLOR_RANDOM = 'random';
    const COLOR_LEVEL = 'level';
    const COLOR_DEFAULT = 'default';

    /** @var array */
    private $colorTemplate = [
        'Magenta',
        self::COLOR_LEVEL,
        self::COLOR_LEVEL,
        'DarkGray',
        'DarkGray',
        self::COLOR_RANDOM,
        self::COLOR_LEVEL,
        'DarkGray',
        self::COLOR_LEVEL,
        self::COLOR_LEVEL
    ];
    /** @var string */
    private $default = 'LightGray';
    /** @var callable */
    private $getServer;

    /**
     * @param callable $function
     */
    public function setGetServer(callable $function): void
    {
        $this->getServer = $function;
    }

    /**
     * @param array $messages
     * @throws Exception
     */
    public function export(array $messages): void
    {
        if (!is_callable($this->getServer)) {
            return;
        }
        /** @var Server $server */
        $swooleServer = call_user_func($this->getServer);
        if (!$swooleServer || !$swooleServer instanceof Server) {
            return;
        }
        foreach ($swooleServer->connections as $fd) {
            if ($swooleServer->isEstablished($fd)) {
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
                        if (!empty($this->levelList) && !in_array(strtolower($msg[$this->levelIndex]),
                                $this->levelList)) {
                            continue;
                        }
                        if (empty($ranColor)) {
                            $ranColor = $this->default;
                        } elseif (is_array($ranColor) && isset($ranColor['websocket'])) {
                            $ranColor = $ranColor['websocket'];
                        } else {
                            $ranColor = $this->default;
                        }
                        foreach ($msg as $index => $msgValue) {
                            $msg[$index] = is_string($msgValue) ? trim($msgValue) : (string)$msgValue;
                            $level = $this->getLevelColor(trim($msg[$this->levelIndex]));
                            if (isset($this->colorTemplate[$index])) {
                                $color = $this->colorTemplate[$index];
                                switch ($color) {
                                    case self::COLOR_LEVEL:
                                        $colors[] = HtmlColor::getColor($level);
                                        break;
                                    case self::COLOR_RANDOM:
                                        $colors[] = HtmlColor::getColor($ranColor);
                                        break;
                                    case self::COLOR_DEFAULT:
                                        $colors[] = $this->default;
                                        break;
                                    default:
                                        $colors[] = HtmlColor::getColor($color);
                                }
                            } else {
                                $colors[] = $level;
                            }
                        }
                        $msg = json_encode([$msg, $colors], JSON_UNESCAPED_UNICODE);
                        rgo(function () use ($swooleServer, $fd, $msg) {
                            $swooleServer->push($fd, $msg);
                        });
                    }
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
                return "Green";
            case LogLevel::DEBUG:
                return 'DarkGray';
            case LogLevel::ERROR:
                return "Red";
            case LogLevel::WARNING:
                return 'Yellow';
            default:
                return 'DarkRed';
        }
    }

}