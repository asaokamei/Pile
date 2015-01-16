<?php
namespace WScore\Pile\Frames;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class UrlMapperTrait
 * @package WScore\Pile\Frames
 *
 * this trait is based on (i.e. copied from)...
 * https://github.com/stackphp/url-map/blob/master/src/Stack/UrlMap.php
 */
trait UrlMapperTrait
{
    /**
     * @var array
     */
    protected $_url_map;

    /**
     * Sets a map of prefixes to objects implementing HttpKernelInterface
     *
     * @param array $map
     */
    public function setMap( array $map )
    {
        if ( empty( $map ) ) return;

        # Collect an array of all key lengths
        $lengths = array_map( 'strlen', array_keys( $map ) );

        # Sort paths by their length descending, so the most specific
        # paths go first. `array_multisort` sorts the lengths descending and
        # uses the order on the $map
        array_multisort( $lengths, SORT_DESC, $map );
        $this->_url_map = $map;
    }

    public function route( $route, $target )
    {
        // todo: implement this method.
    }

    public function name( $name )
    {
        // todo: implement this method.
    }


    /**
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param Request $request A Request instance
     * @param int     $type The type of the request
     *                          (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool    $catch Whether to catch exceptions or not
     *
     * @return Response A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     *
     * @api
     */
    public function handle( Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true )
    {
        $pathInfo = rawurldecode( $request->getPathInfo() );
        foreach ( $this->_url_map as $path => $app ) {

            if ( ( $pos = strpos( $pathInfo, $path ) ) !== 0 ) continue;

            /*
             * try invoke app with new request (just in case failed to respond).
             */
            $newReq = $this->createNewRequest( $request, $path );

            /*
             * if responded, rewrite the existing request information.
             */
            if ( $response = $this->invoke( $newReq, $type, $catch, $app ) ) {
                $this->updatePath( $request, $path );
            }
            return $response;
        }
        return null;
    }

    /**
     * @param Request $request
     * @param int     $type
     * @param bool    $catch
     * @param mixed   $app
     * @return Response|null
     */
    abstract protected function invoke( $request, $type, $catch, $app );

    /**
     * @param Request $request
     * @param string  $path
     * @return Request
     */
    public function createNewRequest( Request $request, $path )
    {
        $newPath    = $request->getBaseUrl() . $path;
        $server     = $request->server->all();
        $attributes = $request->attributes->all();
        // update with new values
        $server[ 'PHP_SELF' ]       = $server[ 'SCRIPT_NAME' ] = $server[ 'SCRIPT_FILENAME' ] = $newPath;
        $attributes[ 'url.mapped' ] = $newPath;
        return $request->duplicate( null, null, $attributes, null, null, $server );
    }

    /**
     * @param Request $request
     * @param string  $path
     */
    public function updatePath( Request $request, $path )
    {
        $server  = $request->server;
        $newPath = $request->getBaseUrl() . $path;
        $server->set( 'SCRIPT_FILENAME', $newPath );
        $server->set( 'SCRIPT_NAME', $newPath );
        $server->set( 'PHP_SELF', $newPath );
        $request->attributes->set( 'url.mapped', $newPath );
    }
}
