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
            'post:/'            => 'insert',
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
     * create 5 initial tasks.
     *
     * @return Response
     */
    public function onInit()
    {
        $this->dao->initialize();
        return $this->respond
            ->withMessage('initialized tasks.')
            ->asPath($this->basePath);
    }

    /**
     * toggle status between ACTIVE <-> DONE.
     *
     * @param $id
     * @return Response
     */
    public function onToggle($id)
    {
        if($this->dao->toggle($id)) {
            return $this->respond
                ->withMessage('toggled task #'.$id)
                ->asPath($this->basePath);
        }
        return $this->respond
            ->withErrorMessage('cannot find task #'.$id)
            ->asPath($this->basePath);
    }

    /**
     * deletes task $id.
     *
     * @param $id
     * @return Response
     */
    public function onDelete($id)
    {
        if($this->dao->delete($id)) {
            return $this->respond
            ->withMessage('deleted task #'.$id)
                ->asPath($this->basePath);
        }
        return $this->respond
            ->withErrorMessage('cannot find task #'.$id)
            ->asPath($this->basePath);
    }

    /**
     * insert a new task.
     *
     * @return Response
     */
    public function onInsert()
    {
        $input = $this->request->getBodyParams('task');
        if(!isset($input['task']) || !$input['task']) {
            return $this->respond
                ->withErrorMessage('please write a task to accomplish!')
                ->asPath($this->basePath);
        }
        if(!$id = $this->dao->insert($input['task'])) {
            return $this->respond
                ->withErrorMessage('cannot add a new task, yet!')
                ->asPath($this->basePath);
        }
        return $this->respond
            ->withMessage('added a new task #'.$id)
            ->asPath($this->basePath);
    }
}