<?php
namespace WScore\Pile\Frames;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Form\Builder;
use WScore\Pile\App;
use WScore\Pile\Http\View;
use WScore\Pile\Stack\ReleaseInterface;

class HtmlBuilder implements HttpKernelInterface, ReleaseInterface
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
     * @var Builder
     */
    protected $builder;

    /**
     * @param App     $app
     * @param Builder $builder
     */
    public function __construct( $app, $builder )
    {
        $this->app = $app;
        $this->builder = $builder;
    }

    /**
     * @param App $app
     * @return HtmlBuilder
     */
    public static function forge( $app )
    {
        return new self( $app, Builder::forge() );
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
        $this->request = $request;
    }

    /**
     * @param Response $response
     * @return Response
     */
    public function release( $response )
    {
        if ( $response && $response instanceof View ) {
            $this->setContents();
        }
        return $response;
    }

    /**
     *
     */
    protected function setContents()
    {
        // generate CSRF token
        $token = hash( 'sha512', uniqid( '', true ) . time() );
        $this->app->pub( 'token', $token );
        $this->builder->setToken( $token );

        // get old input from bag.
        $input = $this->app->sub( 'input' );
        $this->builder->setInput( $input );
        $this->app->pub( 'FormBuilders', [
            'form' => $this->builder,
        ] );
    }
}