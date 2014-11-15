<?php
namespace WScore\Pile\Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\App;

/**
 * Class Pile
 * @package WScore\Pile
 *
 * creates a pile of handlers for http request.
 * continues processing the request until one of the pile returns a response.
 */
class Stack implements HttpKernelInterface, StackableInterface
{
    /**
     * the middleware. the Http Kernel that does the job. 
     * 
     * @var HttpKernelInterface
     */
    protected $middleware;

    /**
     * pile of Stackable Http Kernels. 
     * 
     * @var Stack
     */
    protected $next;

    /**
     * @var array
     */
    protected $roots = [];

    /**
     * @var array
     */
    protected $beforeFilters = [];

    /**
     * wraps the Http Kernel that does the job with Stackable Http Kernel. 
     *
     * @param HttpKernelInterface $middleware
     */
    public function __construct( HttpKernelInterface $middleware )
    {
        $this->middleware = $middleware;
    }

    /**
     * sets root to invoke the middleware
     *
     * @param string $root
     * @return $this
     */
    public function match( $root ) 
    {
        $args = func_get_args();
        $this->roots[] = array_merge( $this->roots, $args );
        return $this;
    }

    /**
     * @param string|\Closure $filter
     * @return $this
     */
    public function before( $filter )
    {
        $this->beforeFilters[] = $filter;
        return $this;
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function isMatch( $request )
    {
        if( empty( $this->roots ) ) return true;
        $pathInfo = rawurldecode( $request->getPathInfo() );
        foreach ( $this->roots as $root ) {
            if ( ( $pos = strpos( $pathInfo, $root ) ) === 0 ) return true;
        }
        return false;
    }

    /**
     * @param array   $filter_list
     * @param Request $request
     * @return Response|null
     */
    protected function applyFilters( $filter_list, $request )
    {
        $app = App::reveal( $request );
        $response = null;
        foreach( $filter_list as $filter ) {
            if( is_string( $filter ) ) {
                $response = $app->filter( $filter, $request );
            }
            elseif( $filter instanceof \Closure ) {
                $response = $filter( $request );
            }
            if( $response ) return $response;
        }
        return null;
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
        if( !$this->isMatch( $request ) ) {
            if( $this->next ) {
                return $this->next->handle( $request, $type, $catch );
            }
            return null;
        }
        if( $response = $this->applyFilters( $this->beforeFilters, $request ) ) {
            return $response;
        }
        return $this->_handle( $request, $type, $catch );
    }

    /**
     * @param Request $request
     * @param int     $type
     * @param Bool    $catch
     * @return Response
     */
    public function _handle( Request $request, $type, $catch )
    {
        // get the response from the own handler.
        $response = $this->middleware->handle( $request, $type );

        // if no response, invoke the next pile of handler.
        if ( !$response && $this->next ) {
            $response = $this->next->handle( $request, $type, $catch );
        }
        // process the response if PileInterface is implemented.
        if ( $this->middleware instanceof ReleaseInterface ) {
            $response = $this->middleware->release( $response );
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