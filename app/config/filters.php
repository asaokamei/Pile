<?php

use Tuum\Web\App;
use Tuum\Web\Web;

/** @var Web $app */

/*
 * set up services and filters.
 */

// services
$app->set(App::LOGGER, $app->get('service/logger'));
$app->set(App::RENDER_ENGINE, $app->get('service/renderer') );

// filters
$app->set(App::CS_RF_FILTER, $app->get('filter/csrf') );
