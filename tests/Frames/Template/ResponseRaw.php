<?php
namespace tests\Frames\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\App;
use WScore\Pile\Http\Responder;

class ResponseRaw implements HttpKernelInterface
{
    /**
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     * @return Response|null
     */
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true )
    {
        /** @var Responder $res */
        $res = $request->attributes->get('responder');
        return $res->view( 'raw_file' )
            ->with( 'test', 'tested' );
    }
}