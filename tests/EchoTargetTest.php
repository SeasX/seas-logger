<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Tests;

use Seasx\SeasLogger\AbstractConfig;
use Seasx\SeasLogger\Targets\EchoTarget;

class EchoTargetTest extends TestCase
{
    public function testStringExport()
    {
        $target = new EchoTarget();
        $this->assertInstanceOf(EchoTarget::class, $target);
        $target->export([
            'logger' => [
                implode(' | ', [
                    '2019-08-30 09:58:01.937',
                    'WARNING',
                    'vendor/phpunit/phpunit/phpunit',
                    '/bin/bash',
                    'local',
                    '5d6882a9e38ff',
                    'Logger.php:453',
                    1380624,
                    '[SeasLog String]'
                ])
            ]
        ]);
    }

    public function testExport()
    {
        $target = new EchoTarget();
        $this->assertInstanceOf(EchoTarget::class, $target);
        $target->export([
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
                    '[SeasLog Array]'
                ]
            ]
        ]);
    }

    public function testExportWithCustomerTemplate()
    {
        $target = new EchoTarget();
        $this->assertInstanceOf(EchoTarget::class, $target);
        $target->export([
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
                    implode(' | ', ['123']),
                    '[SeasLog Array]'
                ]
            ]
        ]);
    }

    public function testExportWithCustomerTypeJson()
    {
        $target = new EchoTarget();
        $target->setCustomerFieldType(AbstractConfig::TYPE_JSON);
        $this->assertInstanceOf(EchoTarget::class, $target);
        $target->export([
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
        ]);
    }

    public function testExportWithLevel()
    {
        $target = new EchoTarget([
            'info'
        ]);
        $this->assertInstanceOf(EchoTarget::class, $target);
        $target->export([
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
                    '[Test WARNING]'
                ],
                [
                    '2019-08-30 09:58:01.937',
                    'INFO',
                    'vendor/phpunit/phpunit/phpunit',
                    '/bin/bash',
                    'local',
                    '5d6882a9e38ff',
                    'Logger.php:453',
                    1380624,
                    '[Test INFO]'
                ]
            ]
        ]);
    }
}