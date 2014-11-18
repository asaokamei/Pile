<?php
namespace WScore\Pile\Service;

use FastRoute\Dispatcher\GroupCountBased as Dispatcher;
use FastRoute\RouteCollector;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Router
 * @package WScore\Pile\Service
 *
 * @method Router get( $uri, $handler )
 * @method Router post( $uri, $handler )
 * @method Router put( $uri, $handler )
 * @method Router delete( $uri, $handler )
 */
class Router
{
    /**
     * @var RouteCollector
     */
    protected $routes;

    /**
     * @param string $method
     * @param string $uri
     * @param mixed  $handler
     * @return $this
     */
    public function __call( $method, $uri, $handler )
    {
        return $this->addRoute( $method, $uri, (array)$handler );
    }

    /**
     * @param string $method
     * @param string $uri
     * @param mixed  $handler
     * @return $this
     */
    public function addRoute( $method, $uri, $handler )
    {
        $method = strtoupper( $method );
        $this->routes->addRoute( $method, $uri, (array)$handler );
        return $this;
    }

    /**
     * @param Request $request
     * @return bool|null|array
     */
    public function match( $request )
    {
        $method     = $request->getMethod();
        $uri        = $request->getRequestUri();
        $dispatcher = new Dispatcher( $this->routes->getData() );

        $found      = $dispatcher->dispatch( $method, $uri );
        switch ( $found[ 0 ] ) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                return false;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                return false;
            case \FastRoute\Dispatcher::FOUND:
                return $found[ 1 ] + $found[ 2 ];
        }
        return null;
    }
}