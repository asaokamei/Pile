<?php
namespace WScore\Pile\Frames;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Http\Redirect;
use WScore\Pile\Stack\ReleaseInterface;

class Session implements HttpKernelInterface, ReleaseInterface
{
    /**
     * @var SymfonySession
     */
    protected $session;

    /**
     * @var SessionStorageInterface
     */
    protected $storage = null;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param null|SessionStorageInterface $storage
     */
    public function __construct( $storage = null )
    {
        $this->storage = $storage;
    }

    /**
     * @param SessionStorageInterface $storage
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
        $this->request = $request;
        if ( !$request->hasSession() ) {
            $request->setSession( new SymfonySession( $this->storage ) );
        }
        $this->session = $request->getSession();
        $flash         = $this->session->getFlashBag();

        if( $message = $flash->get( 'messages' ) ) {
            $request->attributes->set( 'messages', $message );
        }
        if( $input = $flash->get( 'input' ) ) {
            $request->attributes->set( 'input', $input );
        }
        if( $errors = $flash->get( 'errors' ) ) {
            $request->attributes->set( 'errors', $errors );
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
            $flash->set( 'messages', $this->request->attributes->get( 'messages' ) );
            $flash->set( 'errors',  $this->request->attributes->get( 'errors' ) );
            $flash->set( 'input',   $this->request->attributes->get( 'input' ) );
        }
        $this->session->set( 'token', $this->request->attributes->get( 'token' ) );
        $this->session->save();

        return $response;
    }
}