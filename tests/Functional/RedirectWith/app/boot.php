<?php

use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use WScore\Pile\App;
use WScore\Pile\Frames\HtmlBuilder;
use WScore\Pile\Frames\Session;
use WScore\Pile\Frames\Template;
use WScore\Pile\Frames\UrlMap;


/**
 * @internal param string $routes
 * @param SessionStorageInterface $session
 * @return App
 */
return function()
{
    /*
     * build application
     */
    $app    = App::start( __DIR__ );
    $config = $app->config();
    $routes = 'routes.php';

    $app
        ->push( Session::forge( $config->evaluate( 'session_storage' ) ) )
        ->push( Template::forge( $app, $config->evaluate( 'template' ) ) )
        ->push( HtmlBuilder::forge() )
        ->push( UrlMap::forge( $config->locate( $routes ) ) )
    ;
    return $app;

};
