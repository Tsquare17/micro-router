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
     * @var string $templatesPath;
     */
    protected $templatesPath;

    /**
     * The Request URI.
     * @var $uri
     */
    protected $uri;

    /**
     * MicroRouter constructor.
     * @param string $templatesPath;
     * @throws FileNotFoundException
     * @throws InvalidPathException
     */
    public function __construct(string $templatesPath)
    {
        if (!is_dir($templatesPath)) {
            throw new InvalidPathException('View path not found');
        }

        $this->templatesPath = rtrim($templatesPath, '/') . '/';

        $this->setURI();

        $route = $this->matchRequest();

        $validPath = $this->validateRequestedPath($route);

        if ($route && !$validPath) {
            header('HTTP/1.0 403 Forbidden');
            throw new InvalidPathException('Invalid request');
        }

        if ($route) {
            include $route;
            return;
        }

        header('HTTP/1.0 404 Not Found');
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
     * @return ?string
     */
    private function matchRequest(): ?string
    {
        $file = $this->templatesPath . $this->uri . '.php';

        if (is_file($file)) {
            return $file;
        }

        if ($this->uri === '' && is_file($this->templatesPath . 'index.php')) {
            return $this->templatesPath . 'index.php';
        }

        if (strpos(strrev($this->uri), 'php.') === 0) {
            return $this->templatesPath . $this->uri;
        }

        $info = pathinfo($this->uri);
        if (isset($info['extension']) && $info['extension'] !== null) {
            return $this->templatesPath . $this->uri;
        }

        if (is_file($this->templatesPath . '404.php')) {
            return $this->templatesPath . '404.php';
        }

        return null;
    }

    /**
     * Ensure the path to the file requested is within the templates path.
     *
     * @param $path
     * @return bool
     */
    private function validateRequestedPath($path): bool
    {
        $realPath = realpath($path);

        return $path && strpos($path, $realPath) === 0;
    }
}
