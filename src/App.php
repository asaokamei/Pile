<?php
namespace WScore\Pile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Http\Responder;
use WScore\Pile\Piles\Bag;
use WScore\Pile\Stack\Stackable;

/**
 * @param Request $request
 * @return App
 */
function App( $request )
{
    return $request->attributes->get( App::KEY );
}


/**
 * Class App
 * @package WScore\Pile
 *
 * its just another Pile without the initial handler.
 */
class App
{
    const KEY = 'app';

    /**
     * @var App
     */
    static $app;

    /**
     * @var Bag[]
     */
    protected $bags = [];

    /**
     * @var Stackable
     */
    protected $stack;

    /**
     * @var Responder
     */
    protected $responder;

    /**
     * @var array
     */
    protected $filter = [];

    // +----------------------------------------------------------------------+
    //  static methods
    // +----------------------------------------------------------------------+
    /**
     * @param Responder    $responder
     */
    public function __construct( $responder )
    {
        $this->responder = $responder;
    }

    /**
     * @return App
     */
    public static function start()
    {
        $res  = new Responder();
        static::$app = new static( $res );
        return static::$app;
    }

    /**
     * @param $app
     */
    public static function setInstance($app)
    {
        static::$app = $app;
    }

    // +----------------------------------------------------------------------+
    //  managing instance and stacks
    // +----------------------------------------------------------------------+
    /**
     * @param HttpKernelInterface $stack
     * @return Stackable
     */
    public function push( $stack )
    {
        if( $this->stack ) {
            return $this->stack->push( $stack );
        }
        $this->stack = stackable::makeStack( $stack );
        return $this->stack;
    }

    /**
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     * @return Response
     */
    public function handle( $request=null, $type=HttpKernelInterface::MASTER_REQUEST, $catch=false )
    {
        if( !$request ) $request = Request::createFromGlobals();
        $this->setupRequest($request);
        return $this->stack->handle( $request, $type, $catch );
    }

    /**
     * a hidden/protected method that sets up request's attribute.
     * default is to set
     *  - app: the app itself, and
     *  - responder: factory for response object.
     *
     * @param $request
     */
    protected function setupRequest( $request )
    {
        $this->responder->setRequest($request);
        $request->attributes->set( App::KEY, $this );
        $request->attributes->set( 'responder', $this->responder );
    }

    // +----------------------------------------------------------------------+
    //  bags. publish and subscribe bags.
    // +----------------------------------------------------------------------+
    /**
     * @param string $name
     * @return Bag
     */
    public function bag($name)
    {
        if( !isset( $this->bags[$name] ) ) {
            $this->bags[$name] = new Bag();
        }
        return $this->bags[$name];
    }

    /**
     * @param $name
     * @return Bag
     */
    public static function getBag($name)
    {
        return static::$app->bag($name);
    }

    /**
     * @param string $name
     * @param array  $content
     * @param bool   $overwrite
     */
    public function pub( $name, $content, $overwrite=false )
    {
        if( !isset( $this->bags[$name] ) || $overwrite ) {
            $this->bags[$name] = $content;
        }
    }

    /**
     * @param string $name
     * @return array
     */
    public function sub($name)
    {
        if( isset( $this->bags[$name] ) ) {
            return $this->bags[$name];
        }
        return [];
    }

    // +----------------------------------------------------------------------+
    //  filters
    // +----------------------------------------------------------------------+
    /**
     * @param string                       $name
     * @param \Closure|HttpKernelInterface $filter
     * @return $this
     */
    public function setFilter( $name, $filter )
    {
        $this->filter[$name] = $filter;
        return $this;
    }

    /**
     * @param string  $name
     * @param Request $request
     * @return null|Request
     */
    public function filter( $name, $request )
    {
        if( !isset( $this->filter[$name] ) ) return null;
        if( $name instanceof HttpKernelInterface ) return $name->handle( $request );
        if( $name instanceof \Closure ) return $name( $request );
        return null;
    }

}