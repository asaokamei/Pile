<?php
namespace WScore\Pile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Http\Responder;
use WScore\Pile\Service\Locator;
use WScore\Pile\Service\LocatorInterface;
use WScore\Pile\Service\UrlGenerator;
use WScore\Pile\Stack\Stack;

/**
 * Class App
 * @package WScore\Pile
 *
 * its just another Pile without the initial handler.
 *
 * @method Responder respond()
 * @method Request   request()
 * @method LocatorInterface config()
 */
class App
{
    const KEY = 'app';

    /**
     * @var Stack
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
     * starts an app with services.
     *
     * sets respond (Responder), url (UrlGenerator).
     * sets config (Locator) if $dir a config directory is given.
     *
     * @param null|string $dir
     * @return App
     */
    public static function start( $dir = null )
    {
        $app = new static();
        $app->register( 'respond', new Responder( $app, 'errors' ) );
        $app->register( 'url', new UrlGenerator( $app ) );
        if ( $dir ) {
            $locator = Locator::dir( $dir, [ 'app'=>$app ] );
            if ( file_exists( $env_file = $dir . '/.env.php' ) ) {
                $environment = include $env_file;
                $locator->addRoot( $dir . '/' . $environment );
            }
            $app->register( 'config', $locator );
        }
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
        return array_key_exists( $name, $this->services ) ? $this->services[ $name ] : null;
    }

    /**
     * @return \WScore\Pile\Service\UrlGenerator
     */
    public function url()
    {
        /** @var \WScore\Pile\Service\UrlGenerator $url */
        if ( $url = $this->service( 'url' ) ) {
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
     * @return Stack
     */
    public function push( $stack )
    {
        if ( $this->stack ) {
            return $this->stack->push( $stack );
        }
        $this->stack = Stack::makeStack( $stack );
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
        $this->register( 'request', $request );
        $request->attributes->set( App::KEY, $this );
        return $this->stack->handle( $request, $type, $catch );
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