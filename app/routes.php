<?php

use Demo\Site\SampleController;
use Tuum\Router\Tuum\Router;
use Tuum\Web\Psr7\Request;
use Tuum\Web\Stack\Dispatcher;
use Tuum\Web\Stack\RouterStack;
use Tuum\Web\Web;

/** @var Web $app */

// --------------------
// create basic routers
// --------------------

$router = Router::forge();
$routes = $router->getRouting();

// ----------
// add routes
// ----------

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

/*
 * add sample controller
 */
$routes->any( '/sample*', SampleController::class);

/* -------------------
 * create router stack 
 */

$routeStack = new RouterStack($router, new Dispatcher($app));
return $routeStack;