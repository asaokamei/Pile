<?php
namespace tests\RedirectWith\Functional;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use WScore\Pile\App;

require_once( __DIR__ . '/../../autoloader.php' );
require_once( __DIR__ . '/app/boot.php' );

class RedirectTest extends \PHPUnit_Framework_TestCase
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
        /** @var RedirectResponse $res */
        $res = $this->app->handle( Request::create('/redirect') );
        $res->getTargetUrl();
        $this->assertEquals( true,  $res->isRedirect() );
        $this->assertEquals( '302', $res->getStatusCode() );
        $this->assertEquals( 'http://localhost/redirect-test.php', $res->getTargetUrl() );

    }
}