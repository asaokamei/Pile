<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2014/10/22
 * Time: 17:57
 */
namespace WScore\Pile\Stack;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

interface ReleaseInterface extends HttpKernelInterface
{
    /**
     * @param Response $response
     * @return Response
     */
    public function release( $response );
}