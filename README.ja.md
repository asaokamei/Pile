Pile for PHP
============

Synfonyの```HttpKernel```を実装したオブジェクトをPileして次々と実行します。
Httpアプリケーションの作成にどうぞ。

本レポジトリは[StackPHP](http://stackphp.com/)を理解するために
コードを起こしたものです。StackPHPに比べると、次のような機能が違います。

*   結果として返ってくるResponseオブジェクトを明示的に操作可能。
*   パイルを分岐できる。
*   __ミドル__ウェアでなくてもOK。

テストなし。走らせたこともなし。コードのみ(^^)


### License

MIT License


Methods
-------

### App

アプリケーションの開始。

*   App $app::start()
*   Stack $app->push(HttpKernelInterface $middleware)
*   Response $app->handle(Request $request, $type, $catch)

サービス

*   App $app->register($service_name, $service)
*   mixed $app->service($service_name)
*   mixed $app->$service_name()
*   Request $app->request()
*   Responder $app->respond()
*   UrlGenerator $app->url()
*   Log $app->log()

フィルター

*   App $app->setFilter($filter_name, $filter)
*   HttpKernelInterface $app->filter($filter_name)

バッグ（共有データ）

*   Bag $app->bag($bag_name)
*   App $app->deco($bag_name, array $data)
*   $app->pub($bag_name, array $data)
*   string|array $app->sub($bag_name)


#### Respondサービス

結果を返す。

*   Response $app->respond()->test($text)
*   Response $app->respond()->view($filename)
*   Response $app->respond()->json(array $data)
*   Response $app->respond()->subRequest(Request $request)

別URLに飛ばす

*   Redirect $app->respond()->redirect($url)
*   Redirect $app->respond()->reload($url)
*   Redirect $app->respond()->named($route_name)
*   Redirect $app->respond()->$route_name()

エラーの場合。

*   Response $app->respond()->error($status, $file=null)
*   Response $app->respond()->notFound($file=null)



### Middleware Stack

*   Stack $stack->push(HttpKernelInterface $middleware)
*   Stack $stack->match($route)
*   Stack $stack->before($filter_name)
*   Stack $stack->after($filter_name) ? may not be implemented...


URLMap Middleware

*   UrlMap $map->setMap(array $map)
*   UrlMap $map->route( $route, $target )
*   UrlMap $map->name( $route_name )


