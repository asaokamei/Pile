<?php
namespace Demo\Tasks;

use Tuum\Router\Route;
use Tuum\Web\ApplicationInterface;
use Tuum\Web\Middleware\BeforeFilterTrait;
use Tuum\Web\Middleware\MatchRootTrait;
use Tuum\Web\Middleware\MiddlewareTrait;
use Tuum\Web\MiddlewareInterface;
use Tuum\Web\Psr7\Request;
use Tuum\Web\Psr7\Response;
use Tuum\Web\Stack\Dispatcher;

class TaskApp implements MiddlewareInterface
{
    use MiddlewareTrait;
    
    use MatchRootTrait;

    use BeforeFilterTrait;

    /**
     * @var ApplicationInterface
     */
    protected $controller;

    /**
     * @param Dispatcher     $dispatcher
     * @param TaskController $controller
     */
    public function __construct($dispatcher, $controller)
    {
        $this->dispatcher = $dispatcher;
        $this->controller = $controller;
    }
    
    /**
     * @param Request $request
     * @return Response|null
     */
    public function __invoke($request)
    {
        if(!$this->isMatch($request)) {
            return null;
        }
        $this->dispatcher->setRoute(new Route(['handle' => $this->controller]));
        $app = $request->getWebApp()->cloneApp();
        $app->prepend($this->dispatcher);
        if ($this->_beforeFilters) {
            foreach($this->_beforeFilters as $filter) {
                $filter = $request->getFilter($filter);
                $app->prepend($filter);
            }
        }
        return $app($request);
    }
}