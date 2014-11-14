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
 * @method Responder respond()
 * @method Request   request()
 */
class App
{
    const KEY = 'app';

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
        $app->register( 'url', new UrlGenerator() );
        return $app;
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
     * @param $name
     * @return null|mixed
     */
    public function service( $name )
    {
        return array_key_exists( $name, $this->services ) ? $this->services[$name] : null;
    }

    /**
     * @return UrlGenerator
     */
    public function url()
    {
        /** @var UrlGenerator $url */
        if( $url = $this->service( 'url' ) ) {
            return $url();
        }
        return $url;
    }

    /**
     * @param string $name
     * @param array  $args
     * @return null|mixed
     */
    public function __call( $name, $args )
    {
        return $this->service( $name );
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
        // set up UrlGenerator.
        /** @noinspection PhpUndefinedMethodInspection */
        $this->services['url']->setRequest( $request );

        // set up Responder.
        $this->respond()->setRequest( $request );

        // save $app itself to the $request.
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
     * @param string $name
     * @param array  $values
     */
    public function deco( $name, $values )
    {
        $this->bag($name)->fill($values);
    }

    /**
     * @param string $name
     * @param string|array  $content
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
            $bag = $this->bags[ $name ];
            if( $bag instanceof Bag ) {
                return $bag->all();
            }
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