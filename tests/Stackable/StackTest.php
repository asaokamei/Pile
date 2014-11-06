<?php
namespace tests\Stackable;

use WScore\Pile\App;

require_once( __DIR__.'/../autoloader.php' );

class StackTest extends \PHPUnit_Framework_TestCase
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
        $this->assertEquals( 'WScore\Pile\App', get_class($this->app) );
    }

    /**
     * @test
     */
    function middleware_to_increment_contents()
    {
        $app = $this->app;
        $app->push( new Increment() )->push( new ReturnOne() );
        $this->assertEquals( 2, $app->handle()->getContent() );
    }

    /**
     * @test
     */
    function middleware_to_increment_contents_pushing_twice_should_not_matter()
    {
        $app = $this->app;
        $app->push( new Increment() )->push( new Increment() );
        $app->push( new ReturnOne() );
        $this->assertEquals( 3, $app->handle()->getContent() );
    }
}
