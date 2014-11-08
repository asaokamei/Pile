<?php
namespace WScore\Pile\Frames;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
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
            $request->setSession( new SymfonySession() );
        }
        $this->session = $request->getSession();
        $this->app     = \WScore\Pile\App( $request );
        $flash         = $this->session->getFlashBag();

        $this->app->pub( 'message', $flash->get( 'message' ) );
        $this->app->pub( 'input',   $flash->get( 'input' ) );
        $this->app->pub( 'errors',  $flash->get( 'errors' ) );

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
            $flash->set( 'errors',  $this->app->sub( 'errors' ) );
            $flash->set( 'input',   $this->app->sub( 'input' ) );
        }
        $this->session->set( 'token', $this->app->sub( 'token' ) );
        $this->session->save();
        
        return $response;
    }
}