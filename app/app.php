<?php
use Tuum\Web\App;
use Tuum\Web\Psr7\RequestFactory;

require_once( dirname( __DIR__ ) . '/vendor/autoload.php' );

/** @var \Closure $boot */
/** @var App $app */
date_default_timezone_set('Asia/Tokyo');
$config = [
    'debug'  => true,
    'routes' => __DIR__.'/routes.php',
];
$boot = include( __DIR__.'/boot.php' );
$app  = $boot($config);

$request  = RequestFactory::fromGlobals();
$response = $app->__invoke( $request );
$response->send();

