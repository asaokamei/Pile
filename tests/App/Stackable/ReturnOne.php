<?php
namespace tests\App\Stackable;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ReturnOne implements HttpKernelInterface
{
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true )
    {
        $responder = $request->attributes->get('responder');
        return $responder->text(1);
    }

}
