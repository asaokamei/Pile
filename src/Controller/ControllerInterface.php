<?php
namespace WScore\Pile\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ControllerInterface
{
    /**
     * @param Request $request
     * @param         $type
     * @param         $catch
     * @return Response
     */
    public static function call( Request $request, $type, $catch );
}