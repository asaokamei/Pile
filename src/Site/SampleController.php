<?php
namespace Demo\Site;

use Tuum\Controller\AbstractController;
use Tuum\Controller\RouteDispatchTrait;
use Tuum\Web\Http\View;

class SampleController extends AbstractController
{
    use RouteDispatchTrait;

    /**
     * @return array
     */
    protected function getRoutes()
    {
        return [
            '/{name}' => 'hello',
            '/'       => 'welcome',
        ];
    }

    /**
     * @param string $name
     * @return View
     */
    protected function onHello($name)
    {
        return $this->respond
            ->view('sample/hello')
            ->with('name', $name )
            ;
    }

    /**
     * @return View
     */
    protected function onWelcome()
    {
        return $this->respond
            ->view('sample/welcome')
            ;
    }
}