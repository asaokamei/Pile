<?php
namespace WScore\Pile\Handler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use WScore\Pile\Controller\ControllerInterface;

/**
 * URL Map Middleware, which maps kernels to paths
 *
 * Maps kernels to path prefixes and is insertable into a stack.
 *
 * @author
 * Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 *
 * class is from the back-stage class below with respect.
 * https://github.com/stackphp/url-map
 * 
 * to demonstrate the difference between the middleware and handler
 * 
 */
class UrlMap implements HttpKernelInterface
{
    use ApplyFilterTrait;

    const ATTR_PREFIX = "pile.url_map.prefix";

    /**
     * @var HttpKernelInterface[]
     */
    protected $map = array();

    /**
     * @var string
     */
    protected $root;

    public function __construct( array $map = array())
    {
        if ($map) {
            $this->setMap($map);
        }
    }

    /**
     * @param array $map
     * @return UrlMap
     */
    public static function forge( $map )
    {
        return new self( $map );
    }

    /**
     * @param string $root
     */
    public function setRoot( $root )
    {
        $this->root = $root;
    }

    /**
     * Sets a map of prefixes to objects implementing HttpKernelInterface
     *
     * @param array $map
     */
    public function setMap(array $map)
    {
        # Collect an array of all key lengths
        $lengths = array_map('strlen', array_keys($map));

        # Sort paths by their length descending, so the most specific
        # paths go first. `array_multisort` sorts the lengths descending and
        # uses the order on the $map
        array_multisort($lengths, SORT_DESC, $map);

        $this->map = $map;
    }

    /**
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     * @return null|Response
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $pathInfo = rawurldecode($request->getPathInfo());
        if (0 !== strpos($pathInfo, $this->root ) ) return null;

        foreach ($this->map as $path => $app) {

            $path = $this->root . $path;
            if (0 !== strpos($pathInfo, $path)) continue;

            $server = $request->server->all();
            $server['SCRIPT_FILENAME'] = $server['SCRIPT_NAME'] = $server['PHP_SELF'] = $request->getBaseUrl().$path;

            $attributes = $request->attributes->all();
            $attributes[static::ATTR_PREFIX] = $request->getBaseUrl().$path;

            $newRequest = $request->duplicate(null, null, $attributes, null, null, $server);

            return $app->handle($newRequest, $type, $catch);
        }
        return null;
    }

    /**
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     * @param mixed   $app
     * @return Response
     */
    protected function invoke( Request $request, $type, $catch, $app )
    {
        /*
         * apply filters before invoking subsequent apps.
         */
        if( $response = $this->applyFilters( $request ) ) {
            return $response;
        }
        /*
         * run the application.
         */
        if( is_string( $app ) && class_exists( $app ) ) {
            if( $app instanceof ControllerInterface ) {
                return $app::call( $request, $type, $catch );
            }
            $app = new $app;
        }
        if( $app instanceof HttpKernelInterface ) {
            return $app->handle( $request, $type, $catch );
        }
        throw new \RuntimeException("no map found");
    }
}