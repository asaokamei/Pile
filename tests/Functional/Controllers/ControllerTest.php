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

    /**
     * @test
     */
    function response_view()
    {
        $res = $this->app->handle( Request::create('/more', 'view' ) );
        $this->assertEquals( "view: tested\nmore: tested", $res->getContent() );

    }

    /**
     * @test
     */
    function not_found_error()
    {
        $res = $this->app->handle( Request::create('/error' ) );
        $this->assertEquals( "<h1>Errors</h1>\n<p>not found</p>", trim( $res->getContent() ) );

    }
}