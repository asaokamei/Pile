<?php
namespace WScore\Pile\Frames;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Http\View;
use WScore\Pile\Piles\UnionManager;
use WScore\Pile\Stack\ReleaseInterface;

class Template implements HttpKernelInterface, ReleaseInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var TemplateInterface
     */
    protected $engine;

    /**
     * @param UnionManager      $view
     * @param TemplateInterface $engine
     */
    public function __construct( $view, $engine=null )
    {
        $this->view   = $view;
        $this->engine = $engine ?: new PhpEngine();
    }

    /**
     * Handles a Request to convert it to a Response.
     *
     * @param Request $request A Request instance
     * @param int     $type The type of the request
     *                          (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool    $catch Whether to catch exceptions or not
     *
     * @return SymfonyResponse A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     *
     * @api
     */
    public function handle( Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true )
    {
        $this->request = $request;
    }

    /**
     * @param SymfonyResponse $response
     * @return SymfonyResponse
     */
    public function release( $response )
    {
        if ( $response && $response instanceof View ) {
            return $this->setContents( $response );
        }
        return $response;
    }

    /**
     * @param View $response
     * @return SymfonyResponse
     */
    protected function setContents( $response )
    {
        $app   = $this->request->attributes->get( 'app' );
        $file  = 'view/'.$response->getFile() . '.php';
        $file  = $this->view->locate($file);
        $data  = $response->getData() +
            [
                'message' => $app->sub( 'message' ),
                'errors'  => $app->sub( 'errors' ),
                '_token'  => $app->sub( 'token' ),
            ];
        $response->setContent( $this->$this->engine->render( $file, $data ) );
        return $response;
    }
}