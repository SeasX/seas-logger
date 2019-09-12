<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Seasx\SeasLogger\Context;
use Seasx\SeasLogger\HtmlColor;
use Seasx\SeasLogger\Kafka\Broker;
use Seasx\SeasLogger\Kafka\Producter;
use Seasx\SeasLogger\Kafka\ProducterConfig;
use Seasx\SeasLogger\Kafka\Socket\Pool;
use Seasx\SeasLogger\Logger;
use Seasx\SeasLogger\LoggerConfig;
use Seasx\SeasLogger\Targets\KafkaTarget;
use Seasx\SeasLogger\Targets\StyleTarget;
use Wujunze\Colors;

rgo(function () {
    $logger = new Logger(
        new LoggerConfig([
            'echo' => new StyleTarget([
                'info',//过滤等级，默认为[]全部输出
            ]),
            'kafka' => new KafkaTarget(
                new Producter(
                    new ProducterConfig([
                        'requiredAck' => 0,
                    ]),
                    new Broker([
                        'brokerVersion' => '1.0.0',
                    ]), new Pool([
                    'uri' => '192.168.5.134:9092'
                ])),
                [],
                'seaslog_test',
                [['task_id', 'string'], ['worker_id', 'string']]//自定义模板添加的处理字段，顺序需要按照日志记录中的template数组一致
            )
        ], [
            'appName' => 'Seaslog',//应用名：远程发送日志的时候用于区分是哪个应用发送来的
            'bufferSize' => 1,//定量：buffer>=时会输出，默认为1，每次记录都会输出
            'tick' => 0,//定时：每tick秒输出，默认为0，不开启定时
            'recall_depth' => 2,//与Seaslog配置参数一样，默认为0
        ]));
    /**
     * 这里可以注册两个回调函数，会在log方法前执行，可以用来处理RequestID,Request URI,Request Method,Client IP的值
     * 下面是示例代码，具体的设置根据自己需要
     */
    $logger->getConfig()->registerTemplate(function () {
        $possibleStyles = (new Colors())->getForegroundColors();
        $htmlColors = HtmlColor::getPossibleColors();
        if (($requestVar = Context::get(Logger::CONTEXT_KEY)) === null) {
            $requestVar = array_filter([
                '%Q' => uniqid(),
                '%c' => [
                    'console' => $possibleStyles[rand(0, count($possibleStyles) - 1)],
                    'websocket' => $htmlColors[rand(0, count($htmlColors) - 1)]
                ]
            ]);
            $requestVar['%A'] = ['123', '456'];
            Context::set(Logger::CONTEXT_KEY, $requestVar);
        }
        return $requestVar;
    });
    /*
     * 这里区别于标准PSR-3，Context占用两个固定key(module)，作用与Seaslog的Logger参数一样,默认值为System
     * template为用户自定义模板对应的填充值，默认为[]，不填充
     */
    for ($i = 0; $i < 1; $i++) {
        $logger->info("test logger $i", ['module' => 'logger', 'template' => ['abc', 'def']]);
    }
});

swoole_event_wait();