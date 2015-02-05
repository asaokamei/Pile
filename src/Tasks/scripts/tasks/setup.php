<?php

use Demo\Tasks\TaskApp;
use Tuum\Web\Stack\Dispatcher;

$taskStack = new TaskApp(new Dispatcher($app), 'Demo\Tasks\TaskController');
if(isset($root)) {
    $taskStack->setRoot($root);
}
return $taskStack;