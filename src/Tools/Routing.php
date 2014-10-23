<?php
namespace WScore\Pile\Tools;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Routing
{
    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @param string $url
     * @param array  $handler
     * @return Route
     */
    public function set( $url, $handler )
    {
        $route = new Route( $url, $handler );
        $this->routes->add( $url, $route );
        return $route;
    }

    /**
     * @param Request $request
     * @return array|null|void
     */
    public function match( Request $request )
    {
        $context = new RequestContext( $request->getRequestUri() );
        $matcher = new UrlMatcher( $this->routes, $context );
        if ( $matched = $matcher->match( $request->getPathInfo() ) ) {
            return $matched;
        }
        return null;
    }
}