<?php

use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use WScore\Pile\App;
use WScore\Pile\Frames\HtmlBuilder;
use WScore\Pile\Frames\Session;
use WScore\Pile\Frames\Template;
use WScore\Pile\Frames\UrlMap;
use WScore\Pile\Service\Locator;


/**
 * @param string $routes
 * @return App
 */
return function( $routes = null )
{
    /*
     * start application
     */
    $app = App::start( __DIR__ );

    $routes = $routes ?: 'routes.php';
    $views  = Locator::dir( __DIR__ .'/views' );
    /*
     * build stack
     */
    $app
        ->push( Session::forge( new MockArraySessionStorage() ) )
        ->push( Template::forge( $app, $views ) )
        ->push( HtmlBuilder::forge() )
        ->push( UrlMap::forge( $app->config()->locate( $routes ) ) )
    ;
    return $app;

};
