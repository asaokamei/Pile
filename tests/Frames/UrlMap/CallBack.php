<?php
namespace tests\Frames\UrlMap;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WScore\Pile\Controller\ControllerInterface;

class CallBack implements ControllerInterface
{

    /**
     * @return static|$this
     */
    public static function forge()
    {
    }

    /**
     * @param Request $request
     * @param         $type
     * @param         $catch
     * @return Response
     */
    public static function call( Request $request, $type, $catch )
    {
        return 'callback';
    }
}