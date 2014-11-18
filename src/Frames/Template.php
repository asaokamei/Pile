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
     * @var callable
     */
    protected $renderer;

    /**
     * @param App                        $app
     * @param TemplateInterface|callable $engine
     */
    public function __construct( $app, $engine )
    {
        $this->app = $app;
        if ( $engine instanceof TemplateInterface ) {

            $this->engine   = $engine;
            $this->renderer = [ $this, 'renderer' ];

        } elseif ( is_callable( $engine ) ) {
            $this->renderer = $engine;
        }
    }

    /**
     * @param App                               $app
     * @param string|TemplateInterface|\Closure $engine
     * @return Template
     */
    public static function forge( $app, $engine )
    {
        if ( is_string( $engine ) ) {
            $engine = new PhpEngine( $engine );
        }
        $self = new self( $app, $engine );
        return $self;
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
        if ( !$response ) {
            $response = $this->app->respond()->notFound();
        }
        if ( $response instanceof View ) {
            return $this->setContents( $response );
        }
        if ( is_string( $response ) ) {
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
        $renderer = $this->renderer;
        $file     = $response->getFile();
        $data     = $response->getData();

        $content = $renderer( $file, $data, $this->request, $this->app );
        $response->setContent( $content );
        return $response;
    }

    /**
     * @param string  $file
     * @param array   $data
     * @param Request $request
     * @return string
     */
    protected function renderer( $file, $data, $request )
    {
        $messages = $request->attributes->get( 'messages', [ ] );
        $errors   = $request->attributes->get( 'errors', [ ] );
        $input    = $request->attributes->get( 'input', [ ] );
        $data     = $data + $messages + $errors + $input;

        $this->engine->register( 'url', $this->app->url() );
        return $this->engine->render( $file, $data );
    }
}