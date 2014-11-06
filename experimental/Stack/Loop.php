<?php
namespace WScore\Pile\Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Handler\ReleaseInterface;

/**
 * Class Loop
 * @package WScore\Pile\Stack
 *
 * an experimental class to loop through the stack,
 * instead of nesting the call like Stackable.
 *
 * NOT TESTED!
 */
class Loop implements HttpKernelInterface
{
    /**
     * @var \SplStack
     */
    protected $stack;

    /**
     * 
     */
    public function __construct()
    {
        $this->stack = new \SplStack();
    }

    /**
     * @param HttpKernelInterface $handler
     */
    public function push( $handler )
    {
        $this->stack->push($handler);
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * loops through the Http Kernels for response, 
     * and revert back the loop for ReleaseInterface. 
     * 
     * @param Request $request  A Request instance
     * @param int     $type     The type of the request
     *                          (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool    $catch    Whether to catch exceptions or not
     *
     * @return Response A Response instance
     *
     */
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true )
    {
        // loop through Http Kernels.
        $this->stack->rewind();
        $response = null;
        /** @var HttpKernelInterface $handler */
        while( $handler = $this->stack->current() ) {
            if( $response = $handler->handle( $request, $type, $catch ) ) {
                break;
            }
            $this->stack->next();
        }
        // loop back the kernels for Release. 
        /** @var ReleaseInterface $handler */
        $this->stack->prev();
        while( $handler = $this->stack->current() ) {
            if( $handler instanceof ReleaseInterface ) {
                $response = $handler->release( $response );
            }
            $this->stack->prev();
        }
        return $response;
    }
}