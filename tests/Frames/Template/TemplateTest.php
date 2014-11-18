<?php
namespace tests\Frames\UrlMap;

use Symfony\Component\HttpFoundation\Request;
use tests\Frames\Template\ResponseRaw;
use WScore\Pile\App;
use WScore\Pile\Frames\Template;

require_once( __DIR__ . '/../../autoloader.php' );

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Template
     */
    protected $tmp;

    /**
     * @var App
     */
    protected $app;

    function setup()
    {
        class_exists( 'Symfony\Component\HttpFoundation\Request' );
        class_exists( 'Symfony\Component\HttpFoundation\Response' );
        $this->app = App::start();
        $this->tmp = Template::forge( $this->app, __DIR__ );
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Pile\Frames\Template', get_class( $this->tmp ) );
    }

    /**
     * @test
     */
    function render_raw_php()
    {
        $app = $this->app;
        $app->push( $this->tmp )->push( new ResponseRaw() );
        $req      = Request::create( '/' );
        $response = $app->handle( $req );
        $this->assertEquals( 'raw file: tested', $response->getContent() );
    }

    /**
     * @test
     */
    function use_renderer_closure()
    {
        $app = $this->app;
        $tmp = Template::forge( $this->app, function( $file, $data, $request ) {
            return "Closure: {$file}.";
        } );
        $app->push( $tmp )->push( new ResponseRaw() );
        $req      = Request::create( '/' );
        $response = $app->handle( $req );
        $this->assertEquals( 'Closure: raw_file.', $response->getContent() );
    }
}
