<?php
namespace WScore\Pile\Http;

use Symfony\Component\HttpFoundation\Request;
use WScore\Pile\App;

trait ResponseWithTrait
{
    /**
     * @var Request
     */
    protected $request;

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
        return $this->request;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withMessage( $message )
    {
        if( $request = $this->getRequest() ) {
            App::reveal( $request )->pub( 'message', $message );
        }
        return $this;
    }

    /**
     * @param string $error
     * @return $this
     */
    public function withErrorMsg( $error )
    {
        if( $request = $this->getRequest() ) {
            App::reveal( $request )->pub( 'error', $error );
        }
        return $this;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function withInput( $input )
    {
        if( $request = $this->getRequest() ) {
            App::reveal( $request )->pub( 'input', $input );
        }
        return $this;
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function withValidationMsg( $errors )
    {
        if( $request = $this->getRequest() ) {
            App::reveal( $request )->pub( 'errors', $errors );
        }
        return $this;
    }

}