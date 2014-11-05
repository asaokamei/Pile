<?php
namespace WScore\Pile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Base\Bag;
use WScore\Pile\Base\UnionManager;
use WScore\Pile\Stack\Stackable;
use WScore\Pile\Stack\StackableInterface;

/**
 * Class App
 * @package WScore\Pile
 *
 * its just another Pile without the initial handler.
 */
class App
{
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
     * @var HttpKernelInterface|Stackable|StackableInterface
     */
    protected $stack;

    // +----------------------------------------------------------------------+
    //  static methods
    // +----------------------------------------------------------------------+
    /**
     * @return App
     */
    public static function start()
    {
        $file = new UnionManager();
        static::$app = new static( $file );
        return static::$app;
    }

    /**
     * @param $app
     */
    public static function setInstance($app)
    {
        static::$app = $app;
    }

    /**
     * @param $name
     * @return Bag
     */
    public static function getBag($name)
    {
        return static::$app->bag($name);
    }

    // +----------------------------------------------------------------------+
    //  managing instance and stacks
    // +----------------------------------------------------------------------+
    /**
     * @param UnionManager $file
     */
    public function __construct( $file )
    {
        $this->config = $file;
    }

    /**
     * @param HttpKernelInterface|StackableInterface $stack
     * @return Stackable
     */
    public function push( $stack )
    {
        if( $this->stack ) {
            return $this->stack->push( $stack );
        }
        $this->stack = stackable::makeStack( $stack );
        return $stack;
    }

    /**
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     * @return Response
     */
    public function handle( $request, $type, $catch )
    {
        $request->attributes->set( 'app', $this );
        return $this->stack->handle( $request, $type, $catch );
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
        $this->config->add( $root );
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