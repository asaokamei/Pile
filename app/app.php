<?php

use Symfony\Component\HttpFoundation\Request;
use WScore\Pile\App;
use WScore\Pile\Frames\HtmlBuilder;
use WScore\Pile\Frames\Session;
use WScore\Pile\Frames\Template;
use WScore\Pile\Frames\UrlMap;

require_once( dirname( __DIR__ ) . '/vendor/autoload.php' );

$app      = boot_pile();
$request  = Request::createFromGlobals();
$response = $app->handle( $request )->send();


/**
 * @param string $route_init
 * @return App
 */
function boot_pile( $route_init = null )
{
    $environment = null;
    $env_file    = __DIR__ . '/.env.php';
    if ( file_exists( $env_file ) ) {
        $environment = include $env_file;
    }
    $route_init  = $route_init ?: 'routes.php';
    $view_folder = __DIR__ . 'views';
    $db_init     = 'database.php';

    $app = App::start();
    $app->config( __DIR__ );
    if( $environment ) {
        $app->config( __DIR__ . '/' . $environment );
    }
    $app
        ->push( Session::forge() )
        ->push( Template::forge( $view_folder ) )
        ->push( HtmlBuilder::forge() )
        ->push( UrlMap::forge( $app->locate( $route_init ) ) )
    ;
    return $app;

}
