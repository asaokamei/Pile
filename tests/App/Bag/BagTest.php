<?php
namespace tests\App\UnionManager;

use WScore\Pile\App;

require_once( __DIR__ . '/../../autoloader.php' );

class BagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var App
     */
    var $app;

    function setup()
    {
        $this->app = App::start();
    }

    function test0()
    {
        $app = $this->app;
        $bag = $app->getBag( 'test' );
        $this->assertEquals( 'WScore\Pile\Piles\Bag', get_class( $bag ) );
    }

    /**
     * @test
     */
    function basic_pub_sub()
    {
        $app = $this->app;
        $app->pub( 'test', 'tested' );
        $this->assertEquals( 'tested', $app->sub( 'test' ) );

        $app->pub( 'more', [ 'more' => 'tested' ] );
        $this->assertEquals( [ 'more' => 'tested' ], $app->sub( 'more' ) );
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    function cannot_publish_twice()
    {
        $app = $this->app;
        $app->pub( 'test', 'tested' );
        $app->pub( 'test', 'more tested' );
    }

    /**
     * @test
     */
    function do_bag_features()
    {
        $app = $this->app;
        $app->pub( 'test', [ 'test' => 'tested' ] );
        $bag = $app->getBag( 'test' );
        $bag->set( 'more', 'more test' );
        // test all()
        $this->assertEquals( [ 'test' => 'tested', 'more' => 'more test' ], $bag->all() );
        // test get()
        $this->assertEquals( 'tested', $bag->get( 'test', 'ignore this' ) );
        $this->assertEquals( null, $bag->get( 'none' ) );
        $this->assertEquals( 'no such data', $bag->get( 'none', 'no such data' ) );
        // test exists()
        $this->assertEquals( true, $bag->exists( 'test' ) );
        $this->assertEquals( false, $bag->exists( 'none' ) );
    }
}
