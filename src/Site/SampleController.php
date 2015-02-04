<?php
namespace Demo\Site;

use Tuum\Web\Controller\AbstractController;
use Tuum\Web\Controller\RouteDispatchTrait;
use Tuum\Web\Psr7\Response;

class SampleController extends AbstractController
{
    use RouteDispatchTrait;

    /**
     * @return SampleController
     */
    public static function forge()
    {
        return new self;
    }

    /**
     * @return array
     */
    protected function getRoutes()
    {
        return [
            '/sample/'       => 'welcome',
            '/sample/jump'   => 'jump',
            '/sample/jumper' => 'jumper',
            '/sample/{name}' => 'hello',
        ];
    }

    /**
     * @param string $name
     * @return Response
     */
    protected function onHello($name)
    {
        return $this->respond
            ->with('name', $name )
            ->asView('sample/hello')
            ;
    }

    /**
     * @return Response
     */
    protected function onWelcome()
    {
        return $this->respond
            ->asView('sample/welcome')
            ;
    }

    /**
     * @return Response
     */
    protected function onJump()
    {
        return $this->respond
            ->asView('sample/jump')
            ;
    }

    /**
     * @param string $message
     * @return Response
     */
    protected function onJumper($message='jumped')
    {
        return $this->respond
            ->withMessage($message)
            ->asRedirectUri('/sample/jump')
            ;
    }
}