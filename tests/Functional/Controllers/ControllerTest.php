<?php
namespace tests\Controllers\Functional;

use Symfony\Component\HttpFoundation\Request;
use WScore\Pile\App;

require_once( __DIR__ . '/../../autoloader.php' );
require_once( __DIR__ . '/app/boot.php' );

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var App
     */
    protected $app;

    function setup()
    {
        /** @var \Closure $boot_pile */
        $boot_pile = include( __DIR__ . '/app/boot.php' );
        $this->app = $boot_pile();
    }

    /**
     * @test
     */
    function get_response_from_controller()
    {
        $res = $this->app->handle( Request::create('/call') );
        $this->assertEquals( 'calling controller', $res->getContent() );

        $res = $this->app->handle( Request::create('/more') );
        $this->assertEquals( 'received onGet method', $res->getContent() );

    }
}