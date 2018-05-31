<?php

/*
 * This file is part of the seasx/seas-logger.
 *
 * (c) Panda <itwujunze@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Seasx\SeasLogger\Tests;

use Seasx\SeasLogger\Logger;

class LoggerTest extends TestCase
{
    /**
     * @return Logger
     */
    public function init()
    {
        $logger = new Logger();
        $logger->setBasePath('/tmp/seaslogger');

        return $logger;
    }

    public function testGetBasePath()
    {
        $logger = $this->init();

        $basePath = $logger->getBasePath();
        $this->assertEquals('/tmp/seaslogger', $basePath);
    }

    public function testSetRequestID()
    {
        $logger = $this->init();
        $result = $logger::setRequestID(1024);
        $this->assertTrue($result);
    }

    public function testGetRequestID()
    {
        $logger = $this->init();
        $result = $logger::setRequestID(1024);
        $this->assertTrue($result);
        $requestID = $logger::getRequestID();
        $this->assertEquals(1024, $requestID);
    }

    public function testEmergency()
    {
        $logger = $this->init();
        $this->assertInstanceOf(Logger::class, $logger);
        $logger->emergency('[SeasLog Test]', ['level' => 'emergency']);
    }

    public function testAlert()
    {
        $logger = $this->init();
        $this->assertInstanceOf(Logger::class, $logger);
        $logger->alert('[SeasLog Test]', ['level' => 'alert']);
    }

    public function testCritical()
    {
        $logger = $this->init();
        $this->assertInstanceOf(Logger::class, $logger);
        $logger->critical('[SeasLog Test]', ['level' => 'critical']);
    }

    public function testError()
    {
        $logger = $this->init();
        $this->assertInstanceOf(Logger::class, $logger);
        $logger->error('[SeasLog Test]', ['level' => 'error']);
    }

    public function testWarning()
    {
        $logger = $this->init();
        $this->assertInstanceOf(Logger::class, $logger);
        $logger->warning('[SeasLog Test]', ['level' => 'warning']);
    }

    public function testNotice()
    {
        $logger = $this->init();
        $this->assertInstanceOf(Logger::class, $logger);
        $logger->notice('[SeasLog Test]', ['level' => 'notice']);
    }

    public function testInfo()
    {
        $logger = $this->init();
        $this->assertInstanceOf(Logger::class, $logger);
        $logger->info('[SeasLog Test]', ['level' => 'info']);
    }

    public function testDebug()
    {
        $logger = $this->init();
        $this->assertInstanceOf(Logger::class, $logger);
        $logger->debug('[SeasLog Test]', ['level' => 'debug']);
    }

    public function testLog()
    {
        $logger = $this->init();
        $this->assertInstanceOf(Logger::class, $logger);
        $logger->log(Logger::DEBUG, '[SeasLog Test]', ['level' => 'log']);
    }

    public function testRequestLevel()
    {
        $logger = $this->init();
        $this->assertInstanceOf(Logger::class, $logger);
        $logger->setRequestLevel(Logger::ALL);
        $logger->log(Logger::DEBUG, '[SeasLog Test]', ['level' => 'DEBUG']);
        $logger->log(Logger::WARNING, '[SeasLog Test]', ['level' => 'WARNING']);
        $logger->log(Logger::ERROR, '[SeasLog Test]', ['level' => 'ERROR']);
        $logger->log(Logger::INFO, '[SeasLog Test]', ['level' => 'INFO']);
        $logger->log(Logger::CRITICAL, '[SeasLog Test]', ['level' => 'CRITICAL']);
        $logger->log(Logger::EMERGENCY, '[SeasLog Test]', ['level' => 'EMERGENCY']);
        $logger->log(Logger::NOTICE, '[SeasLog Test]', ['level' => 'NOTICE']);
        $logger->log(Logger::ALERT, '[SeasLog Test]', ['level' => 'ALERT']);

        $logger->log(0, '[SeasLog Test]', ['level' => 'default']);
        $logger->log(Logger::ALL - 1, '[SeasLog Test]', ['level' => 'default']);
    }

    public function testSetLogger()
    {
        $logger = $this->init();
        $result = $logger::setLogger('seas');
        $this->assertTrue($result);
    }

    public function testGetLastLogger()
    {
        $logger = $this->init();
        $result = $logger::setLogger('seas');
        $this->assertTrue($result);
        $model = $logger::getLastLogger();
        $this->assertEquals('seas', $model);
    }

    public function testSetDatetimeFormat()
    {
        $logger = $this->init();
        $result = $logger::setDatetimeFormat('Y-m-d H:i:s');
        $this->assertTrue($result);
    }

    public function testGetDatetimeFormat()
    {
        $logger = $this->init();
        $result = $logger::setDatetimeFormat('Y-m-d H:i:s');
        $this->assertTrue($result);
        $format = $logger::getDatetimeFormat();
        $this->assertEquals('Y-m-d H:i:s', $format);
    }

    public function testAnalyzerCount()
    {
        $logger = $this->init();
        $result = $logger::analyzerCount();
        $this->assertNotNull($result);
    }

    public function testAnalyzerDetail()
    {
        $logger = $this->init();
        $result = $logger::analyzerDetail();
        $this->assertNotNull($result);
    }

    public function testGetBuffer()
    {
        $logger = $this->init();
        $this->assertInstanceOf(Logger::class, $logger);
        $logger->info('[SeasLog Test]', ['level' => 'info']);
        $buffer = $logger::getBuffer();
        $this->assertNull($buffer);
    }

    public function testFlushBuffer()
    {
        $logger = $this->init();
        $result = $logger::flushBuffer();
        $this->assertTrue($result);
    }


    public function testInvoke()
    {
        $logger = $this->init();
        $seasLogger = $logger(['path' => '/tmp/logger']);
        $this->assertInstanceOf(Logger::class, $seasLogger);
    }
}
