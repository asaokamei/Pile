<?php
namespace WScore\Pile\Service;

class Locator extends UnionManager
{
    /**
     * @var array
     */
    protected $pass = [];

    /**
     * @param string $dir
     * @param array  $pass
     * @return UnionManager
     */
    public static function dir( $dir, $pass=[] )
    {
        $self = new self( $dir );
        $self->pass = $pass;
        return $self;
    }

    /**
     * @param string $file
     * @param array  $data
     * @return mixed|null|void
     */
    public function evaluate( $file, $data=[] )
    {
        $data = array_merge( $this->pass, $data );
        return parent::evaluate( $file, $data );
    }
}