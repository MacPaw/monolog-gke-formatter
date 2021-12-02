Monolog extension for Google Cloud logging formatter
=================================

This library can re-format json log to Google Kubernetes Engine format

| Version | Build Status |
|:---------:|:-------------:|
| `master`| [![CI][master Build Status Image]][master Build Status] |

## Installation
Open a command console, enter your project directory and execute the
following command to download the latest stable version:
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

[master Build Status]: https://github.com/macpaw/monolog-gke-formatter/actions?query=workflow%3ACI+branch%3Amaster
[master Build Status Image]: https://github.com/macpaw/monolog-gke-formatter/workflows/CI/badge.svg?branch=master
