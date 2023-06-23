<?php

namespace Tsquare\MicroRouter;

/**
 * Render template.
 *
 * @param $route
 * @param $router
 * @param array $data
 */
function render($route, $router, $data = [])
{
    extract($data, EXTR_OVERWRITE);
    unset($data);

    include $route;
}
