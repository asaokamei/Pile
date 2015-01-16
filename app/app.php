<?php
use Tuum\Locator\Container;
use Tuum\Locator\Locator;
use Tuum\Web\App;
use Tuum\Web\Http\Request;

require_once( dirname( __DIR__ ) . '/vendor/autoload.php' );

$loc = new Locator(__DIR__.'/config');
$loc->addRoot( dirname(__DIR__).'/vendor/tuum/stack/scripts');
$app = new App(new Container($loc));

/*
 * set up directories
 */
$app->set('view-dir', __DIR__.'/views');
$app->set(App::DEBUG, true);

/*
 * set up handlers
 */
$app->push($app->get('error-handler'));
$app->push($app->get('session-handler'));
$app->push($app->get('route-handler'));

/*
 * set up releases
 */
$app->push($app->get('view-release'));
$app->push($app->get('session-release'));
$app->push($app->get('error-release'));

$request  = Request::startGlobal();
$response = $app->__invoke( $request );
$response->send();

