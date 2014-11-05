<?php
namespace WScore\Pile\Base;

class Bag
{
    protected $data = [];

    /**
     * @param string $name
     * @param mixed  $default
     * @return mixed
     */
    public function get($name, $default=null)
    {
        return isset($this->data[$name]) ? $this->data[$name] : $default;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function exists($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return $this
     */
    public function set( $name, $value )
    {
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * @param array $values
     * @param bool  $cleanup
     */
    public function fill( array $values, $cleanup=false )
    {
        if( $cleanup ) {
            $this->data = [];
        }
        $this->data = array_merge( $this->data, $values );
    }
}