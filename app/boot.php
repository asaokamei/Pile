<?php

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
function boot_pile( $routes = null )
{
    /*
     * build application
     */
    $app = App::start();

    $routes = $routes ?: 'routes.php';
    $views  = Locator::dir( __DIR__ .'/views' );
    $app
        ->push( Session::forge() )
        ->push( Template::forge( $app, $views ) )
        ->push( HtmlBuilder::forge() )
        ->push( UrlMap::forge( $app->config()->locate( $routes ) ) )
    ;
    return $app;

}
