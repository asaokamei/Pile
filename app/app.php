<?php

use Symfony\Component\HttpFoundation\Request;

require_once( dirname( __DIR__ ) . '/vendor/autoload.php' );
require_once( __DIR__ . '/boot.php' );

$app      = boot_pile();
$request  = Request::createFromGlobals();
$response = $app->handle( $request )->send();

