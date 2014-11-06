<?php
namespace WScore\Pile\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tuum\Web\Http\Redirect;
use Tuum\Web\Http\PhpFile;

class Responder
{
    protected $request;

    /**
     * @param Request $request
     */
    public function __construct( $request )
    {
        $this->request = $request;
    }

    /**
     * @param $content
     * @return Response
     */
    public function text( $content )
    {
        return new Response( $content );
    }

    /**
     * @param string $url
     * @return Redirect
     */
    public function redirect( $url )
    {
        $url = $this->request->getUriForPath($url);
        $response = new Redirect($url);
        return $response;
    }

    /**
     * @param string $url
     * @return Redirect
     */
    public function reload( $url=null )
    {
        $url = $this->request->getSchemeAndHttpHost() . $this->request->getBaseUrl() . $url;
        $response = new Redirect($url);
        return $response;
    }

    /**
     * @param string $file
     * @return PhpFile
     */
    public function view( $file )
    {
        $response = new PhpFile();
        $response->setFile($file);
        return $response;
    }
}