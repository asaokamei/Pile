<?php
namespace WScore\Pile;

use WScore\Pile\Piles\UnionManager;

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