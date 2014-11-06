<?php
namespace tests\Frames\UrlMap;

use Symfony\Component\HttpFoundation\Request;
use WScore\Pile\Frames\UrlMap;

require_once( __DIR__ . '/../../autoloader.php' );

class UrlMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UrlMap
     */
    protected $map;

    function setup()
    {
        $this->map = new UrlMap();
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Pile\Frames\UrlMap', get_class( $this->map ) );
    }

    /**
     * @test
     */
    function urlMap_returns_some_text()
    {
        $map = $this->map;
        $map->setMap( [ '/' => 'test' ] );
        $request = Request::create( '/' );
        $this->assertEquals( 'test', $map->handle( $request ) );
    }

    /**
     * @test
     */
    function urlMap_request_()
    {
        $map = $this->map;
        $map->setMap( [
            '/test' => '\tests\Frames\UrlMap\ReturnRequest',
            '/some' => '\tests\Frames\UrlMap\ReturnRequest',
        ] );
        /*
         * test on /test/more
         */
        $request = Request::create( '/test/more' );
        /** @var Request $request */
        $request = $map->handle( $request );
        $this->assertEquals('/more', $request->getPathInfo() );
        $this->assertEquals('/test', $request->getBaseUrl() );
        /*
         * test on /some
         */
        $request = Request::create( '/some' );
        /** @var Request $request */
        $request = $map->handle( $request );
        $this->assertEquals('/', $request->getPathInfo() );
        $this->assertEquals('/some', $request->getBaseUrl() );
        /*
         * test on missing
         */
        $request = Request::create( '/none' );
        /** @var Request $request */
        $this->assertEquals(null, $map->handle( $request ) );

    }
}

