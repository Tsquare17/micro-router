<?php

namespace Tsquare;

use Tsquare\Exception\FileNotFoundException;
use Tsquare\Exception\InvalidPathException;

/**
 * Class MicroRouter
 * @package Tsquare
 */
class MicroRouter
{
    /**
     * Path to the PHP template files.
     * @var string $viewsPath
     */
    protected $viewsPath;

    /**
     * The Request URI.
     * @var $uri
     */
    protected $uri;

    /**
     * MicroRouter constructor.
     * @param string $viewsPath
     * @throws FileNotFoundException
     */
    public function __construct(string $viewsPath)
    {
        if (!is_dir($viewsPath)) {
            throw new InvalidPathException('');
        }

        $this->viewsPath = rtrim($viewsPath, '/') . '/';

        $this->setURI();

        $route = $this->matchRequest();

        if ($route !== '') {
            include $route;
            return;
        }

        throw new FileNotFoundException('404 - File not found');
    }

    /**
     * Sets the URI, stripping unwanted characters.
     */
    private function setURI(): void
    {
        $this->uri = trim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
    }

    /**
     * Match the request with a file.
     * @return string
     */
    private function matchRequest(): string
    {
        $file = $this->viewsPath . $this->uri . '.php';

        if (is_file($file)) {
            return $file;
        }

        if ($this->uri === '' && is_file($this->viewsPath . 'index.php')) {
            return $this->viewsPath . 'index.php';
        }

        if (is_file($this->viewsPath . '404.php')) {
            return $this->viewsPath . '404.php';
        }

        return '';
    }
}
