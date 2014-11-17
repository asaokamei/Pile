<?php
namespace WScore\Pile\Frames;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Form\Builder;
use WScore\Pile\Http\View;
use WScore\Pile\Stack\ReleaseInterface;

class HtmlBuilder implements HttpKernelInterface, ReleaseInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var callable
     */
    protected $setInput;

    /**
     * @var callable
     */
    protected $setToken;

    /**
     * @param Builder $builder
     */
    public function __construct( $builder )
    {
        $this->builder = $builder;
        $this->setInput  = [ $this->builder, 'setInput' ];
        $this->setToken  = [ $this->builder, 'setToken' ];
    }

    /**
     * @param array $build_info
     * @return HtmlBuilder
     */
    public static function forge( $build_info=[] )
    {
        $builder = isset( $build_info['builder'] ) ? $build_info['builder'] : Builder::forge();
        $self = new self( $builder );

        if( isset( $build_info[ 'set_input' ] ) ) {
            $self->setInput = $build_info[ 'set_input' ];
        }
        if( isset( $build_info[ 'set_token' ] ) ) {
            $self->setToken = $build_info[ 'set_token' ];
        }
        return $self;
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
        if( is_callable( $this->setToken ) ) {
            // generate CSRF token
            $token = hash( 'sha512', uniqid( '', true ) . time() );
            $this->request->attributes->set( 'token', $token );
            call_user_func( $this->setToken, $token );
        }

        if( is_callable( $this->setInput ) ) {
            // get old input from bag.
            $input = $this->request->attributes->get( 'input' );
            call_user_func( $this->setInput, $input );
        }
        $this->request->attributes->set( 'FormBuilders', [
            'form' => $this->builder,
        ] );
    }
}