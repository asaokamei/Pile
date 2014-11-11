<?php
namespace WScore\Pile\Frames;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Http\View;

class CsRf implements HttpKernelInterface
{
    /**
     * @var \Closure|Response
     */
    protected $response;

    /**
     * @param null $response
     */
    public function __construct( $response=null )
    {
        $this->response = $response;
    }

    /**
     * @param string||Closure|Response $response
     * @return CsRf
     */
    public static function forge( $response=null )
    {
        if( is_null( $response ) ) {
            $response = 'Failed to validate CSRF Token. ';
        }
        if( is_string( $response ) ) {
            $response = new View( $response, Response::HTTP_UNAUTHORIZED );
        }
        return new self( $response );
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param Request $request A Request instance
     * @param int     $type The type of the request
     *                          (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool    $catch Whether to catch exceptions or not
     *
     * @return Response A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     *
     * @api
     */
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true )
    {
        $session = $request->getSession();
        $token1  = $session->get( '_token' );
        $token2  = $request->request->get( '_token' );
        if( $token1 === $token2 ) return null; // Token passed.

        return $this->badTokenResponse( $request );
    }

    /**
     * @param Request $request
     * @return null|Response
     */
    private function badTokenResponse( $request )
    {
        if( $this->response instanceof Response ) {
            return $this->response;
        }
        if( $this->response instanceof \Closure ) {
            $responder = $this->response;
            return $responder( $request );
        }
        throw new \RuntimeException( 'CSRF token check failure.' );
    }
}
