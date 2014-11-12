<?php
namespace WScore\Pile\Frames;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\App;
use WScore\Pile\Http\Responder;
use WScore\Pile\Http\View;
use WScore\Pile\Piles\PhpEngine;
use WScore\Pile\Stack\ReleaseInterface;

class Template implements HttpKernelInterface, ReleaseInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var TemplateInterface
     */
    protected $engine;

    /**
     * @param TemplateInterface $engine
     */
    public function __construct( $engine )
    {
        $this->engine = $engine;
    }

    /**
     * @param string $dir
     * @return Template
     */
    public static function forge( $dir )
    {
        return new self( new PhpEngine( $dir ) );
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * @param Request $request  A Request instance
     * @param int     $type     The type of the request
     *                          (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool    $catch    Whether to catch exceptions or not
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
        if ( !$response ) {
            /** @var Responder $res */
            $res      = $this->request->attributes->get( 'responder' );
            $response = $res->notFound();
        }
        if ( $response instanceof View ) {
            return $this->setContents( $response );
        }
        if ( is_string( $response ) ) {
            /** @var Responder $res */
            $res = App::reveal( $this->request )->respond();
            return $response = $res->text( $response );
        }
        return $response;
    }

    /**
     * @param View $response
     * @return SymfonyResponse
     */
    protected function setContents( $response )
    {
        $app  = $this->request->attributes->get( 'app' );
        $file = $response->getFile();
        $data = $response->getData() +
            [
                'message' => $app->sub( 'message' ),
                'errors'  => $app->sub( 'errors' ),
            ];
        $this->engine->register( 'url', $app->url() );
        $response->setContent( $this->engine->render( $file, $data ) );
        return $response;
    }
}