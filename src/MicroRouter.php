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
        } else {
            header('HTTP/1.0 404 Not Found');
            throw new FileNotFoundException('404 - File not found');
        }
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
        // URI matching the name of the file.
        if (is_file($this->templatesPath . $this->uri . '.php')) {
            return $this->templatesPath . $this->uri . '.php';
        }

        // URI matches a directory where index.php exists.
        if (is_file($this->templatesPath . $this->uri . '/index.php')) {
            return $this->templatesPath . $this->uri . ($this->uri === '' ? '' : '/') . 'index.php';
        }

        // URI matches a php file.
        if (strpos(strrev($this->uri), 'php.') === 0) {
            return $this->templatesPath . $this->uri;
        }

        // URI contains a file extension.
        $info = pathinfo($this->uri);
        if (isset($info['extension']) && $info['extension'] !== null) {
            return $this->templatesPath . $this->uri;
        }

        // A 404 template exists.
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
