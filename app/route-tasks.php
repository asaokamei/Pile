<?php
use Tuum\View\Tuum\Renderer;
use Tuum\Web\Web;

/** @var Web $app */

$task_dir = dirname(__DIR__).'/src/Tasks';

$app->setConfigRoot($task_dir.'/scripts');

return $app->get('tasks/setup', [
    'root' => 'demoTasks*', // tasks url root name.
    'views' => $task_dir.'/views',  // set view dir for task, or set to null to use current views.
]);
    