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
        $bag = $app->bag( 'test' );
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
     */
    function cannot_publish_twice()
    {
        $app = $this->app;
        $app->pub( 'test', 'tested' );
        $app->pub( 'test', 'more tested' );
        $this->assertEquals( 'tested', $app->sub('test') );
    }
}
