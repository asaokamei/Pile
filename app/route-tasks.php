<?php
use Tuum\View\Tuum\Renderer;
use Tuum\Web\Web;

/** @var Web $app */

$task_dir = dirname(__DIR__).'/src/Tasks';

$app->setConfigRoot($task_dir.'/scripts');
$app->setRenderRoot($task_dir.'/views');

return $app->get('tasks/setup', ['root' => 'demoTasks*']);
    