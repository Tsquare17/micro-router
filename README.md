# MicroRouter

### A minimal PHP router for pretty URLs and file organization during prototyping.

#### Requirements
- PHP 7.1

#### Installation

    composer require tsquare\micro-router

#### Usage:

- public/index.php

```php
<?php

include '../vendor/autoload.php';

use Tsquare\MicroRouter\Exception\FileNotFoundException;
use Tsquare\MicroRouter\Exception\InvalidPathException;
use Tsquare\MicroRouter\MicroRouter;

try {

    $router = new MicroRouter(dirname(__FILE__, 2) . '/templates');

    // Prefix the include path with a directory, relative to the templates path.
    $router->setIncludePrefix('partials-path');

    $router->dispatch();

} catch (InvalidPathException $e) {

    echo '<h1>' . $e->getMessage() . '</h1>';

} catch (FileNotFoundException $e) {

    echo '<h1>' . $e->getMessage() . '</h1>';

}
```

- Within a template, you can include a partial, and pass it some data.
```php
<?php

$router->include('partial', ['variableName' => 'value']);

```

- You may need to include rewrite rules in .htaccess.
```apacheconfig
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
```
