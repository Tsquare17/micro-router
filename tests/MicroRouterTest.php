<?php

namespace Tsquare\Tests;

use Tsquare\Exception\FileNotFoundException;
use Tsquare\MicroRouter;
use PHPUnit\Framework\TestCase;
use Tsquare\Exception\InvalidPathException;

class MicroRouterTest extends TestCase
{
    private $templatesPath = __DIR__ . '/Fixtures';

    private function routeOutputs($route, $outputs): void
    {
        $_SERVER['REQUEST_URI'] = $route;

        $router = new MicroRouter($this->templatesPath);

        $router->setIncludePrefix('partials');

        $router->dispatch();

        $this->expectOutputString($outputs);
    }

    /** @test */
    public function throws_exception_on_invalid_views_path(): void
    {
        $this->expectException(InvalidPathException::class);

        new MicroRouter('non-existent path');
    }

    /** @test */
    public function matches_index_route(): void
    {
        $this->routeOutputs('/', 'index');
    }

    /** @test */
    public function matches_foo_route(): void
    {
        $this->routeOutputs('/foo', 'foo');
    }

    /** @test */
    public function matches_child_page_route(): void
    {
        $this->routeOutputs('/dir/child', 'child');
    }

    /** @test */
    public function catches_404_if_file_exists(): void
    {
        $this->routeOutputs('/bar', '404');
    }

    /** @test */
    public function route_matches_with_end_slash(): void
    {
        $this->routeOutputs('/foo/', 'foo');
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function exception_is_thrown_if_no_match_and_no_404_file_exists(): void
    {
        $_SERVER['REQUEST_URI'] = 'no matching file';

        $this->expectException(FileNotFoundException::class);

        new MicroRouter(__DIR__);
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function exception_is_thrown_if_requested_path_is_invalid(): void
    {
        $_SERVER['REQUEST_URI'] = '/../MicroRouterTest.php';

        $this->expectException(InvalidPathException::class);

        $router = new MicroRouter($this->templatesPath);

        $router->dispatch();
    }

    /** @test */
    public function matches_directory_index(): void
    {
        $this->routeOutputs('/dir', 'index');
    }

    /**
     * @test
     */
    public function can_get_template_from_inside_template(): void
    {
        $this->routeOutputs('/partial-test', 'included partial');
    }

    /** @test */
    public function can_pass_vars_to_partials(): void
    {
        $this->routeOutputs('/partial-test-with-vars', 'bar');
    }
}
