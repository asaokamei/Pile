<?php
namespace WScore\Pile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Http\Responder;
use WScore\Pile\Piles\Bag;
use WScore\Pile\Piles\UnionManager;
use WScore\Pile\Stack\Stackable;

/**
 * @param Request $request
 * @return App
 */
function App( $request )
{
    $request->attributes->get( App::KEY );
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
     * @var UnionManager
     */
    protected $config;

    /**
     * @var Stackable
     */
    protected $stack;

    /**
     * @var Responder
     */
    protected $responder;

    // +----------------------------------------------------------------------+
    //  static methods
    // +----------------------------------------------------------------------+
    /**
     * @param UnionManager $file
     */
    public function __construct( $file, $responder )
    {
        $this->config = $file;
        $this->responder = $responder;
    }

    /**
     * @return App
     */
    public static function start()
    {
        $file = new UnionManager();
        $res  = new Responder();
        static::$app = new static( $file, $res );
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
     */
    public function pub( $name, $content )
    {
        if( isset( $this->bags[$name] ) ) {
            throw new \BadMethodCallException("bag \"{$name}\" already published.");
        }
        $this->bags[$name] = new Bag();
        $this->bags[$name]->fill($content);
    }

    /**
     * @param string $name
     * @return array
     */
    public function sub($name)
    {
        if( isset( $this->bags[$name] ) ) {
            return $this->bags[$name]->all();
        }
        return [];
    }

    // +----------------------------------------------------------------------+
    //  configurations
    // +----------------------------------------------------------------------+
    /**
     * @param string $root
     * @return $this
     */
    public function config( $root )
    {
        $this->config->addRoot( $root );
        return $this;
    }

    /**
     * @param string $file
     * @return mixed
     */
    public function read( $file )
    {
        return $this->config->read($file);
    }

}