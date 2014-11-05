<?php
namespace tests\Stackable;

use WScore\Pile\App;

require_once( __DIR__.'/../autoloader.php' );

$app = App::start();
echo get_class($app);

/*
class StackTest extends \PHPUnit_Framework_TestCase
{

}
    */