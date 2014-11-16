<?php

use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
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
return function( $routes = null )
{
    /*
     * build application
     */
    $app = App::start( __DIR__ );
    $routes = $routes ?: 'routes.php';
    $views  = Locator::dir( __DIR__ .'/views' );
    $app
        ->push( Session::forge( $app, new MockArraySessionStorage() ) )
        ->push( Template::forge( $app, $views ) )
        ->push( HtmlBuilder::forge( $app ) )
        ->push( UrlMap::forge( $app->config()->locate( $routes ) ) )
    ;
    return $app;

};
