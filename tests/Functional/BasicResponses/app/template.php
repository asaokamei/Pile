<?php
use WScore\Pile\Service\Locator;
use WScore\Pile\Service\PhpEngine;

$view   = Locator::dir( __DIR__ . '/views' );
return $engine = new PhpEngine( $view );