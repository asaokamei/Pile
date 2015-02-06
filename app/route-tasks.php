<?php
use Tuum\View\Tuum\Renderer;
use Tuum\Web\Web;

/** @var Web $app */

$task_dir = dirname(__DIR__).'/src/Tasks';

$app->setConfigRoot($task_dir.'/scripts');

return $app->get('tasks/setup', [
    'root' => 'demoTasks*', // tasks url root name.
    'views' => null,        // set view dir name to use custom views.
]);
    