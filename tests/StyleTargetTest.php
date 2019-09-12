<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Tests;


use Seasx\SeasLogger\AbstractConfig;
use Seasx\SeasLogger\Exceptions\NotSupportedException;
use Seasx\SeasLogger\Targets\AbstractTarget;
use Seasx\SeasLogger\Targets\StyleTarget;

class StyleTargetTest extends TestCase
{
    public function testSetCustomerFieldType()
    {
        $target = new StyleTarget();
        $this->assertInstanceOf(AbstractTarget::class, $target->setCustomerFieldType(AbstractConfig::TYPE_JSON));
    }

    public function testSetNotSupportCustomerFieldType()
    {
        $target = new StyleTarget();
        $this->expectException(NotSupportedException::class);
        $target->setCustomerFieldType('test');
    }

    public function testStringExport()
    {
        $target = new StyleTarget();
        $this->assertInstanceOf(StyleTarget::class, $target);
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
        $target = new StyleTarget();
        $this->assertInstanceOf(StyleTarget::class, $target);
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
        $target = new StyleTarget();
        $this->assertInstanceOf(StyleTarget::class, $target);
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
        $target = new StyleTarget();
        $target->setCustomerFieldType(AbstractConfig::TYPE_JSON);
        $this->assertInstanceOf(StyleTarget::class, $target);
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
        $target = new StyleTarget([
            'info'
        ]);
        $this->assertInstanceOf(StyleTarget::class, $target);
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