<?php
namespace tests\RedirectWith\Functional;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use WScore\Pile\App;

require_once( __DIR__ . '/../../autoloader.php' );
require_once( __DIR__ . '/app/boot.php' );

class RedirectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var MockArraySessionStorage
     */
    protected $session;

    function setup()
    {
        /** @var \Closure $boot_pile */
        $boot_pile = include( __DIR__ . '/app/boot.php' );
        $this->session = new MockArraySessionStorage();
        $this->app = $boot_pile( $this->session );
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
        $session = $this->session;
        /** @var FlashBagInterface $flash */
        $flash = $session->getBag( 'flashes' );
        $this->assertEquals( ['message' => 'redirected'], $flash->get( 'messages' ) );
        $this->assertEquals( ['test' => 'tested'], $flash->get( 'input' ) );
        $this->assertEquals( ['more' => 'errors'], $flash->get( 'errors' ) );
    }
}