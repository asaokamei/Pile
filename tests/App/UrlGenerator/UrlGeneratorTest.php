<?php
namespace tests\App\UrlGenerator;

use Symfony\Component\HttpFoundation\Request;
use WScore\Pile\Http\UrlGenerator;

require_once( __DIR__ . '/../../autoloader.php' );

class UrlGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UrlGenerator
     */
    protected $url;

    function setup()
    {
        $this->url = new UrlGenerator(null);
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Pile\Http\UrlGenerator', get_class($this->url ) );
    }

    /**
     * @test
     */
    function wrt_to()
    {
        $request = Request::create( '/path/to/level1/level2/target', 'GET', [], [], [], [
            'SCRIPT_FILENAME' => '/sys/path/to/index.php',
            'PHP_SELF' => '/path/to/index.php'
        ] );
        $url = $this->url;
        $url->setRequest( $request );
        $this->assertEquals( '/path/to/test.php', (string) $url( 'test.php' ) );
        $this->assertEquals( '/path/to/test.php', (string) $url()->to( 'test.php' ) );
        $this->assertEquals( '/path/to/test.php', (string) $url()->base( 'test.php' ) );

        $this->assertEquals( 'https://localhost/path/to/test.php', (string) $url()->secure()->to( 'test.php' ) );
        $this->assertEquals( 'https://localhost/path/to/test.php', (string) $url()->to( 'test.php' )->secure() );

        $this->assertEquals( '/path/to/test.php?bad=%5B%26%3F%5D&amp;reg=123', (string) $url()->base( 'test.php' )->with( ['bad'=>'[&?]', 'reg'=>'123']) );

    }
}
