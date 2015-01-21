<?php

use Tuum\Web\App;

/** @var App $app */

/*
 * build a router. pick the one you like.
 */
// $router = Tuum\Router\Aura\Router::forge();

return Tuum\Router\PhRouter\Router::forge();
