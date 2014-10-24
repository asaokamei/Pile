<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2014/10/22
 * Time: 17:57
 */
namespace WScore\Pile\Handler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

interface ResponseHandleInterface extends HttpKernelInterface
{
    /**
     * @param Response $response
     * @return Response
     */
    public function handled( $response );
}