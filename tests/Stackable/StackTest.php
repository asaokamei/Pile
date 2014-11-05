<?php
namespace tests\Stackable;

use WScore\Pile\App;

require_once( __DIR__.'/../autoloader.php' );

$app = App::start();
echo get_class($app) . PHP_EOL;

$app->push( new AddOne() )->push( new AddOne() );
echo $app->handle() . PHP_EOL;

$app->push( new AddOne() );
echo $app->handle() . PHP_EOL;

/*
class StackTest extends \PHPUnit_Framework_TestCase
{

}
    */