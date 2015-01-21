<?php

use Tuum\Router\PhRouter\Router;
use Tuum\Web\App;
use Tuum\Web\Http\Request;

/** @var App $app */
/** @var Router $router */

$router = $app->get(APP::ROUTER);
$routes = $router->getRouting();

$routes->get( '/closure', function($request) {
    /** @var Request $request */
    return $request->respond()->html('
    <html><body>
    <h1>This is from a closure!</h1>
    </body></html>
    ');
});

$routes->get( '/closure-view', function($request) {
    /** @var Request $request */
    return $request->respond()->view('closure-view');
});

