<?php
namespace WScore\Pile\Frame;

use League\Plates\Engine;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Http\View;
use WScore\Pile\Stack\ReleaseInterface;

class Template implements HttpKernelInterface, ReleaseInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Engine
     */
    protected $plates;

    /**
     * @param string $view
     */
    public function __construct( $view, $layout=null )
    {
        $this->plates = new Engine( $view );
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * @param Request $request A Request instance
     * @param int     $type The type of the request
     *                          (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool    $catch Whether to catch exceptions or not
     *
     * @return SymfonyResponse A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     *
     * @api
     */
    public function handle( Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true )
    {
        $this->request = $request;
    }

    /**
     * @param SymfonyResponse $response
     * @return SymfonyResponse
     */
    public function release( $response )
    {
        if ( $response && $response instanceof View ) {
            return $this->render( $response );
        }
        return $response;
    }

    /**
     * @param View $response
     * @return SymfonyResponse
     */
    protected function render( $response )
    {
        $app   = $this->request->attributes->get( 'app' );
        $file  = $response->getFile();
        $plate = $this->plates->make( $file );
        $data  = $response->getData() +
            [
                'message' => $app->sub( 'message' ),
                'errors'  => $app->sub( 'errors' ),
                '_token'  => $app->sub( 'token' ),
            ];
        $plate->data( $data );
        $response->setContent( $plate->render() );
        return $response;
    }
}