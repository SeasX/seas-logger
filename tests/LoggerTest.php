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
        $logger->critical('[SeasLog Test]', ['level' => 'error']);
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
        $logger->setRequestLevel(Logger::ERROR);
        $logger->log(Logger::DEBUG, '[SeasLog Test]', ['level' => 'DEBUG']);
        $logger->log(Logger::WARNING, '[SeasLog Test]', ['level' => 'WARNING']);
        $logger->log(Logger::ERROR, '[SeasLog Test]', ['level' => 'ERROR']);
        $logger->log(Logger::INFO, '[SeasLog Test]', ['level' => 'INFO']);
        $logger->log(Logger::CRITICAL, '[SeasLog Test]', ['level' => 'CRITICAL']);
        $logger->log(Logger::EMERGENCY, '[SeasLog Test]', ['level' => 'EMERGENCY']);
        $logger->log(Logger::NOTICE, '[SeasLog Test]', ['level' => 'NOTICE']);
    }
}
