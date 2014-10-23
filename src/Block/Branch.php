<?php
namespace WScore\Pile\Block;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Tools\Tools;

class Branch implements HttpKernelInterface
{
    /**
     * @var HttpKernelInterface[]
     */
    protected $branches = [];

    /**
     * implement the handler for the request.
     *
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     * @return Response
     */
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true )
    {
        foreach ( $this->branches as $url => $handler ) {
            if( Tools::matcher( $url, $request ) ) {
                return $handler->handle( $request, $type, $catch );
            }
        }
        return null;
    }

    /**
     * @param callable|string $url
     * @param Request         $request
     * @return bool|void
     */
    protected function matcher( $url, Request $request )
    {
    }

    /**
     * @param string|\Closure $url
     * @param HttpKernelInterface $pile
     */
    public function branch( $url, $pile )
    {
        $this->branches[ $url ] = $pile;
    }
}