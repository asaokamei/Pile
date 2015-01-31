<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Tuum\Web\App;
use Tuum\Web\Web;

/** @var Web $app */
$var_dir = $app->get(App::VAR_DATA_DIR) . '/log/app.log';
$logger = new Logger('log');
$logger->pushHandler(new StreamHandler($var_dir, Logger::DEBUG));

return $logger;