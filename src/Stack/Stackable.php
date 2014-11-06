<?php
namespace WScore\Pile\Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class Pile
 * @package WScore\Pile
 *
 * creates a pile of handlers for http request.
 * continues processing the request until one of the pile returns a response.
 */
class Stackable implements HttpKernelInterface, StackableInterface
{
    /**
     * the middleware. the Http Kernel that does the job. 
     * 
     * @var HttpKernelInterface
     */
    protected $handler;

    /**
     * pile of Stackable Http Kernels. 
     * 
     * @var Stackable
     */
    protected $next;

    /**
     * wraps the Http Kernel that does the job with Stackable Http Kernel. 
     *
     * @param HttpKernelInterface $handler
     */
    public function __construct( HttpKernelInterface $handler )
    {
        $this->handler = $handler;
    }

    /**
     * @param HttpKernelInterface $handler
     * @return HttpKernelInterface|static
     */
    public static function makeStack( HttpKernelInterface $handler )
    {
        if( !$handler instanceof StackableInterface ) {
            $handler = new static( $handler );
        }
        return $handler;
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * if own handler does not return a response, it handles down to the next pile.
     * if the handler is a PileInterface, the response will be process by handled method.
     *
     * @param Request $request  A Request instance
     * @param int     $type     The type of the request
     *                          (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool    $catch    Whether to catch exceptions or not
     *
     * @return Response         A Response instance
     *
     * @api
     */
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true )
    {
        // get the response from the own handler.
        $response = $this->handler->handle( $request, $type );

        // if no response, invoke the next pile of handler.
        if( !$response && $this->next ) {
            $response = $this->next->handle( $request );
        }
        // process the response if PileInterface is implemented.
        if( $this->handler instanceof ReleaseInterface ) {
            $response = $this->handler->release( $response );
        }
        return $response;
    }

    /**
     * stack up the SplStack.
     * converts normal HttpKernel into Stackable.
     *
     * @param HttpKernelInterface $handler
     * @return $this
     */
    public function push( HttpKernelInterface $handler )
    {
        if( $this->next ) {
            return $this->next->push( $handler );
        }
        $this->next = static::makeStack( $handler );
        return $this->next;
    }
}