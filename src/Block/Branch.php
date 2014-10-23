<?php
namespace WScore\Pile\Block;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Pile;
use WScore\Pile\Tools\Tools;

/**
 * Class Branch
 * @package WScore\Pile\Block
 *
 * for a branching a pile list. this class itself is a pile object.
 *
 */
class Branch extends Pile
{
    /**
     * @var HttpKernelInterface[]
     */
    protected $branches = [];

    /**
     * @var Request
     */
    protected $request;

    /**
     * implement the handler for the request.
     *
     * invoke the next pile based on the branches' url and request.
     *
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     * @return Response
     */
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true )
    {
        foreach ( $this->branches as $url => $handler ) {
            if( Tools::matcher( $url, $this->request ) ) {
                $this->setPile( $handler );
            }
        }
        if( $pile = $this->next() ) {
            return $pile->handle( $request );
        }
        return null;
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