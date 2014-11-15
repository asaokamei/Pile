<?php

use WScore\Pile\App;
use WScore\Pile\Frames\HtmlBuilder;
use WScore\Pile\Frames\Session;
use WScore\Pile\Frames\Template;
use WScore\Pile\Frames\UrlMap;
use WScore\Pile\Locator;


/**
 * @param string $routes
 * @return App
 */
function boot_pile( $routes = null )
{
    $routes = $routes ?: 'routes.php';
    $config = Locator::dir(__DIR__ );
    $views  = Locator::dir( __DIR__ .'/views' );
    /*
     * set up config based on environment
     */
    $environment = null;
    $env_file    = __DIR__ . '/.env.php';
    if ( file_exists( $env_file ) ) {
        $environment = include $env_file;
        $config->addRoot( $env_file );
    }
    /*
     * build application
     */
    $app = App::start();
    $app
        ->push( Session::forge( $app ) )
        ->push( Template::forge( $app, $views ) )
        ->push( HtmlBuilder::forge( $app ) )
        ->push( UrlMap::forge( $config->locate( $routes ) ) )
    ;
    return $app;

}
