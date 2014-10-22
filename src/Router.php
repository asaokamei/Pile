<?php
namespace WScore\Pile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Router extends AbstractPile
{
    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @param string          $url
     * @param string|\Closure $handler
     * @return Route
     */
    public function set( $url, $handler )
    {
        $url = $this->addSlashAtRight($url);
        $url = $this->url . $url;
        if( !is_array( $handler ) ) {
            $handler = [ 'controller' => $handler ];
        }
        $route = new Route( $url, $handler );
        $this->routes->add( $url, $route );
        return $route;
    }

    /**
     * implement the handler for the request.
     *
     * @param Request $request
     * @return Response|null
     */
    protected function handler( Request $request )
    {
        $context = new RequestContext( $request->getRequestUri() );
        $matcher = new UrlMatcher( $this->routes, $context );
        if ( $matched = $matcher->match( $request->getPathInfo() ) ) {
            return $this->dispatch( $request, $matched );
        }
        return null;
    }

    /**
     * dispatch the controller (class or closure).
     *
     * todo: what if controller or method not found? should return
     *  - a bad response with code and message,
     *  - throw an exception, or
     *  - null?
     *
     * @param Request $request
     * @param array   $matched
     * @return Response|null
     */
    protected function dispatch( $request, $matched )
    {
        if( !isset( $matched['controller'] ) ) {
            return null;
        }
        $controller = $matched[ 'controller' ];
        if ( $controller instanceof \Closure ) {
            return $controller( $request );
        }
        if ( is_string( $controller ) && class_exists( $controller ) ) {

            if ( method_exists( $controller, 'handle' ) ) {
                return $controller::handle( $request );
            }
            $obj = new $controller;
            $method = $request->getMethod();
            if( method_exists( $obj, $method ) ) {
                return $obj->$method( $request );
            }
            $method = 'on' . ucwords($request->getMethod());
            if( method_exists( $obj, $method ) ) {
                return $obj->$method( $request );
            }
        }
        return null;
    }

    /**
     * @param object  $obj
     * @param string  $method
     * @param Request $request
     * @return Request|null
     */
    protected function invoke( $obj, $method, $request )
    {
        $refMethod = new \ReflectionMethod( $obj, $method );
        $refArgs   = $refMethod->getParameters();
        $params    = [];
        foreach( $refArgs as $arg ) {
            $key = $arg->getPosition();
            $name = $arg->getName();
            $opt = $arg->isOptional() ? $arg->getDefaultValue() : null;
            $params[ $key ] = $request->get( $name, $opt );
        }
        $refMethod->setAccessible(true);
        return $refMethod->invokeArgs( $obj, $params );
    }
}