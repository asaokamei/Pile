<?php
namespace Demo\Tasks;

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
            'get:/'             => 'index',
            'post:/initialize'  => 'init',
            'post:/{id}'        => 'update',
            'post:/{id}/toggle' => 'toggle',
            'post:/{id}/delete' => 'delete',
        ];
    }

    /**
     * @return Response
     */
    public function onIndex()
    {
        $tasks = $this->dao->getTasks();
        return $this->respond
            ->with('tasks', $tasks)
            ->asView('tasks/index');
    }

    /**
     * @return Response
     */
    public function onInit()
    {
        $this->dao->initialize();
        $basePath = $this->request->getBasePath();
        return $this->respond
            ->withMessage('initialized tasks.')
            ->asPath($basePath);
    }
}