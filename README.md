# Monolog extension for Google Cloud logging formatter

This library can re-format json log to Google Kubernetes Engine format 

## Installation

```
composer require macpaw/monolog-gke-formatter
```

## Usage

```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use MacPaw\MonologGkeFormatter\GkeFormatter;

$handler = new StreamHandler('php://stdout');
$handler->setFormatter(new GkeFormatter());
```
