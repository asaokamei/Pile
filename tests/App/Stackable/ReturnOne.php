<?php
namespace tests\App\Stackable;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\App;

class ReturnOne implements HttpKernelInterface
{
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true )
    {
        $responder = App::reveal($request)->respond();
        return $responder->text(1);
    }

}
