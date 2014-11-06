<?php
namespace WScore\Pile\Base;

use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use SebastianBergmann\Exporter\Exception;

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
            $this->addRoot($root);
        }
    }

    /**
     * @param string $root
     */
    public function addRoot($root)
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
     * @throws \Exception
     * @return mixed
     */
    public function read($file)
    {
        foreach( $this->filesystems as $system ) {
            try {
                return $system->read($file);
            } catch( FileNotFoundException $e ) {
                // continue. do nothing.
            } catch( \Exception $e ) {
                throw $e;
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
}