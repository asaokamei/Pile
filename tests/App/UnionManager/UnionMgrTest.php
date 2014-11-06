<?php
namespace tests\App\UnionManager;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use WScore\Pile\Base\UnionManager;

require_once( __DIR__ . '/../../autoloader.php' );

class UnionMgrTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UnionManager
     */
    protected $mgr;

    function setup()
    {
        $this->mgr = new UnionManager( __DIR__ . '/app' );
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Pile\Base\UnionManager', get_class( $this->mgr ) );
    }

    /**
     * @test
     */
    function get_contents_from_app_folder()
    {
        $mgr = $this->mgr;
        $this->assertEquals( true,  $mgr->has( 'test.txt' ) );
        $this->assertEquals( true,  $mgr->has( 'more.txt' ) );
        $this->assertEquals( false, $mgr->has( 'none.txt' ) );

        $this->assertEquals( 'test', $mgr->read( 'test.txt' ) );
        $this->assertEquals( 'more', $mgr->read( 'more.txt' ) );
        $this->assertEquals( null,   $mgr->read( 'none.txt' ) );
    }

    /**
     * @test
     */
    function get_contents_from_app_and_tested_folder()
    {
        $mgr = $this->mgr;
        $mgr->addRoot( __DIR__ . '/app/tested' );

        $this->assertEquals( true,  $mgr->has( 'test.txt' ) );
        $this->assertEquals( true,  $mgr->has( 'tested.txt' ) );
        $this->assertEquals( true,  $mgr->has( 'more.txt' ) );
        $this->assertEquals( false, $mgr->has( 'none.txt' ) );

        $this->assertEquals( 'tested test',   $mgr->read( 'test.txt' ) );
        $this->assertEquals( 'tested tested', $mgr->read( 'tested.txt' ) );
        $this->assertEquals( 'more',          $mgr->read( 'more.txt' ) );
        $this->assertEquals( null,            $mgr->read( 'none.txt' ) );
    }

    /**
     * @test
     */
    function get_contents_from_app_and_tested_adaptor()
    {
        $mgr  = $this->mgr;
        $root = new Filesystem( new Local(__DIR__ . '/app/tested') );
        $mgr->addRoot( $root );

        $this->assertEquals( true,  $mgr->has( 'test.txt' ) );
        $this->assertEquals( true,  $mgr->has( 'tested.txt' ) );
        $this->assertEquals( true,  $mgr->has( 'more.txt' ) );
        $this->assertEquals( false, $mgr->has( 'none.txt' ) );

        $this->assertEquals( 'tested test',   $mgr->read( 'test.txt' ) );
        $this->assertEquals( 'tested tested', $mgr->read( 'tested.txt' ) );
        $this->assertEquals( 'more',          $mgr->read( 'more.txt' ) );
        $this->assertEquals( null,            $mgr->read( 'none.txt' ) );
    }
}
