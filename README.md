# SeasLogger
## An effective,fast,stable log package for PHP base [SeasLog](https://github.com/SeasX/SeasLog)

[![Build Status](https://travis-ci.org/SeasX/seas-logger.svg?branch=master)](https://travis-ci.org/SeasX/seas-logger)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SeasX/seas-logger/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SeasX/seas-logger/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/SeasX/seas-logger/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SeasX/seas-logger/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/SeasX/seas-logger/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Build Status](https://scrutinizer-ci.com/g/SeasX/seas-logger/badges/build.png?b=master)](https://scrutinizer-ci.com/g/SeasX/seas-logger/build-status/master)

This library implements the [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
and [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)


### Installation

Install the latest version with

```bash
$ composer require seasx/seas-logger
```

### Basic Usage

```php
<?php

use Seasx\SeasLogger\Logger;

$logger = new Logger();

// add records to the log
$logger->warning('Hello');
$logger->error('SeasLogger');
```
### configuration for laravel/lumen >=5.6
add seaslog configuration in config/logging.php
```php
'channels' => [
    ...
    'seaslog' => [
        'driver' => 'custom',
        'via' => \Seasx\SeasLogger\Logger::class,
        'path' => '/path/to/logfile',
    ],
    ...
]
```

edit .env file to use seaslog
```php
LOG_CHANNEL=seaslog
```

### See more
[https://github.com/SeasX/SeasLog](https://github.com/SeasX/SeasLog)

