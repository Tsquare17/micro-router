# MicroRouter

### A minimal PHP router for pretty URLs and file organization during prototyping.

#### Installation

    composer require tsquare\micro-router --prefer-source

#### Usage:

- public/index.php

```php
use Tsquare\MicroRouter;

new MicroRouter(dirname(__FILE__, 1) . '/path-to-views');
```
