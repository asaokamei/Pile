<?php
namespace WScore\Pile\Service;

use League\Flysystem\Adapter\Local as Adapter;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;

class UnionManager implements LocatorInterface
{
    /**
     * @var \SplStack|FilesystemInterface[]
     */
    protected $filesystems = [ ];

    /**
     * @var array
     */
    protected $directories = [ ];

    /**
     * @param string $root
     */
    public function __construct( $root = null )
    {
        $this->filesystems = new \SplStack();
        $roots             = func_get_args();
        foreach ( $roots as $root ) {
            $this->addRoot( $root );
        }
    }

    /**
     * @param string $root
     */
    public function addRoot( $root )
    {
        if ( is_string( $root ) ) {
            $this->addFileSystem( new Filesystem( new Adapter( $root ) ), $root );
            return;
        }
        if ( $root instanceof FilesystemInterface ) {
            $this->addFileSystem( $root );
            return;
        }
        throw new \InvalidArgumentException;
    }

    /**
     * @param FilesystemInterface $system
     * @param null                $root
     */
    protected function addFileSystem( $system, $root = null )
    {
        $this->filesystems->push( $system );
        if ( $root ) {
            $root .= substr( $root, -1 ) == '/' ? '' : '/';
            $this->directories[ spl_object_hash( $system ) ] = $root;
        }
    }

    /**
     * @param string $file
     * @return bool|string
     */
    public function locate( $file )
    {
        foreach ( $this->filesystems as $system ) {
            if ( $system->has( $file ) ) {
                $meta = $system->getMetadata( $file );
                $hash = spl_object_hash( $system );
                $root = isset( $this->directories[ $hash ] ) ? $this->directories[ $hash ] : null;
                return $root . $meta[ 'path' ];
            }
        }
        return false;
    }

    /**
     * @param string $file
     * @param array  $data
     * @return mixed|null
     */
    public function evaluate( $file, $data=[] )
    {
        $file .= substr($file,-4)==='.php' ? '' : '.php';
        if( $location = $this->locate($file) ) {
            extract($data);
            /** @noinspection PhpIncludeInspection */
            return include( $location );
        }
        return null;
    }

    /**
     * @param string $file
     * @throws \Exception
     * @return mixed
     */
    public function read( $file )
    {
        foreach ( $this->filesystems as $system ) {
            try {
                return $system->read( $file );
            } catch ( FileNotFoundException $e ) {
                // continue. do nothing.
            } catch ( \Exception $e ) {
                throw $e;
            }
        }
        return null;
    }

    /**
     * @param $file
     * @return bool
     */
    public function has( $file )
    {
        foreach ( $this->filesystems as $system ) {
            if ( $system->has( $file ) ) {
                return true;
            }
        }
        return false;
    }
}