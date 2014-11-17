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
return function( $routes = null )
{
    /*
     * build application
     */
    $app    = App::start();
    $config = $app->config();
    $routes = $routes ?: 'routes.php';

    $app
        ->push( Session::forge() )
        ->push( Template::forge( $app, $config->evaluate( 'template.php' ) ) )
        ->push( HtmlBuilder::forge() )
        ->push( UrlMap::forge( $config->evaluate( $routes ) ) )
    ;
    return $app;

};
