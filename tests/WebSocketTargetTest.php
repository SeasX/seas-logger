<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Tests;

use co;
use Seasx\SeasLogger\AbstractConfig;
use Seasx\SeasLogger\HtmlColor;
use Seasx\SeasLogger\Targets\WebsocketTarget;
use Swoole;
use Swoole\Coroutine\Http\Client;
use Swoole\WebSocket\Frame;
use Swoole\Websocket\Server;

class WebSocketTargetTest extends TestCase
{
    public function testExport()
    {
        $pm = new ProcessManager();
        $log = [
            'logger' => [
                [
                    '2019-08-30 09:58:01.937',
                    'WARNING',
                    'vendor/phpunit/phpunit/phpunit',
                    '/bin/bash',
                    'local',
                    '5d6882a9e38ff',
                    'Logger.php:453',
                    1380624,
                    '123',
                    '[SeasLog Array]'
                ]
            ]
        ];
        $target = new WebsocketTarget();
        $this->assertInstanceOf(WebsocketTarget::class, $target);

        $pm->parentFunc = function ($pid) use ($pm, $log) {
            \Co\Run(function () use ($log) {
                $cli = new Client('127.0.0.1', 9501);
                $cli->set(['timeout' => -1]);
                while (!@$cli->upgrade('/')) {
                    Co::sleep(0.1);
                }
                $frame = $cli->recv();
                $data = json_decode($frame->data, true);
                [$msg, $colors] = $data;
                $this->assertEquals(count($msg), count($colors));
                $this->assertEquals($msg[8], '123');
                $inColor = true;
                foreach ($colors as $color) {
                    if (!in_array($color, HtmlColor::getPossibleColorsRGB())) {
                        $inColor = false;
                        break;
                    }
                }
                $this->assertTrue($inColor);
            });
            $pm->kill();
        };
        $pm->childFunc = function () use ($pm, $target, $log) {
            $server = new Server('127.0.0.1', 9501, SWOOLE_BASE);
            $server->set([
                'worker_num' => 1,
                'log_file' => '/dev/null',
                'pid_file' => '/dev/shm/ws.pid'
            ]);
            $server->on('workerStart', function () use ($pm) {
                $pm->wakeup();
            });
            $server->on('message', function (Server $server, Frame $frame) {
            });
            $server->on('open',
                function (Swoole\WebSocket\Server $server, Swoole\Http\Request $request) use ($target, $log) {
                    $target->setGetServer(function () use ($server) {
                        return $server;
                    });
                    $target->export($log);
                    $server->shutdown();
                });
            $server->start();
        };
        $pm->run();
    }

    public function testExportWithJsonType()
    {
        $pm = new ProcessManager();
        $log = [
            'logger' => [
                [
                    '2019-08-30 09:58:01.937',
                    'WARNING',
                    'vendor/phpunit/phpunit/phpunit',
                    '/bin/bash',
                    'local',
                    '5d6882a9e38ff',
                    'Logger.php:453',
                    1380624,
                    json_encode(['task_id' => '123']),
                    '[SeasLog Array]'
                ]
            ]
        ];
        $target = new WebsocketTarget();
        $this->assertInstanceOf(WebsocketTarget::class, $target);

        $pm->parentFunc = function ($pid) use ($pm, $log) {
            \Co\Run(function () use ($log) {
                $cli = new Client('127.0.0.1', 9501);
                $cli->set(['timeout' => -1]);
                while (!@$cli->upgrade('/')) {
                    Co::sleep(0.1);
                }
                $frame = $cli->recv();
                $data = json_decode($frame->data, true);
                [$msg, $color] = $data;
                $this->assertEquals(count($msg), count($color));
                $this->assertEquals($msg[8], json_encode(['task_id' => '123']));
            });
            $pm->kill();
        };
        $pm->childFunc = function () use ($pm, $target, $log) {
            $server = new Server('127.0.0.1', 9501, SWOOLE_BASE);
            $server->set([
                'worker_num' => 1,
                'log_file' => '/dev/null',
                'pid_file' => '/dev/shm/ws.pid'
            ]);
            $server->on('workerStart', function () use ($pm) {
                $pm->wakeup();
            });
            $server->on('message', function (Server $server, Frame $frame) {
            });
            $server->on('open',
                function (Swoole\WebSocket\Server $server, Swoole\Http\Request $request) use ($target, $log) {
                    $target->setGetServer(function () use ($server) {
                        return $server;
                    });
                    $target->setCustomerFieldType(AbstractConfig::TYPE_JSON);
                    $target->export($log);
                    $server->shutdown();
                });
            $server->start();
        };
        $pm->run();
    }
}