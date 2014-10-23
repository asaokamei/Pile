<?php
namespace WScore\Pile;

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
class Pile implements HttpKernelInterface
{
    /**
     * @var HttpKernelInterface|PileInterface
     */
    protected $handler;

    /**
     * @var Pile
     */
    protected $pile;

    /**
     * constructs a pile, which contains a http handler object.
     * handles the request by invoking the handler's handle method,
     * and may process the response if the handler is a PileInterface object.
     *
     * @param HttpKernelInterface|PileInterface $handler
     */
    public function __construct( $handler )
    {
        $this->handler = $handler;
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
        if( !$response && $pile = $this->next() ) {
            $response = $pile->handle( $request );
        }
        // process the response if PileInterface is implemented.
        if( $this->handler instanceof PileInterface ) {
            $response = $this->handler->handled( $response );
        }
        return $response;
    }

    /**
     * make a dumb and simple one-way linked list.
     *
     * @param HttpKernelInterface|PileInterface $handler
     * @return $this
     */
    public function push( $handler )
    {
        if( $this->pile ) {
            return $this->pile->push( $handler );
        }
        $this->setPile( $handler );
        return $this;
    }

    /**
     * set a next pile, forcefully.
     *
     * @param HttpKernelInterface|PileInterface $handler
     * @return Pile
     */
    protected function setPile( $handler )
    {
        if( !$handler instanceof Pile ) {
            $handler = new self( $handler );
        }
        return $this->pile = $handler;
    }

    /**
     * get the next pile of handler.
     *
     * @return Pile
     */
    protected function next()
    {
        return $this->pile;
    }
}