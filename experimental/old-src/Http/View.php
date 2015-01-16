<?php
namespace WScore\Pile\Http;

use Symfony\Component\HttpFoundation\Response;
use Traversable;

class View extends Response
{
    use ResponseWithTrait;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var array   data for template
     */
    protected $data = [ ];

    /**
     * @param $file
     */
    public function setFile( $file )
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param array|Traversable $data
     * @return $this
     */
    public function fill( $data )
    {
        if ( is_array( $data ) || $data instanceof Traversable ) {
            foreach ( $data as $name => $value ) {
                $this->data[ $name ] = $value;
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @return $this
     */
    public function with( $key, $value )
    {
        $this->data[ $key ] = $value;
        return $this;
    }

}