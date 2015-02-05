<?php
use Tuum\Locator\Container;
use Tuum\Locator\Locator;
use Tuum\Web\App;
use Tuum\Web\Web;

return function( array $config ) {

    /* ---------------------
     * default configuration
     */
    $default_config = [
        
        // default routes file to set up router.
        'routes' => __DIR__.'/routes.php',
        
        // default config directory. 
        'config' => __DIR__.'/config',
        
        // default view/template directory.
        'views'  => __DIR__.'/views',
        
        // default document/resource directory.
        'docs'   => __DIR__.'/views',
        
        // default var (cache, logs, etc.) directory.
        'var'    => dirname(__DIR__).'/var',
        
        // default debug is off. 
        'debug'  => false,
    ];
    $config += $default_config;

    /* ----------
     * build $app
     */
    $loc = new Locator($config['config']);
    $loc->addRoot( dirname(__DIR__).'/vendor/tuum/web/scripts');
    $loc->addRoot( dirname(__DIR__).'/vendor/tuum/view/scripts');
    $app = new Web(new Container($loc));

    /* ------------------
     * set up directories
     */
    $app->set(App::CONFIG_DIR,   $config['config']);
    $app->set(App::TEMPLATE_DIR, $config['views']);
    $app->set(App::DOCUMENT_DIR, $config['docs']);
    $app->set(App::VAR_DATA_DIR, $config['var']);
    $app->set(App::DEBUG,        $config['debug']);

    /* ---------------
     * set up services
     */
    $app->set(App::LOGGER, $app->get('logger'));
    $app->set(App::ROUTER, $app->get('router'));
    $app->set(App::RENDER_ENGINE, $app->get('renderer') );

    /* -------------
     * set up stacks
     */
    $stacks = $app->get('stacks');
    foreach($stacks as $stack) {
        $app->push($app->get($stack));
    }

    /* ---------------
     * read the routes.
     * the route files are out of the config directory. 
     */
    $route_files = (array) $config['routes'];
    foreach($route_files as $routes ) {
        $app->push($app->execute($routes));
    }

    return $app;
};
