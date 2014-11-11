<?php
namespace WScore\Pile\Frames;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class Map
 * @package Tuum\Web\Ware
 *
 * copied code with respect!
 * https://github.com/stackphp/url-map/blob/master/src/Stack/UrlMap.php
 *
 */
class UrlMap implements HttpKernelInterface
{
    use UrlMapperTrait;

    use ApplyFilterTrait;

    /**
     */
    public function __construct()
    {
    }

    /**
     * @param array|string $map
     * @return UrlMap
     */
    public static function forge( $map )
    {
        if( is_string( $map ) ) {
            $map = include($map);
        }
        $self = new self( $map );
        $self->setMap( $map );
        return $self;
    }

    /**
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     * @param mixed   $app
     * @return Response|mixed
     */
    protected function invoke( $request, $type, $catch, $app )
    {
        /*
         * apply filters before invoking apps.
         */
        if( $response = $this->applyFilters( $request ) ) {
            return $response;
        }
        /*
         * invoke an app.
         */
        if( is_string( $app ) && class_exists( $app ) ) {
            if( method_exists( $app, 'call' ) ) {
                return $app::call( $request, $type, $catch );
            }
            $app = new $app;
        }
        if( $app instanceof \Closure ) {
            return $app( $request, $type, $catch );
        }
        if( $app instanceof HttpKernelInterface ) {
            return $app->handle( $request, $type, $catch );
        }
        return $app;
    }
}