<?php
namespace WScore\Pile\Piles;

interface LocatorInterface
{
    /**
     * locate an absolute path from a partial $file.
     *
     * @param string $file
     * @return bool|string
     */
    public function locate( $file );
}