<?php
declare(strict_types=1);

namespace Seasx\SeasLogger\Tests;

use Seasx\SeasLogger\AbstractConfig;
use Seasx\SeasLogger\Logger;
use Seasx\SeasLogger\LoggerConfig;
use Seasx\SeasLogger\SeaslogConfig;
use Seasx\SeasLogger\Targets\StyleTarget;

class SwooleLoggerTest extends TestCase
{
    public function testGetLogger()
    {
        $logger = new Logger();
        $this->assertEmpty($logger->getConfig());

        $logger = $this->init();
        $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());

        $logger = $this->init(1);
        $this->assertInstanceOf(SeaslogConfig::class, $logger->getConfig());
    }

    /**
     * @expectedException  \Seasx\SeasLogger\Exceptions\NotSupportedException
     */
    public function testGetBasePath()
    {
        $logger = $this->init();
        $logger->getBasePath();

        $logger = $this->init(1);
        $logger->setBasePath('/tmp/seaslogger');
        $basePath = $logger->getBasePath();
        $this->assertEquals('/tmp/seaslogger', $basePath);
    }

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

    /**
     * @expectedException  \Seasx\SeasLogger\Exceptions\NotSupportedException
     */
    public function testSetRequestID()
    {
        $logger = $this->init();
        $logger::setRequestID(1024);

        $logger = $this->init(1);
        $result = $logger::setRequestID(1024);
        $this->assertTrue($result);
    }

    /**
     * @expectedException  \Seasx\SeasLogger\Exceptions\NotSupportedException
     */
    public function testGetRequestID()
    {
        $logger = $this->init();
        $logger::getRequestID();

        $logger = $this->init(1);
        $result = $logger::setRequestID(1024);
        $this->assertTrue($result);
        $requestID = $logger::getRequestID();
        $this->assertEquals(1024, $requestID);
    }

    public function testEmergency()
    {
        \Co\run(function () {
            $logger = $this->init();
            $this->assertInstanceOf(Logger::class, $logger);
            $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());
            $logger->emergency('[LoggerConfig Test]', ['level' => 'emergency']);

//            $logger = $this->init(1);
//            $this->assertInstanceOf(Logger::class, $logger);
//            $logger->emergency('[SeasLog Test]', ['level' => 'emergency']);
        });
    }

    public function testAlert()
    {
        \Co\run(function () {
            $logger = $this->init();
            $this->assertInstanceOf(Logger::class, $logger);
            $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());
            $logger->alert('[LoggerConfig Test]', ['level' => 'alert']);

//            $logger = $this->init(1);
//            $this->assertInstanceOf(Logger::class, $logger);
//            $logger->alert('[SeasLog Test]', ['level' => 'alert']);
        });
    }

    public function testCritical()
    {
        \Co\run(function () {
            $logger = $this->init();
            $this->assertInstanceOf(Logger::class, $logger);
            $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());
            $logger->critical('[LoggerConfig Test]', ['level' => 'critical']);

//            $logger = $this->init(1);
//            $this->assertInstanceOf(Logger::class, $logger);
//            $logger->critical('[SeasLog Test]', ['level' => 'critical']);
        });
    }

    public function testError()
    {
        \Co\run(function () {
            $logger = $this->init();
            $this->assertInstanceOf(Logger::class, $logger);
            $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());
            $logger->error('[LoggerConfig Test]', ['level' => 'error']);

//            $logger = $this->init(1);
//            $this->assertInstanceOf(Logger::class, $logger);
//            $logger->error('[SeasLog Test]', ['level' => 'error']);
        });
    }

    public function testWarning()
    {
        \Co\run(function () {
            $logger = $this->init();
            $this->assertInstanceOf(Logger::class, $logger);
            $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());
            $logger->warning('[LoggerConfig Test]', ['level' => 'warning']);

//            $logger = $this->init(1);
//            $this->assertInstanceOf(Logger::class, $logger);
//            $logger->warning('[SeasLog Test]', ['level' => 'warning']);
        });
    }

    public function testNotice()
    {
        \Co\run(function () {
            $logger = $this->init();
            $this->assertInstanceOf(Logger::class, $logger);
            $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());
            $logger->notice('[LoggerConfig Test]', ['level' => 'notice']);

//            $logger = $this->init(1);
//            $this->assertInstanceOf(Logger::class, $logger);
//            $logger->notice('[SeasLog Test]', ['level' => 'notice']);
        });
    }

    public function testInfo()
    {
        \Co\run(function () {
            $logger = $this->init();
            $this->assertInstanceOf(Logger::class, $logger);
            $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());
            $logger->info('[LoggerConfig Test]', ['level' => 'info']);

//            $logger = $this->init(1);
//            $this->assertInstanceOf(Logger::class, $logger);
//            $logger->info('[SeasLog Test]', ['level' => 'info']);
        });
    }

    public function testDebug()
    {
        \Co\run(function () {
            $logger = $this->init();
            $this->assertInstanceOf(Logger::class, $logger);
            $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());
            $logger->debug('[LoggerConfig Test]', ['level' => 'debug']);

//            $logger = $this->init(1);
//            $this->assertInstanceOf(Logger::class, $logger);
//            $logger->debug('[SeasLog Test]', ['level' => 'debug']);
        });
    }

    public function testLog()
    {
        \Co\run(function () {
            $logger = $this->init();
            $this->assertInstanceOf(Logger::class, $logger);
            $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());
            $logger->log(Logger::DEBUG, '[LoggerConfig Test]', ['level' => 'log']);

//            $logger = $this->init(1);
//            $this->assertInstanceOf(Logger::class, $logger);
//            $logger->log(Logger::DEBUG, '[SeasLog Test]', ['level' => 'log']);
        });
    }

    public function testLogWithFieldTemplate()
    {
        \Co\run(function () {
            $logger = new Logger(
                new LoggerConfig([
                    'echo' => new StyleTarget()
                ], [
                    'appName' => 'Seaslog',
                    'bufferSize' => 1,
                    'tick' => 0,
                    'recall_depth' => 2,
                ]));
            $this->assertInstanceOf(Logger::class, $logger);
            $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());
            $logger->log(Logger::INFO, '[LoggerConfig FieldTemplate]', ['level' => 'log', 'template' => ['test']]);

//            $logger = $this->init(1);
//            $this->assertInstanceOf(Logger::class, $logger);
//            $logger->log(Logger::INFO, '[SeasLog FieldTemplate]', ['level' => 'log', 'template' => ['test']]);
        });
    }

    public function testLogWithJsonTemplate()
    {
        \Co\run(function () {
            $logger = new Logger(
                new LoggerConfig([
                    'echo' => new StyleTarget()
                ], [
                    'appName' => 'Seaslog',
                    'customerType' => AbstractConfig::TYPE_JSON,
                    'bufferSize' => 1,
                    'tick' => 0,
                    'recall_depth' => 2,
                ]));
            $this->assertInstanceOf(Logger::class, $logger);
            $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());
            $logger->log(Logger::INFO, '[LoggerConfig JsonTemplate]',
                ['level' => 'log', 'template' => ['task_type' => 'test']]);

//            $logger = new Logger(
//                new SeaslogConfig([
//                    'echo' => new StyleTarget()
//                ], [
//                    'appName' => 'Seaslog',
//                    'customerType' => AbstractConfig::TYPE_JSON,
//                    'bufferSize' => 1,
//                    'tick' => 0,
//                    'recall_depth' => 2,
//                ]));
//            $this->assertInstanceOf(Logger::class, $logger);
//            $logger->log(Logger::INFO, '[SeasLog JsonTemplate]', ['level' => 'log', 'template' => ['task_type' => 'test']]);
        });
    }

    public function testRequestLevel()
    {
        \Co\run(function () {
            $logger = $this->init();
            $this->assertInstanceOf(Logger::class, $logger);
            $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());
            $logger->setRequestLevel(Logger::ALL);
            $logger->log(Logger::DEBUG, '[LoggerConfig Test]', ['level' => 'DEBUG']);
            $logger->log(Logger::WARNING, '[LoggerConfig Test]', ['level' => 'WARNING']);
            $logger->log(Logger::ERROR, '[LoggerConfig Test]', ['level' => 'ERROR']);
            $logger->log(Logger::INFO, '[LoggerConfig Test]', ['level' => 'INFO']);
            $logger->log(Logger::CRITICAL, '[LoggerConfig Test]', ['level' => 'CRITICAL']);
            $logger->log(Logger::EMERGENCY, '[LoggerConfig Test]', ['level' => 'EMERGENCY']);
            $logger->log(Logger::NOTICE, '[LoggerConfig Test]', ['level' => 'NOTICE']);
            $logger->log(Logger::ALERT, '[LoggerConfig Test]', ['level' => 'ALERT']);
            $logger->log(0, '[LoggerConfig Test]', ['level' => 'default']);
            $logger->log(Logger::ALL - 1, '[LoggerConfig Test]', ['level' => 'default']);

//            $logger = $this->init(1);
//            $this->assertInstanceOf(Logger::class, $logger);
//            $logger->setRequestLevel(Logger::ALL);
//            $logger->log(Logger::DEBUG, '[SeasLog Test]', ['level' => 'DEBUG']);
//            $logger->log(Logger::WARNING, '[SeasLog Test]', ['level' => 'WARNING']);
//            $logger->log(Logger::ERROR, '[SeasLog Test]', ['level' => 'ERROR']);
//            $logger->log(Logger::INFO, '[SeasLog Test]', ['level' => 'INFO']);
//            $logger->log(Logger::CRITICAL, '[SeasLog Test]', ['level' => 'CRITICAL']);
//            $logger->log(Logger::EMERGENCY, '[SeasLog Test]', ['level' => 'EMERGENCY']);
//            $logger->log(Logger::NOTICE, '[SeasLog Test]', ['level' => 'NOTICE']);
//            $logger->log(Logger::ALERT, '[SeasLog Test]', ['level' => 'ALERT']);
//            $logger->log(0, '[SeasLog Test]', ['level' => 'default']);
//            $logger->log(Logger::ALL - 1, '[SeasLog Test]', ['level' => 'default']);
        });
    }

    /**
     * @expectedException  \Seasx\SeasLogger\Exceptions\NotSupportedException
     */
    public function testSetLogger()
    {
        $logger = $this->init();
        $logger::setLogger('seas');

        $logger = $this->init(1);
        $result = $logger::setLogger('seas');
        $this->assertTrue($result);
    }

    /**
     * @expectedException  \Seasx\SeasLogger\Exceptions\NotSupportedException
     */
    public function testGetLastLogger()
    {
        $logger = $this->init();
        $logger::getLastLogger();

        $logger = $this->init(1);
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

        $logger = $this->init(1);
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

        $logger = $this->init(1);
        $result = $logger::setDatetimeFormat('Y-m-d H:i:s');
        $this->assertTrue($result);
        $format = $logger::getDatetimeFormat();
        $this->assertEquals('Y-m-d H:i:s', $format);
    }

    /**
     * @expectedException  \Seasx\SeasLogger\Exceptions\NotSupportedException
     */
    public function testAnalyzerCount()
    {
        $logger = $this->init();
        $logger::analyzerCount();

        $logger = $this->init(1);
        $result = $logger::analyzerCount();
        $this->assertNotNull($result);
    }

    /**
     * @expectedException  \Seasx\SeasLogger\Exceptions\NotSupportedException
     */
    public function testAnalyzerDetail()
    {
        $logger = $this->init();
        $logger::analyzerDetail();

        $logger = $this->init(1);
        $result = $logger::analyzerDetail();
        $this->assertNotNull($result);
    }

    public function testGetBuffer()
    {
        \Co\run(function (){
            $logger = $this->init();
            $this->assertInstanceOf(Logger::class, $logger);
            $this->assertInstanceOf(LoggerConfig::class, $logger->getConfig());
            $logger->info('[LoggerConfig Test]', ['level' => 'info']);
            $buffer = $logger::getBuffer();
            $this->assertNotNull($buffer);

//            $logger = $this->init(1);
//            $this->assertInstanceOf(Logger::class, $logger);
//            $logger->info('[SeasLog Test]', ['level' => 'info']);
//            $buffer = $logger::getBuffer();
//            $this->assertNotNull($buffer);
        });


    }

    /**
     * @expectedException  \Seasx\SeasLogger\Exceptions\NotSupportedException
     */
    public function testFlushBuffer()
    {
        $logger = $this->init();
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

        $logger = $this->init(1);
        $seasLogger = $logger(['path' => '/tmp/logger']);
        $this->assertInstanceOf(Logger::class, $seasLogger);
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

    /**
     * @expectedException  \Seasx\SeasLogger\Exceptions\NotSupportedException
     */
    public function testCloseLoggerStream()
    {
        $logger = $this->init();
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