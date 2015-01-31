<?php
use Tuum\Locator\Container;
use Tuum\Locator\Locator;
use Tuum\Web\App;
use Tuum\Web\Web;

return function( array $config ) {

    /*
     * set up default configuration.
     */
    $default_config = [
        'routes' => __DIR__.'/routes.php',
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
    $app = new Web(new Container($loc));

    /*
     * set up directories
     */
    $app->set(App::CONFIG_DIR,   $config['config']);
    $app->set(App::TEMPLATE_DIR, $config['views']);
    $app->set(App::RESOURCE_DIR, $config['views']);
    $app->set(App::VAR_DATA_DIR, $config['var']);
    $app->set(App::DEBUG,        $config['debug']);

    /*
     * set up services
     */
    $app->set(App::LOGGER, $app->get('logger'));
    $app->set(App::ROUTER, $app->get('router'));
    $app->set(App::RENDER_ENGINE, $app->get('renderer') );

    /*
     * set up handlers
     */
    $stacks = $app->get('stacks');
    foreach($stacks as $stack) {
        $app->push($app->get($stack));
        
    }

    /*
     * read the routes.
     * the routes must match with the $router class above.
     */
    $route_files = (array) $config['routes'];
    foreach($route_files as $routes ) {
        /** @noinspection PhpIncludeInspection */
        $app->push(include($routes));
    }

    return $app;
};
