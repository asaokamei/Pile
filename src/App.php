<?php
namespace WScore\Pile;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Stack\Stackable;

/**
 * Class App
 * @package WScore\Pile
 *
 * its just another Pile without the initial handler.
 */
class App
{
    /**
     * empty constructor...
     *
     * @param HttpKernelInterface $handler
     * @return Stackable
     */
    public static function build( $handler )
    {
        return new Stackable( $handler );
    }

}