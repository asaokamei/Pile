<?php
namespace WScore\Pile\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Route;
use WScore\Pile\Tools\Dispatch;
use WScore\Pile\Tools\Routing;
use WScore\Pile\Tools\Tools;

class Router implements HttpKernelInterface
{
    /**
     * @var Routing
     */
    protected $routes;

    /**
     * @var string
     */
    protected $rootUrl;

    /**
     * @param string          $url
     * @param string|\Closure $handler
     * @return Route
     */
    public function set( $url, $handler )
    {
        $url = Tools::addSlashAtRight($url);
        $url = $this->rootUrl . $url;
        if( !is_array( $handler ) ) {
            $handler = [ 'controller' => $handler ];
        }
        return $this->routes->set( $url, $handler );
    }

    /**
     * @param string $rootUrl
     * @return $this
     */
    public function setRoot( $rootUrl )
    {
        $this->rootUrl = $rootUrl;
        return $this;
    }

    /**
     * implement the handler for the request.
     *
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     * @return Response
     */
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true )
    {
        if ( $matched = $this->routes->match( $request ) ) {
            return $this->dispatch( $request, $matched );
        }
        return null;
    }

    /**
     * dispatch the controller (class or closure).
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
        return Dispatch::controller( $controller, $request );
    }
}