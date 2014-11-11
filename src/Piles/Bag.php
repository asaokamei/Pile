<?php
namespace WScore\Pile\Piles;

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
     * @param array|string $values
     * @param bool  $cleanup
     */
    public function fill( $values, $cleanup=false )
    {
        if( $cleanup || !is_array($values) ) {
            $this->data = $values;
            return;
        }
        $this->data = array_merge( $this->data, $values );
    }
}