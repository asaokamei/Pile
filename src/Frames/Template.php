<?php
namespace WScore\Pile\Frames;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\App;
use WScore\Pile\Http\Responder;
use WScore\Pile\Http\View;
use WScore\Pile\Service\PhpEngine;
use WScore\Pile\Service\TemplateInterface;
use WScore\Pile\Stack\ReleaseInterface;

class Template implements HttpKernelInterface, ReleaseInterface
{
    /**
     * @var App
     */
    protected $app;
    
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var TemplateInterface
     */
    protected $engine;

    /**
     * @param App               $app
     * @param TemplateInterface $engine
     */
    public function __construct( $app, $engine )
    {
        $this->app = $app;
        $this->engine = $engine;
    }

    /**
     * @param App    $app
     * @param string $dir
     * @return Template
     */
    public static function forge( $app, $dir )
    {
        return new self( $app, new PhpEngine( $dir ) );
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
            $response = $this->app->respond()->notFound();
        }
        if ( $response instanceof View ) {
            return $this->setContents( $response );
        }
        if ( is_string( $response ) ) {
            /** @var Responder $res */
            return $response = $this->app->respond()->text( $response );
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
        $messages = $app->sub( 'messages' ) ?: [];
        $errors = $app->sub( 'errors' ) ?: [];
        $input = $app->sub( 'input' ) ?: [];
        $data = $response->getData() + $messages + $errors + $input;
        $this->engine->register( 'url', $app->url() );
        $response->setContent( $this->engine->render( $file, $data ) );
        return $response;
    }
}