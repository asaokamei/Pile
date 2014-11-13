<?php
namespace WScore\Pile\experimental\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController implements ControllerInterface
{
    /**
     * @var array
     */
    protected $routes = [ ];

    /**
     * @var Request
     */
    protected $request;

    /**
     * simple routing
     * @param string $uri
     * @return array
     */
    protected function routes( $uri )
    {
        foreach ( $this->routes as $method => $pattern ) {
            $pattern = "@^" . preg_replace( '/\\\:[-_0-9a-zA-Z]+/', '([-_0-9a-zA-Z]+)', preg_quote( $pattern ) ) . "$@D";
            if ( preg_match( $pattern, $uri, $params ) ) {
                array_shift( $params );
                $params[ 'method' ] = $method;
                return $params;
            }
        }
        return [ ];
    }

    /**
     * @param Request $request
     */
    public function setRequest( $request )
    {
        $this->request = $request;
    }

    /**
     * @param string $method
     * @param array  $param
     * @return Response|null
     */
    protected function invoke( $method, $param )
    {
        $method = 'on' . ucwords( $method );
        if ( !method_exists( $this, $method ) ) {
            return null;
        }
        $refMethod = new \ReflectionMethod( $this, $method );
        $refArgs   = $refMethod->getParameters();
        $arguments = array();
        foreach( $refArgs as $arg ) {
            $key  = $arg->getPosition();
            $name = $arg->getName();
            $opt  = $arg->isOptional() ? $arg->getDefaultValue() : null;
            $val  = isset($param[$name]) ? $param[$name] : $opt;
            $arguments[$key] = $val;
        }
        $refMethod->setAccessible(true);
        return $refMethod->invokeArgs( $this, $arguments );
    }

    /**
     * @param Request $request
     * @param         $type
     * @param         $catch
     * @return Response|null
     */
    public static function call( Request $request, $type, $catch )
    {
        $pathInfo = $request->getPathInfo();
        $app      = static::forge();
        $app->setRequest( $request );
        $method = $request->getMethod();
        if ( $params = $app->routes( $pathInfo ) ) {
            $method = $params[ 'method' ];
        }
        return $app->invoke( $method, $params );
    }
}