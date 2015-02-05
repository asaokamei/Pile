<?php
use Tuum\Web\App;

return new \Demo\Tasks\TaskDao($app->get(App::VAR_DATA_DIR).'/data/tasks.csv');