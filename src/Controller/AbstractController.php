<?php
namespace WScore\Pile\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WScore\Pile\App;
use WScore\Pile\Http\Responder;

abstract class AbstractController implements ForgedControllerInterface
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Responder
     */
    protected $respond;

    /**
     * override this method!
     *
     * @return Mixed|ForgedControllerInterface
     */
    public static function forge()
    {
        return new static();
    }

    /**
     * @param Request $request
     * @param         $type
     * @param         $catch
     * @return Response|null
     */
    public static function call( Request $request, $type, $catch )
    {
        $controller          = static::forge();
        $controller->app     = App::reveal( $request );
        $controller->request = $request;
        $controller->respond = $controller->app->respond();

        return $controller->callInvoke( $request );
    }

    /**
     * @param Request $request
     * @return Response
     */
    protected function callInvoke( $request )
    {
        $method = 'on' . ucwords( $request->getMethod() );
        return $this->$method();
    }
}
