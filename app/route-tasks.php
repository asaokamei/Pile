<?php
use Tuum\View\Tuum\Renderer;
use Tuum\Web\App;

$task_dir = dirname(__DIR__).'/src/Tasks';
$app->setConfigRoot($task_dir.'/scripts');
/** @var Renderer $engine */
$engine = $app->get(App::RENDER_ENGINE);
$engine->locator->addRoot( $task_dir.'/views');

return $app->get('tasks/setup', ['root' => 'demoTasks*']);
    