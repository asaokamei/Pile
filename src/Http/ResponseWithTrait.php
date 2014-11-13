<?php
namespace WScore\Pile\Http;

use WScore\Pile\App;

trait ResponseWithTrait
{

    /**
     * @param string $message
     * @param bool   $error
     * @return $this
     */
    public function withMessage( $message, $error = true )
    {
        App::getApp()->pub( 'message', [
            'error'   => $error,
            'message' => $message,
        ] );
        return $this;
    }

    /**
     * @param array $input
     * @return $this
     */
    public function withInput( $input )
    {
        App::getApp()->pub( 'input', $input );
        return $this;
    }

    /**
     * @param array $errors
     * @return $this
     */
    public function withErrors( $errors )
    {
        App::getApp()->pub( 'errors', $errors );
        return $this;
    }

}