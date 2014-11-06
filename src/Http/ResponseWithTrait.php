<?php
namespace WScore\Pile\Http;

use Tuum\Web\App;

trait ResponseWithTrait
{

    /**
     * @param string $message
     * @param bool   $error
     * @return $this
     */
    public function setMessage( $message, $error = true )
    {
        App::getBag( 'message' )->fill( [
            'error'   => $error,
            'message' => $message,
        ] );
        return $this;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function setInput( $input )
    {
        App::getBag( 'input' )->fill( $input );
        return $this;
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function setErrors( $errors )
    {
        App::getBag( 'errors' )->fill( $errors );
        return $this;
    }

}