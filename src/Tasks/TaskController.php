<?php
namespace Demo\Tasks;

use Tuum\Web\App;
use Tuum\Web\Controller\AbstractController;
use Tuum\Web\Controller\RouteDispatchTrait;
use Tuum\Web\Psr7\Response;
use Tuum\Web\Web;

class TaskController extends AbstractController
{
    use RouteDispatchTrait;

    /**
     * @var TaskDao
     */
    protected $dao;

    /**
     * @param TaskDao $dao
     */
    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    /**
     * @param Web $app
     * @return TaskController
     */
    public static function forge($app)
    {
        $dao  = $app->get('tasks/task-dao');
        return new self($dao);
    }
    
    /**
     * @return array
     */
    protected function getRoutes()
    {
        return [
            'get:/demoTasks'             => 'index',
            'post:/demoTasks/initialize'  => 'init',
            'post:/demoTasks/{id}'        => 'update',
            'post:/demoTasks/{id}/toggle' => 'toggle',
            'post:/demoTasks/{id}/delete' => 'delete',
        ];
    }

    /**
     * @return Response
     */
    public function onIndex()
    {
        return $this->respond->asView('tasks/index');
    }
}