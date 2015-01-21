<?php
use Tuum\Web\App;
use Tuum\Web\Http\Request;

require_once( dirname( __DIR__ ) . '/vendor/autoload.php' );

/** @var \Closure $boot */
/** @var App $app */
$config = [
    'debug' => true,
    'routes' => __DIR__.'/routes.php',
];
$boot = include( __DIR__.'/boot.php' );
$app  = $boot([]);

$request  = Request::startGlobal();
$response = $app->__invoke( $request );
$response->send();

