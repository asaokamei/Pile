<?php
use Tuum\Locator\Locator;
use Tuum\Stack\ErrorRelease;
use Tuum\Stack\ErrorRenderer;
use Tuum\Stack\NotFoundHandler;
use Tuum\Stack\ErrorHandler;
use Tuum\Stack\SessionHandler;
use Tuum\Stack\SessionRelease;
use Tuum\Stack\UrlMapper;
use Tuum\Stack\ViewRelease;
use Tuum\Web\App;
use Tuum\Web\Http\Response;
use Whoops\Handler\PrettyPageHandler;

/** @var App $app */

/* ===============
 * set up handlers
 * ===============
 */

/*
 * for debugging, use PrettyPageHandler
 * when unexpected exception is caught.
 */
if( $app->get(App::DEBUG) ) {

    $engine = new PrettyPageHandler;

} else {

    $engine = $app->get('error-renderer-service');
}
;$app->push( new ErrorHandler($engine) );


/*
 * sample session stack constructor script for locator.
 */
$app->push( new SessionHandler() );

/*
 * routing.
 */
$router = $app->get('router');
$route = \Tuum\Stack\Routes::forge($router);
$app->get('routes', ['routes' => $route->router] );
$app->push($route);

/*
 * sample session stack constructor script for locator.
 */
$loc = new Locator($app->get(App::RESOURCE_DIR));
$app->push(new UrlMapper($loc));

/*
 *
 */
$app->push(new NotFoundHandler());

/* ===============
 * set up releases
 * ===============
 */

/*
 * sample session stack constructor script for locator.
 */

$app->push(new ViewRelease($app->renderer()));

/*
 * sample session stack constructor script for locator.
 */

$app->push(new SessionRelease());

// ----------------------------------------------
/*
 * set up error templates.
 */

// error renderer
$view = new ErrorRenderer($app->renderer());

// default error template file name.
$view->default_error_file = 'errors/error';

// error template files for each error status code.
$view->error_files[Response::HTTP_FORBIDDEN] = 'errors/forbidden';
$view->error_files[Response::HTTP_NOT_FOUND] = 'errors/not-found';

$app->push(new ErrorRelease($view));

