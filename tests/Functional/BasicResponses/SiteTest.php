<?php
namespace tests\BasicResponses\Functional;

use Symfony\Component\HttpFoundation\Request;
use WScore\Pile\App;

require_once( __DIR__ . '/../../autoloader.php' );

class SiteTest extends \PHPUnit_Framework_TestCase
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

    function test0()
    {
        $this->assertEquals( 'WScore\Pile\App', get_class($this->app) );
    }

    /**
     * @test
     */
    function get_response_from_text_closure_and_php_file()
    {
        $res = $this->app->handle( Request::create('/') );
        $this->assertEquals( 'This is from Index File', $res->getContent() );

        $res = $this->app->handle( Request::create('/text') );
        $this->assertEquals( 'This text is returned as string', $res->getContent() );

        $res = $this->app->handle( Request::create('/closure') );
        $this->assertEquals( 'This text is returned from Closure', $res->getContent() );
    }
}
