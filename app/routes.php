<?php

use Demo\Site\SampleController;
use Tuum\Router\Tuum\Router;
use Tuum\Web\App;
use Tuum\Web\Psr7\Request;
use Tuum\Web\Web;

/** @var Web $app */
/** @var Router $router */

$router = Tuum\Router\Tuum\Router::forge();
$routes = $router->getRouting();

$routes->get( '/closure', function($request) {
    /** @var Request $request */
    return $request->respond()->asHtml('
    <html><body>
    <h1>This is from a closure!</h1>
    </body></html>
    ');
});

$routes->get( '/closure-view', function($request) {
    /** @var Request $request */
    return $request->respond()->asView('closure-view');
});

$routes->get( '/', function($request) {
    /** @var Request $request */
    return $request->respond()->asView('index');
});

$routes->any( '/sample*', SampleController::class);

$routeStack = \Tuum\Web\Stack\Routes::forge($router);
return $routeStack;