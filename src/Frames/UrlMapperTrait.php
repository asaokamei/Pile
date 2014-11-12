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
    public function setMap(array $map)
    {
        if( empty( $map ) ) return;

        # Collect an array of all key lengths
        $lengths = array_map('strlen', array_keys($map));

        # Sort paths by their length descending, so the most specific
        # paths go first. `array_multisort` sorts the lengths descending and
        # uses the order on the $map
        array_multisort($lengths, SORT_DESC, $map);
        $this->_url_map = $map;
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
        foreach( $this->_url_map as $path => $app ) {

            if( ($pos=strpos( $pathInfo, $path )) !== 0 ) continue;
            $server = $request->server->all();
            $server['SCRIPT_FILENAME'] = $server['SCRIPT_NAME'] = $server['PHP_SELF'] = $request->getBaseUrl().$path;
            $attributes = $request->attributes->all();
            $attributes[ 'url.mapped' ] = $request->getBaseUrl().$path;
            $newReq = $request->duplicate( null, null, $attributes, null, null, $server );
            return $this->invoke( $newReq, $type, $catch, $app );
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
}
