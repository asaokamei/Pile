<?php
namespace WScore\Pile\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use WScore\Pile\App;

class Request extends SymfonyRequest
{
    /**
     * @var Responder
     */
    protected $respond;

    /**
     * @var App
     */
    protected $app;

    /**
     * @param SessionStorageInterface $storage
     * @return SymfonyRequest
     */
    public static function startGlobal( $storage = null )
    {
        $session = new Session( $storage );
        $request = new Request( $_GET, $_POST, array(), $_COOKIE, $_FILES, $_SERVER );
        $request->setSession( $session );

        $respond = new Responder( null, null );
        $respond->setRequest( $request );
        $request->setRespond( $respond );

        return $request;
    }

    /**
     * @param Responder $respond
     */
    protected function setRespond( $respond )
    {
        $this->respond = $respond;
    }

    /**
     * @param App $app
     */
    public function setApp( $app )
    {
        $this->app = $app;
    }

    /**
     * @return Responder
     */
    public function respond()
    {
        return $this->respond;
    }

    /**
     * @param string $name
     * @return null|Response
     */
    public function filter( $name )
    {
        return $this->app->filter( $name, $this );
    }

    /**
     * @return App
     */
    public function app()
    {
        return $this->app;
    }
}