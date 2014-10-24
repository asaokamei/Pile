<?php
namespace WScore\Pile;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use WScore\Pile\Pile\Pile;

/**
 * Class App
 * @package WScore\Pile
 *
 * its just another Pile without the initial handler.
 */
class App extends Pile
{
    /**
     * empty constructor...
     *
     * @param HttpKernelInterface $handler
     * @return Pile
     */
    public static function build( $handler )
    {
        return new self( $handler );
    }

}