<?php


namespace Seasx\SeasLogger\Tests;


use Psr\Log\LogLevel;
use Seasx\SeasLogger\SeaslogConfig;

class SeaslogConfigTest extends TestCase
{
    public function init(int $bufferSize = 1)
    {
        $config = new SeaslogConfig([
        ], [
            'appName' => 'Seaslog',
            'bufferSize' => $bufferSize,
            'tick' => 0,
            'recall_depth' => 1,
        ]);
        return $config;
    }

    public function testSetDatetimeFormat()
    {
        $config = $this->init();
        $result = $config->setDatetimeFormat('Y-m-d H:i:s');
        $this->assertTrue($result);
    }

    public function testGetDatetimeFormat()
    {
        $config = $this->init();
        $result = $config->setDatetimeFormat('Y-m-d H:i:s');
        $this->assertTrue($result);
        $format = $config->getDatetimeFormat();
        $this->assertEquals('Y-m-d H:i:s', $format);
    }

    public function testGetBuffer()
    {
        $config = $this->init(100);
        $config->log(LogLevel::INFO, 'LoggerConfig Test');
        $buffer = $config->getBuffer();
        $this->assertNotNull($buffer);
    }

    public function testFlush()
    {
        \Co\Run(function () {
            $config = $this->init(100);
            $config->log(LogLevel::INFO, 'LoggerConfig Test');
            $config->flush(true);
            $this->assertEmpty($config->getBuffer());
        });
    }
}