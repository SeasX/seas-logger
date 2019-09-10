<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Tests;

use Seasx\SeasLogger\AbstractConfig;
use Seasx\SeasLogger\Kafka\Socket\Pool;
use Seasx\SeasLogger\Targets\SeasStashTarget;
use Swoole;
use Swoole\Server;

class SeasStashTargetTest extends TestCase
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
                    implode(' | ', ['123', '456']),
                    '[SeasLog Array]'
                ]
            ]
        ];
        $target = new SeasStashTarget(new Pool([
            'uri' => '127.0.0.1:9501'
        ]));
        $this->assertInstanceOf(SeasStashTarget::class, $target);

        $pm->parentFunc = function () use ($log, $pm, $target) {
            $str = $pm->getChildOutput();
            $src = [];
            foreach (current($log['logger']) as $item) {
                if (is_array($item)) {
                    $item = implode(' | ', $item);
                }
                $src[] = $item;
            }
            $this->assertEquals(trim($str), trim('logger@' . implode(' | ', $src)));
            $pm->kill();
        };
        $pm->childFunc = function () use ($target, $pm, $log) {
            $server = new Server('127.0.0.1', 9501, SWOOLE_BASE);
            $server->set([
                'worker_num' => 1,
                'log_file' => '/dev/null',
                'open_eof_check' => true,
                'package_eof' => PHP_EOL,
                'pid_file' => '/dev/shm/tcp.pid'
            ]);
            $server->on('workerstart', function (Server $server, int $worker_id) use ($target, $pm, $log) {
                $pm->wakeup();
                $target->export($log);
            });
            $server->on('receive',
                function (Swoole\Server $server, int $fd, int $reactor_id, string $data) use ($pm) {
                    $pm->setChildOutput($data);
                    $server->shutdown();
                });
            $server->start();
        };
        $pm->run(true);
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
                    json_encode(['task_id' => '123', 'id' => 'abc']),
                    '[SeasLog Array]'
                ]
            ]
        ];
        $target = new SeasStashTarget(new Pool([
            'uri' => '127.0.0.1:9501'
        ]));
        $target->setCustomerFieldType(AbstractConfig::TYPE_JSON);
        $this->assertInstanceOf(SeasStashTarget::class, $target);

        $pm->parentFunc = function () use ($log, $pm, $target) {
            $str = $pm->getChildOutput();
            $src = [];
            foreach (current($log['logger']) as $item) {
                if (is_array($item)) {
                    $item = json_encode($item);
                }
                $src[] = $item;
            }
            $this->assertEquals(trim($str), trim('logger@' . implode(' | ', $src)));
            $pm->kill();
        };
        $pm->childFunc = function () use ($target, $pm, $log) {
            $server = new Server('127.0.0.1', 9501, SWOOLE_BASE);
            $server->set([
                'worker_num' => 1,
                'log_file' => '/dev/null',
                'open_eof_check' => true,
                'package_eof' => PHP_EOL,
                'pid_file' => '/dev/shm/tcp.pid'
            ]);
            $server->on('workerstart', function (Server $server, int $worker_id) use ($target, $pm, $log) {
                $pm->wakeup();
                $target->export($log);
            });
            $server->on('receive',
                function (Swoole\Server $server, int $fd, int $reactor_id, string $data) use ($pm) {
                    $pm->setChildOutput($data);
                    $server->shutdown();
                });
            $server->start();
        };
        $pm->run(true);
    }
}