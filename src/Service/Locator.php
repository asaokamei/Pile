<?php
namespace WScore\Pile\Service;

class Locator
{
    /**
     * @param string $dir
     * @return UnionManager
     */
    public static function dir( $dir )
    {
        return new UnionManager( $dir );
    }
}