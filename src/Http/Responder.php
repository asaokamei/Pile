<?php
namespace WScore\Pile\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WScore\Pile\App;

class Responder
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $error_file;

    /**
     * @var App
     */
    protected $app;

    /**
     * @param App    $app
     * @param string $error_file
     */
    public function __construct( $app, $error_file )
    {
        $this->app = $app;
        $this->error_file = $error_file;
    }

    /**
     * @param Request $request
     */
    public function setRequest( $request )
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    protected function getRequest()
    {
        return $this->app ? $this->app->request() : $this->request;
    }

    /**
     * @param $content
     * @return Response
     */
    public function text( $content )
    {
        $response = new Response( $content );
        return $response;
    }

    /**
     * return json string.
     * 
     * @param $data
     */
    public function json( $data ) {
        // todo: implement this method.
    }

    /**
     * issue a sub request to itself.
     * 
     * @param $request
     */
    public function subRequest( $request ) {
        // todo: implement this method.
    }

    /**
     * @param string $url
     * @return Redirect
     */
    public function redirect( $url )
    {
        $url = substr($url,0,1)==='/' ? $url : '/'.$url;
        $url      = $this->getRequest()->getUriForPath( $url );
        $response = new Redirect( $url );
        $response->setRequest( $this->getRequest() );
        return $response;
    }

    /**
     * @param string $url
     * @return Redirect
     */
    public function reload( $url = null )
    {
        $url      = $this->getRequest()->getSchemeAndHttpHost() . $this->getRequest()->getBaseUrl() . $url;
        $response = new Redirect( $url );
        $response->setRequest( $this->getRequest() );
        return $response;
    }

    /**
     * @param string $file
     * @return View
     */
    public function view( $file )
    {
        $response = new View();
        $response->setRequest( $this->getRequest() );
        $response->setFile( $file );
        return $response;
    }

    /**
     * @param int    $status
     * @param string $file
     * @return View
     */
    public function error( $status=Response::HTTP_INTERNAL_SERVER_ERROR, $file=null )
    {
        if( !$file ) {
            $file = $this->error_file;
        }
        $response = new View( '', $status );
        $response->setRequest( $this->getRequest() );
        $response->setFile( $file );
        return $response;
    }

    /**
     * @param null $file
     * @return View
     */
    public function notFound( $file=null )
    {
        $response = $this->error( Response::HTTP_NOT_FOUND, $file );
        $response->setRequest( $this->getRequest() );
        return $response;
    }
}