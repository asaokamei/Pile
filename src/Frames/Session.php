<?php
namespace WScore\Pile\Frames;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Http\Redirect;
use WScore\Pile\App;
use WScore\Pile\Stack\ReleaseInterface;

class Session implements HttpKernelInterface, ReleaseInterface
{
    /**
     * @var SymfonySession
     */
    protected $session;

    /**
     * @var App
     */
    protected $app;

    /**
     * @var null|MockArraySessionStorage
     */
    protected $storage = null;

    /**
     * @param null|MockArraySessionStorage $storage
     */
    public function __construct( $storage = null )
    {
        $this->storage = $storage;
    }

    /**
     * @return Session
     */
    public static function forge( $storage = null )
    {
        return new self( $storage );
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * @param Request $request  A Request instance
     * @param int     $type     The type of the request
     *                          (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool    $catch    Whether to catch exceptions or not
     * @return Response A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     *
     * @api
     */
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true )
    {
        if ( !$request->hasSession() ) {
            $request->setSession( new SymfonySession( $this->storage ) );
        }
        $this->session = $request->getSession();
        $this->app     = App::reveal( $request );
        $flash         = $this->session->getFlashBag();

        if( $message = $flash->get( 'message' ) ) {
            $this->app->pub( 'message', $message );
        }
        if( $error = $flash->get( 'error' ) ) {
            $this->app->pub( 'error', $error );
        }
        if( $input = $flash->get( 'input' ) ) {
            $this->app->pub( 'input', $input );
        }
        if( $errors = $flash->get( 'errors' ) ) {
            $this->app->pub( 'errors', $errors );
        }

        return null;
    }

    /**
     * @param Response $response
     * @return Response
     */
    public function release( $response )
    {
        if ( $response instanceof Redirect ) {
            $flash = $this->session->getFlashBag();
            $flash->set( 'message', $this->app->sub( 'message' ) );
            $flash->set( 'error',   $this->app->sub( 'error' ) );
            $flash->set( 'errors',  $this->app->sub( 'errors' ) );
            $flash->set( 'input',   $this->app->sub( 'input' ) );
        }
        $this->session->set( 'token', $this->app->sub( 'token' ) );
        $this->session->save();

        return $response;
    }
}