<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Tests;

use Seasx\SeasLogger\Exceptions\NotSupportedException;
use Seasx\SeasLogger\Logger;
use Seasx\SeasLogger\LoggerConfig;
use Seasx\SeasLogger\SeaslogConfig;
use Seasx\SeasLogger\Targets\StyleTarget;

class SwooleLoggerTest extends TestCase
{
    /**
     * @param int $type
     * @return Logger
     */
    public function init(int $type = 0)
    {
        $class = $type === 0 ? LoggerConfig::class : SeaslogConfig::class;
        $logger = new Logger(
            new $class([
                'echo' => new StyleTarget()
            ], [
                'appName' => 'Seaslog',
                'bufferSize' => 1,
                'tick' => 0,
                'recall_depth' => 2,
            ]));
        return $logger;
    }

    public function testGetBasePath()
    {
        $logger = $this->init();
        $this->expectException(NotSupportedException::class);
        $logger->getBasePath();

//        $logger = $this->init(1);
//        $logger->setBasePath('/tmp/seaslogger');
//        $basePath = $logger->getBasePath();
//        $this->assertEquals('/tmp/seaslogger', $basePath);
    }

    public function testSetRequestID()
    {
        $logger = $this->init();
        $this->expectException(NotSupportedException::class);
        $logger::setRequestID(1024);

//        $logger = $this->init(1);
//        $result = $logger::setRequestID(1024);
//        $this->assertTrue($result);
    }

    public function testGetRequestID()
    {
        $logger = $this->init();
        $this->expectException(NotSupportedException::class);
        $logger::getRequestID();

//        $logger = $this->init(1);
//        $result = $logger::setRequestID(1024);
//        $this->assertTrue($result);
//        $requestID = $logger::getRequestID();
//        $this->assertEquals(1024, $requestID);
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
        $this->expectException(NotSupportedException::class);
        $logger::setLogger('seas');

//        $logger = $this->init(1);
//        $result = $logger::setLogger('seas');
//        $this->assertTrue($result);
    }

    public function testGetLastLogger()
    {
        $logger = $this->init();
        $this->expectException(NotSupportedException::class);
        $logger::getLastLogger();

//        $logger = $this->init(1);
//        $result = $logger::setLogger('seas');
//        $this->assertTrue($result);
//        $model = $logger::getLastLogger();
//        $this->assertEquals('seas', $model);
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
        $this->expectException(NotSupportedException::class);
        $logger::analyzerCount();

//        $logger = $this->init(1);
//        $result = $logger::analyzerCount();
//        $this->assertNotNull($result);
    }

    public function testAnalyzerDetail()
    {
        $logger = $this->init();
        $this->expectException(NotSupportedException::class);
        $logger::analyzerDetail();

//        $logger = $this->init(1);
//        $result = $logger::analyzerDetail();
//        $this->assertNotNull($result);
    }

    public function testGetBuffer()
    {
        $logger = $this->init();
        $this->assertInstanceOf(Logger::class, $logger);
        $logger->info('[SeasLog Test]', ['level' => 'info']);
        $buffer = $logger::getBuffer();
        $this->assertNotNull($buffer);

//        $logger = $this->init(1);
//        $this->assertInstanceOf(Logger::class, $logger);
//        $logger->info('[SeasLog Test]', ['level' => 'info']);
//        $buffer = $logger::getBuffer();
//        $this->assertNotNull($buffer);
    }

    public function testFlushBuffer()
    {
        $logger = $this->init();
        $this->expectException(NotSupportedException::class);
        $logger::flushBuffer();

//        $logger = $this->init(1);
//        $this->expectException(NotSupportedException::class);
//        $logger::flushBuffer();
    }


    public function testInvoke()
    {
        $logger = $this->init();
        $seasLogger = $logger(['path' => '/tmp/logger']);
        $this->assertInstanceOf(Logger::class, $seasLogger);

//        $logger = $this->init(1);
//        $seasLogger = $logger(['path' => '/tmp/logger']);
//        $this->assertInstanceOf(Logger::class, $seasLogger);
    }

//    public function testCloseLoggerStreamAll()
//    {
//        $logger = $this->init(1);
//        $logger->setBasePath('/tmp/allLogger');
//        $logger->log(Logger::DEBUG, '[SeasLog Test]', ['level' => 'DEBUG']);
//        $logger->log(Logger::WARNING, '[SeasLog Test]', ['level' => 'WARNING']);
//        $logger->log(Logger::ERROR, '[SeasLog Test]', ['level' => 'ERROR']);
//        $logger->log(Logger::INFO, '[SeasLog Test]', ['level' => 'INFO']);
//        $logger->log(Logger::CRITICAL, '[SeasLog Test]', ['level' => 'CRITICAL']);
//        $logger->log(Logger::EMERGENCY, '[SeasLog Test]', ['level' => 'EMERGENCY']);
//        $logger->log(Logger::NOTICE, '[SeasLog Test]', ['level' => 'NOTICE']);
//        $logger->log(Logger::ALERT, '[SeasLog Test]', ['level' => 'ALERT']);
//
//        $logger->log(0, '[SeasLog Test]', ['level' => 'default']);
//        $logger->log(Logger::ALL - 1, '[SeasLog Test]', ['level' => 'default']);
//
//        $this->assertTrue($logger::closeLoggerStream(SEASLOG_CLOSE_LOGGER_STREAM_MOD_ALL));
//    }

    public function testCloseLoggerStream()
    {
        $logger = $this->init();
        $this->expectException(NotSupportedException::class);
        $logger::closeLoggerStream();

//        $logger = $this->init(1);
//        $logger->setBasePath('/tmp/PandaLogger');
//        $logger->log(Logger::DEBUG, '[SeasLog Test]', ['level' => 'DEBUG']);
//        $logger->log(Logger::WARNING, '[SeasLog Test]', ['level' => 'WARNING']);
//        $logger->log(Logger::ERROR, '[SeasLog Test]', ['level' => 'ERROR']);
//        $logger->log(Logger::INFO, '[SeasLog Test]', ['level' => 'INFO']);
//        $logger->log(Logger::CRITICAL, '[SeasLog Test]', ['level' => 'CRITICAL']);
//        $logger->log(Logger::EMERGENCY, '[SeasLog Test]', ['level' => 'EMERGENCY']);
//        $logger->log(Logger::NOTICE, '[SeasLog Test]', ['level' => 'NOTICE']);
//        $logger->log(Logger::ALERT, '[SeasLog Test]', ['level' => 'ALERT']);
//
//        $logger->log(0, '[SeasLog Test]', ['level' => 'default']);
//        $logger->log(Logger::ALL - 1, '[SeasLog Test]', ['level' => 'default']);
//
//        $this->assertTrue($logger::closeLoggerStream(SEASLOG_CLOSE_LOGGER_STREAM_MOD_ASSIGN, '/tmp/PandaLogger'));
    }
}