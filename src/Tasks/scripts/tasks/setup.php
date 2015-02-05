<?php

use Demo\Tasks\TaskStack;
use Tuum\Web\Stack\Dispatcher;

$taskStack = new TaskStack(new Dispatcher($app), 'Demo\Tasks\TaskController');
if(isset($root)) {
    $taskStack->setRoot($root);
}
return $taskStack;