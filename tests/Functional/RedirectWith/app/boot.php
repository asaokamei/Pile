<?php

use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use WScore\Pile\App;
use WScore\Pile\Frames\HtmlBuilder;
use WScore\Pile\Frames\Session;
use WScore\Pile\Frames\Template;
use WScore\Pile\Frames\UrlMap;
use WScore\Pile\Locator;


/**
 * @internal param string $routes
 * @param SessionStorageInterface $session
 * @return App
 */
return function( $session )
{
    /*
     * build application
     */
    $app = App::start( __DIR__ );
    $routes = 'routes.php';
    $views  = Locator::dir( __DIR__ .'/views' );
    $app
        ->push( Session::forge( $app, $session ) )
        ->push( Template::forge( $app, $views ) )
        ->push( HtmlBuilder::forge( $app ) )
        ->push( UrlMap::forge( $app->config()->locate( $routes ) ) )
    ;
    return $app;

};
