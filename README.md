# SeasLogger
## An effective,fast,stable log package for PHP base [SeasLog](https://github.com/SeasX/SeasLog)

[![Build Status](https://travis-ci.org/SeasX/seas-logger.svg?branch=master)](https://travis-ci.org/SeasX/seas-logger)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SeasX/seas-logger/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SeasX/seas-logger/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/SeasX/seas-logger/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SeasX/seas-logger/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/SeasX/seas-logger/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Latest Stable Version](https://poser.pugx.org/seasx/seas-logger/v/stable)](https://packagist.org/packages/seasx/seas-logger)
[![Total Downloads](https://poser.pugx.org/seasx/seas-logger/downloads)](https://packagist.org/packages/seasx/seas-logger)
[![License](https://poser.pugx.org/seasx/seas-logger/license)](https://packagist.org/packages/seasx/seas-logger)


This library implements the [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
and [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)


### Installation

Install the latest version with

```bash
$ composer require seasx/seas-logger
```

简介
---------
* 基于`Swoole`的日志组件，采用`Seaslog`日志模板,符合PSR-3，内置多种Target输出，包括`Kafka`,`SeasStash`,`Websocket`,`Console`。
* 没有做文件`Target`，在高并发下同时写同一个文件，加锁会影响性能，不加锁的方案还没通过，暂时不支持。(有好的方案请多多指教)

功能
------------------------
* 支持多种输出`Target`，可以自定义输出，继承`AbstractTarget`抽象类
* 可选记录日志等级，`Target`可以单独过滤已选的日志等级日志
* `Console`，`Websocket`输出支持彩色字体
* 支持日志Buffer，定时或定量输出。注意：定时或定量输出由于在输出前日志存在内存，宕机会有丢的风险。
* 支持`Seaslog`，`LoggerConfig`替换为`SeaslogConfig`，需要最新版`Seaslog`(还没发布)，暂时可以用`LoggerConfig`


食用方式
----
* 日志收集建议使用`docker`目录下的`docker-compose.yaml`启动套件，用`sql`目录下的`seaslog.sql`在`Clickhouse`中创建数据库和表,`LoggerConfig`中添加`KafkaTarget`，即可在`Clickhouse`中看到日志
* 默认带的BI套件为`Superset`，可以做一些分析
* 生成环境建议`StyleTarget`或者`WebsocketTarget`仅输出`Warning`和`Error`日志或仅保留`KafkaTarget`，使用`SeaslogConfig`(等待最新版本发布)配置，`KafkaTarget`可以输出所有日志
* `WebsocketTarget`由于各个框架的`Server`获取方式不一致，需要调用`setGetServer`注册获取`Server`的回调函数返回`\Swoole\Server`，默认会往所有`Websocket`连接发送日志，如果需要过滤`fd`可以自定义`Target`。
* 支持自定义模板，符号为`%A`，默认在`%M`之前，可以在`registerTemplate`里面设置统一的值，同时会被日志记录方法的`Context`参数中设置`template`键值覆盖。
* 撸码建议采用`DI`依赖注入的方式注册`Logger`


### 非Swoole用法

```php
<?php

use Seasx\SeasLogger\Logger;

$logger = new Logger();

// add records to the log
$logger->warning('Hello');
$logger->error('SeasLogger');
```
### Swoole用法
```php
<?php

use Seasx\SeasLogger\ArrayHelper;
use Seasx\SeasLogger\ConsoleColor;
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

\Co\Run(function () {
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
            'bufferSize' => 1000,//定量：buffer>=时会输出，默认为1
            'tick' => 3,//定时：每tick秒输出，默认为0，不开启定时
            'recall_depth' => 2,//与Seaslog配置参数一样，默认为0
        ]));
    /**
     * 这里可以注册一个回调函数，用来处理RequestID,Request URI,Request Method,Client IP的值
     * 下面是示例代码，具体的设置根据自己需要
     */
    $logger->getConfig()->registerTemplate(function () {
        $possibleStyles = (new ConsoleColor())->getPossibleStyles();
        $htmlColors = HtmlColor::getPossibleColors();
        if (($requestVar = Context::get(Logger::CONTEXT_KEY)) === null) {
            /** @var Request $serverRequest */
            if (($serverRequest = Context::get('request')) !== null) {
                $uri = $serverRequest->getUri();
                $requestId = $serverRequest->getAttribute(AttributeEnum::REQUESTID_ATTRIBUTE);
                !$requestId && $requestId = uniqid();
                $requestVar = array_filter([
                    '%Q' => $requestId,
                    '%R' => $uri->getPath(),
                    '%m' => $serverRequest->getMethod(),
                    '%I' => ArrayHelper::getValue($serverRequest->getServerParams(), 'remote_addr'),
                    '%c' => [
                        $possibleStyles[rand(0, count($possibleStyles) - 1)],
                        $htmlColors[rand(0, count($htmlColors) - 1)]
                    ]
                ]);
            } else {
                $requestVar = array_filter([
                    '%Q' => uniqid(),
                    '%c' => [
                        $possibleStyles[rand(0, count($possibleStyles) - 1)],
                        $htmlColors[rand(0, count($htmlColors) - 1)]
                    ]
                ]);
            }
            $requestVar['%A'] = ['123', '456'];//%A为自定义字段，会被log里面的template覆盖
            Context::set(Logger::CONTEXT_KEY, $requestVar);
        }
        return $requestVar;
    });
    /*
     * 这里区别于标准PSR-3，Context占用两个固定key(module)，作用与Seaslog的Logger参数一样,默认值为System
     * template为用户自定义模板对应的填充值，默认为[]，不填充
     */
    $logger->info("test logger $i", ['module' => 'logger', 'template' => ['abc', 'def']]);
});

```

配套组件`docker-compose`
------------------------
* `Clickhouse`日志存储。
* `ClickhouseWeb`Clickhouse操作Web界面
* `Zookeeper`Kafka依赖
* `Kafka`日志队列
* `Manager`Kafka管理Web界面
* `Superset`BI分析
* `mysql`Superset依赖
* `redis`Superset依赖
* `Grafana`监控(还没添加)


