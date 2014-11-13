<?php
namespace tests\Functional\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WScore\Pile\Controller\ControllerInterface;

class CallController implements ControllerInterface
{
    /**
     * @param Request $request
     * @param         $type
     * @param         $catch
     * @return Response
     */
    public static function call( Request $request, $type, $catch )
    {
        return 'calling controller';
    }

    public function onGet()
    {
        return 'tested controller';
    }
}