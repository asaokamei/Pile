<?php
use Tuum\Locator\Container;
use Tuum\Locator\Locator;
use Tuum\Web\App;

return function( array $config ) {

    /*
     * set up default configuration.
     */
    $default_config = [
        'config' => __DIR__.'/config',
        'views'  => __DIR__.'/views',
        'var'    => dirname(__DIR__).'/var',
        'debug'  => false,
    ];
    $config += $default_config;

    /*
     * build $app.
     */
    $loc = new Locator($config['config']);
    $loc->addRoot( dirname(__DIR__).'/vendor/tuum/stack/scripts');
    $loc->addRoot( dirname(__DIR__).'/vendor/tuum/view/scripts');
    $app = new App(new Container($loc));

    /*
     * set up directories
     */
    $app->set(App::CONFIG_DIR,   $config['config']);
    $app->set(App::TEMPLATE_DIR, $config['views']);
    $app->set(App::RESOURCE_DIR, $config['views']);
    $app->set(App::DEBUG,        $config['debug']);

    /*
     * set up services
     */
    $app->setRenderer( $app->get('renderer') );

    /*
     * set up handlers
     */
    $app->push($app->get('error-handler'));
    $app->push($app->get('session-handler'));
    $app->push($app->get('route-handler'));
    $app->push($app->get('url-mapper-handler'));
    $app->push($app->get('not-found-handler'));

    /*
     * set up releases
     */
    $app->push($app->get('view-release'));
    $app->push($app->get('session-release'));
    $app->push($app->get('error-release'));

    return $app;
};
