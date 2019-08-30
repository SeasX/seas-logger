<?php


namespace Seasx\SeasLogger\Tests;


use Seasx\SeasLogger\Targets\StyleTarget;

class StyleTargetTest extends TestCase
{
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
                    'UNKNOW',
                    '127.0.0.1',
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
                    'UNKNOW',
                    '127.0.0.1',
                    '5d6882a9e38ff',
                    'Logger.php:453',
                    1380624,
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
                    'UNKNOW',
                    '127.0.0.1',
                    '5d6882a9e38ff',
                    'Logger.php:453',
                    1380624,
                    '[Test WARNING]'
                ],
                [
                    '2019-08-30 09:58:01.937',
                    'INFO',
                    'vendor/phpunit/phpunit/phpunit',
                    'UNKNOW',
                    '127.0.0.1',
                    '5d6882a9e38ff',
                    'Logger.php:453',
                    1380624,
                    '[Test INFO]'
                ]
            ]
        ]);
    }
}