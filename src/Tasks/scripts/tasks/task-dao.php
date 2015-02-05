<?php
use Tuum\Web\App;

new \Demo\Tasks\TaskDao($app->get(App::VAR_DATA_DIR).'/data/tasks.csv');