<?php

use WScore\Pile\App;
use WScore\Pile\Frames\HtmlBuilder;
use WScore\Pile\Frames\Session;
use WScore\Pile\Frames\Template;
use WScore\Pile\Frames\UrlMap;


/**
 * @param string $routes
 * @return App
 */
return function ( $routes = null ) {
    /*
     * start application
     */
    $app    = App::start( __DIR__ );
    $config = $app->config();
    $routes = $routes ?: 'routes.php';
    /*
     * build stack
     */
    $app
        ->push( Session::forge( $config->evaluate( 'session_storage' ) ) )
        ->push( Template::forge( $app, $config->evaluate( 'template' ) ) )
        ->push( HtmlBuilder::forge() )
        ->push( UrlMap::forge( $config->locate( $routes ) ) );
    return $app;

};
