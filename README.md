# MicroRouter

### A minimal PHP router for pretty URLs and file organization during prototyping.

#### Installation

    composer require tsquare\micro-router

#### Usage:

- public/index.php

```php
<?php

include '../vendor/autoload.php';

use Tsquare\Exception\FileNotFoundException;
use Tsquare\Exception\InvalidPathException;
use Tsquare\MicroRouter;

try {

    new MicroRouter(dirname(__FILE__, 2) . '/views');

} catch (InvalidPathException $e) {

    echo '<h1>' . $e->getMessage() . '</h1>';

} catch (FileNotFoundException $e) {

    echo '<h1>' . $e->getMessage() . '</h1>';

}
```
