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

    $router = new MicroRouter(dirname(__FILE__, 2) . '/templates');

    // Set the path to partials, relative to the templates path.
    $router->setPartialsPath('partials-path');

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

$router->includePartial('partial', ['variableName' => 'value']);

```

- Your may need to include rewrite rules in .htaccess.
```apacheconfig
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
```
