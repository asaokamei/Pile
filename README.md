Pile for PHP
============

A simple pile of ```HttpKernel```s to construct a http application.

This repository was developed to understand [StackPHP](http://stackphp.com/).
Compared to StackPHP, Pile offers the ability to:

*   process the response in an explicit way,
*   branch the pile list,
*   does not have to be __middle__ware

not tested. nor used. pure coding :)


### License

MIT License


installation
------------

to-be-written.


Basic Usage
-----------

### creating a handler

create a handler, which implements Symfony's HttpKernelInterface.

```php
class MyHandler implements HttpKernelInterface
{
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true ) {
        if( $request->getPathInfo() === '/handled' ) {
            return new Response( 200, [], 'handled by MyHandler' );
        }
        return null;
    }
}
```

you can push as many piles as you want.
 they are just a simple one-way linked list.


handler can implement PileInterface with handled method
 which can be used to process the $response.

```php
class MyLogger implements PileInterface
{
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true ) {
        return null;
    }
    public function handled( $response ) {
        Log::responded( $response );
    }
}
```

### building an app

then, push it to the pile.

```php
$app = new App;
$app->push( new MyLogger );
$app->push( new MyHandler );
$response = $app->handle( Request::createFromGlobals() );
```



Pile Blocks
-----------

There are several predefined piles to start.

### Router

a simple resource-style dispatcher based on a given path.

to-be-written.

### Match

to-be-coded.

### Branch

to-be-written.



To Do
-----

*   laze-load handlers.
*   documentation.
*   tests.

