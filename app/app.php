<?php

use Symfony\Component\HttpFoundation\Request;
use WScore\Pile\App;

require_once( dirname( __DIR__ ) . '/vendor/autoload.php' );
$boot_pile = include( __DIR__ . '/boot.php' );

/** @var \Closure $boot_pile */
/** @var App $app */
$app      = $boot_pile();
$request  = Request::createFromGlobals();
$response = $app->handle( $request )->send();

