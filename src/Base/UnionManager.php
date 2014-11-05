<?php
namespace WScore\Pile\Base;

use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class UnionManager
{
    /**
     * @var \SplStack|FilesystemInterface[]
     */
    protected $filesystems = [];

    /**
     * @param string $root
     */
    public function __construct( $root=null )
    {
        $this->filesystems = new \SplStack();
        $roots = func_get_args();
        foreach( $roots as $root ) {
            $this->add($root);
        }
    }

    /**
     * @param string $root
     */
    public function add($root)
    {
        if( is_string($root) ) {
            $this->filesystems->push( new Filesystem( new Adapter($root) ) );
            return;
        }
        if( $root instanceof FilesystemInterface ) {
            $this->filesystems->push( $root );
            return;
        }
        throw new \InvalidArgumentException;
    }

    /**
     * @param string $file
     * @return mixed
     */
    public function read($file)
    {
        foreach( $this->filesystems as $system ) {
            if( $contents = $system->read($file) ) {
                return $contents;
            }
        }
        return null;
    }

    /**
     * @param $file
     * @return bool
     */
    public function has($file)
    {
        foreach( $this->filesystems as $system ) {
            if( $system->has($file) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $file
     * @param string $content
     * @return array|false
     */
    public function write($file, $content)
    {
        $system = $this->filesystems[0];
        return $system->write( $file, $content );
    }

    /**
     * @param $file
     * @param $content
     * @return array|bool|false
     */
    public function update($file, $content)
    {
        foreach( $this->filesystems as $system ) {
            if( $system->has($file) ) {
                return $system->update($file, $content);
            }
        }
        return false;
    }
}