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
     * Prefix the MicroRouter::prefix() with a directory within the templates path.
     * @var $includePrefix
     */
    protected $includePrefix = null;

    /**
     * The route to render.
     * @var $route
     */
    protected $route = null;

    /**
     * MicroRouter constructor.
     * @param string $templatesPath;
     * @throws FileNotFoundException
     * @throws InvalidPathException
     */
    public function __construct(string $templatesPath)
    {
        if (!is_dir($templatesPath)) {
            throw new InvalidPathException('Templates path not found');
        }

        $this->templatesPath = rtrim($templatesPath, '/') . '/';

        $this->setURI();

        $route = $this->getTemplate($this->uri);

        $validPath = $this->validateRequestedPath($route);

        if (!$route) {
            header('HTTP/1.0 404 Not Found');
            throw new FileNotFoundException('404 - File not found');
        }

        if (!$validPath) {
            header('HTTP/1.0 403 Forbidden');
            throw new InvalidPathException('Invalid request');
        }

        $this->route = $route;
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
     * @param $template
     * @return ?string
     */
    private function getTemplate(string $template): ?string
    {
        // URI matching the name of the file.
        if (is_file($this->templatesPath . $template . '.php')) {
            return $this->templatesPath . $template . '.php';
        }

        // URI matches a directory where index.php exists.
        if (is_file($this->templatesPath . $template . '/index.php')) {
            return $this->templatesPath . $template . ($template === '' ? '' : '/') . 'index.php';
        }

        // URI matches a php file.
        if (strpos(strrev($template), 'php.') === 0) {
            return $this->templatesPath . $template;
        }

        // URI contains a file extension.
        $info = pathinfo($template);
        if (
            isset($info['extension'])
            && $info['extension'] !== null
            && file_exists($this->templatesPath . $template)
        ) {
            return $this->templatesPath . $template;
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

    /**
     * Set the path to partials.
     *
     * @param string $path
     */
    public function setIncludePrefix(string $path): void
    {
        $this->includePrefix = trim($path, '/');
    }

    /**
     * Include a template.
     *
     * @param $template
     * @param array $data
     */
    public function include($template, $data = []): void
    {
        render(
            $this->includePrefix
            ? $this->templatesPath . $this->includePrefix . '/' . $template . '.php'
            : $this->templatesPath . $template . '.php',
            $this,
            $data
        );
    }

    /**
     * Dispatch.
     * @param array $data
     */
    public function dispatch($data = []): void
    {
        if (!function_exists('Tsquare\\render')) {
            function render($route, $router, $data = [])
            {
                extract($data, EXTR_OVERWRITE);
                unset($data);

                include $route;
            }
        }

        render($this->route, $this, $data);
    }
}
