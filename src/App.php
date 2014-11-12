<?php
namespace WScore\Pile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Http\Responder;
use WScore\Pile\Http\UrlGenerator;
use WScore\Pile\Piles\Bag;
use WScore\Pile\Stack\Stackable;

/**
 * Class App
 * @package WScore\Pile
 *
 * its just another Pile without the initial handler.
 *
 * @property Responder $responder
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
    protected $bags = [ ];

    /**
     * @var Stackable
     */
    protected $stack;

    /**
     * @var array
     */
    protected $filter = [ ];

    /**
     * various services.
     *
     * @var array
     */
    protected $services = [ ];

    // +----------------------------------------------------------------------+
    //  static methods
    // +----------------------------------------------------------------------+
    /**
     *
     */
    protected function __construct()
    {
    }

    /**
     * @return App
     */
    public static function start()
    {
        $app = new static();
        $app->register( 'respond', new Responder() );
        $app->register( 'url', new UrlGenerator( null ) );
        return static::$app = $app;
    }

    /**
     * @param $app
     */
    public static function setInstance( $app )
    {
        static::$app = $app;
    }

    /**
     * @param Request $request
     * @return App
     */
    public static function reveal( $request )
    {
        return $request->attributes->get( App::KEY );
    }

    /**
     * @param string $name
     * @param mixed  $service
     * @return $this
     */
    public function register( $name, $service )
    {
        $this->services[ $name ] = $service;
        return $this;
    }

    /**
     * @param $key
     * @return Responder
     */
    public function respond()
    {
        return $this->services[ 'respond' ];
    }

    /**
     * @param null $path
     * @return UrlGenerator
     */
    public function url( $path = null )
    {
        $url = $this->services[ 'url' ];
        if ( !is_null( $path ) ) {
            return $url( $path );
        }
        return $url;
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
        if ( $this->stack ) {
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
    public function handle( $request = null, $type = HttpKernelInterface::MASTER_REQUEST, $catch = false )
    {
        if ( !$request ) $request = Request::createFromGlobals();
        $this->setupRequest( $request );
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
        $this->url()->setRequest( $request );
        $responder = $this->respond();
        $responder->setRequest( $request );
        $request->attributes->set( App::KEY, $this );
    }

    // +----------------------------------------------------------------------+
    //  bags. publish and subscribe bags.
    // +----------------------------------------------------------------------+
    /**
     * @param string $name
     * @return Bag
     */
    public function bag( $name )
    {
        if ( !isset( $this->bags[ $name ] ) ) {
            $this->bags[ $name ] = new Bag();
        }
        return $this->bags[ $name ];
    }

    /**
     * @param $name
     * @return Bag
     */
    public static function getBag( $name )
    {
        return static::$app->bag( $name );
    }

    /**
     * @param string $name
     * @param array  $content
     * @param bool   $overwrite
     */
    public function pub( $name, $content, $overwrite = false )
    {
        if ( !isset( $this->bags[ $name ] ) || $overwrite ) {
            $this->bags[ $name ] = $content;
        }
    }

    /**
     * @param string $name
     * @return null|mixed
     */
    public function sub( $name )
    {
        if ( isset( $this->bags[ $name ] ) ) {
            return $this->bags[ $name ];
        }
        return null;
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
        $this->filter[ $name ] = $filter;
        return $this;
    }

    /**
     * @param string  $name
     * @param Request $request
     * @return null|Request
     */
    public function filter( $name, $request )
    {
        if ( !isset( $this->filter[ $name ] ) ) return null;
        if ( $name instanceof HttpKernelInterface ) return $name->handle( $request );
        if ( $name instanceof \Closure ) return $name( $request );
        return null;
    }

}