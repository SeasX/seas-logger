# SeasLogger
## An effective,fast,stable log package for PHP base [SeasLog](https://github.com/SeasX/SeasLog)

[![Build Status](https://travis-ci.org/SeasX/seas-logger.svg?branch=master)](https://travis-ci.org/SeasX/seas-logger)

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

use SeasX\SeasLogger;

$logger = new SeasLogger();

// add records to the log
$logger->warning('Hello');
$logger->error('SeasLogger');
```

### See more
[https://github.com/SeasX/SeasLog](https://github.com/SeasX/SeasLog)