<?php
namespace tests\App\Stackable;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ReturnOne implements HttpKernelInterface
{
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true )
    {
        $responder = new \WScore\Pile\Http\Responder( $request );
        return $responder->text(1);
    }

}
