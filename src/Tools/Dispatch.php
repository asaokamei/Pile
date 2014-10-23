<?php
namespace WScore\Pile\Block;

use Symfony\Component\HttpFoundation\Request;

class Dispatch
{
    /**
     * @param string  $controller
     * @param Request $request
     * @return mixed|null
     */
    public static function controller( $controller, Request $request )
    {
        if ( $controller instanceof \Closure ) {
            return $controller( $request );
        }
        if ( is_string( $controller ) && class_exists( $controller ) ) {

            if ( method_exists( $controller, 'invoke' ) ) {
                return $controller::invoke( $request );
            }
            // do the DIC stuff... should use DiC...
            if(  method_exists( $controller, 'forge' ) ) {
                $obj = $controller::forge();
            } else {
                $obj = new $controller;
            }
            $method = $request->getMethod();
            if( method_exists( $obj, $method ) ) {
                return $obj->$method( $request );
            }
            $method = 'on' . ucwords($request->getMethod());
            if( method_exists( $obj, $method ) ) {
                return self::method( $obj, $method, $request );
            }
        }
        return null;
    }

    /**
     * @param object  $obj
     * @param string  $method
     * @param Request $request
     * @return mixed
     */
    public static function method( $obj, $method, $request )
    {
        $refMethod = new \ReflectionMethod( $obj, $method );
        $params    = self::argumentList( $refMethod );
        foreach( $params as $key => $arg ) {
            $name = $arg[0];
            $opt = $arg[1];
            $params[ $key ] = $request->get( $name, $opt );
        }
        $refMethod->setAccessible(true);
        return $refMethod->invokeArgs( $obj, $params );
    }

    /**
     * returns argument information of a method as
     * array(
     *    [ name, default-value ], [ name, default-value ],...
     * )
     *
     * @param \ReflectionMethod $refMethod
     * @return array
     */
    protected static function argumentList( $refMethod )
    {
        $refArgs   = $refMethod->getParameters();
        $params    = [];
        foreach( $refArgs as $arg ) {
            $key = $arg->getPosition();
            $name = $arg->getName();
            $opt = $arg->isOptional() ? $arg->getDefaultValue() : null;
            $params[ $key ] = [ $name, $opt ];
        }
        return $params;
    }
}