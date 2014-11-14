<?php
namespace WScore\Pile\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param string $error_file
     */
    public function __construct( $error_file )
    {
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
     * @param $content
     * @return Response
     */
    public function text( $content )
    {
        $response = new Response( $content );
        return $response;
    }

    /**
     * @param string $url
     * @return Redirect
     */
    public function redirect( $url )
    {
        $url = substr($url,0,1)==='/' ? $url : '/'.$url;
        $url      = $this->request->getUriForPath( $url );
        $response = new Redirect( $url );
        $response->setRequest( $this->request );
        return $response;
    }

    /**
     * @param string $url
     * @return Redirect
     */
    public function reload( $url = null )
    {
        $url      = $this->request->getSchemeAndHttpHost() . $this->request->getBaseUrl() . $url;
        $response = new Redirect( $url );
        $response->setRequest( $this->request );
        return $response;
    }

    /**
     * @param string $file
     * @return View
     */
    public function view( $file )
    {
        $response = new View();
        $response->setRequest( $this->request );
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
        $response->setRequest( $this->request );
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
        $response->setRequest( $this->request );
        return $response;
    }
}