<?php
namespace WScore\Pile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractPile implements PileInterface
{
    /**
     * @var PileInterface
     */
    protected $pile;

    /**
     * @var string|\Closure
     */
    protected $url;

    /**
     * @var Request
     */
    protected $request;

    /**
     * a callable/closure style.
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke( Request $request )
    {
        return $this->handle( $request );
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * When $catch is true, the implementation must catch all exceptions
     * and do its best to convert them to a Response instance.
     *
     * @param Request $request   A Request instance
     * @param int     $type      always a sub_request.
     * @param bool    $catch     always ignores exception.
     *
     * @return Response A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     *
     * @api
     */
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true )
    {
        $this->request = $request;
        $response = null;
        if( $this->matcher( $request ) ) {
            $response = $this->handler( $request );
        }
        if( !$response && $this->pile ) {
            $response =  $this->pile->handle( $request );
        }
        return $this->handled( $response );
    }

    /**
     * check if the pile is for the request. handles the next pile if not matched.
     *
     * @param Request $request
     * @return bool
     */
    protected function matcher( Request $request )
    {
        if( !$this->url ) return true;
        $path = $request->getPathInfo();
        if( $this->url instanceof \Closure ) {
            $matcher = $this->url;
            return $matcher( $path );
        }
        if( stripos( $path, $this->url ) === 0 ) return true;
        return false;
    }

    /**
     * implement the handler for the request.
     *
     * @param Request $request
     * @return Response|null
     */
    abstract protected function handler( Request $request );

    /**
     * implement this method to do something with the response, like cache, etc.
     *
     * @param Response|null $response
     * @return Response
     */
    protected function handled( $response )
    {
        return $response;
    }

    /**
     * @param PileInterface $pile
     * @return $this
     */
    public function pile( $pile )
    {
        $this->pile = $pile;
        return $this;
    }

    /**
     * @param string|\Closure $url
     * @return $this
     */
    public function match( $url )
    {
        if( $url ) {
            $this->url = $this->addSlashAtRight($url);
        }
        return $this;
    }

    /**
     * @param $url
     * @return string
     */
    protected function addSlashAtRight( $url )
    {
        return '/' . ltrim( $url, '/' );
    }
}
