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
class MiddleWare implements HttpKernelInterface
{
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true ) {
        if( $request->getPathInfo() === '/handled' ) {
            return new Response( 200, [], 'handled by MyHandler' );
        }
        return null;
    }
}
```

a handler can implement PileInterface with handled method
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
$app = new App::build(new MyLogger)
    ->push( new MiddleWare );
$response = $app->handle( Request::createFromGlobals() );
$response->send();
```

you can push as many piles as you want.
 they are just a simple one-way linked list.



Handlers
-----------

There are several predefined handlers as examples.

### Backstage and UrlMap

they are taken from the StackPHP middleware with respect, 
and removed the $app part. 

### Router

A simple router handler. 


Piles
-----

piles are the basic building blocks for the web application. 

### Branch 

experimental implementation to branch the pile based on the 
request pathinfo. 


To Do
-----

*   make it work. 
*   laze-load handlers.
*   documentation.
*   tests.

