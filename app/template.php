<?php
use Symfony\Component\HttpFoundation\Request;
use WScore\Pile\App;
use WScore\Pile\Service\Locator;
use WScore\Pile\Service\PhpEngine;

$view   = Locator::dir( __DIR__ . '/views' );
return $engine = new PhpEngine( $view );

/*
 * following code sample shows how to use a closure
 * as a template engine.
 */

/** @noinspection PhpUnreachableStatementInspection */
$view   = Locator::dir( __DIR__ . '/views' );
$engine = new PhpEngine( $view );

/**
 * @param string  $file
 * @param array   $data
 * @param Request $request
 * @return string
 */
return function( $file, $data, $request ) use ( $engine, $app )
{
    $messages = $request->attributes->get( 'messages', [ ] );
    $errors   = $request->attributes->get( 'errors', [ ] );
    $input    = $request->attributes->get( 'input', [ ] );
    $data     = $data + $messages + $errors + $input;

    /** @var App $app */
    $engine->register( 'url', $app->url() );
    return $engine->render( $file, $data );
};
