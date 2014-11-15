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

*   $app::start()
*   $app->push(Stack $middleware)
*   $app->handle(Request $request, $type, $catch)

サービス

*   $app->setService($service_name, $service)
*   $app->service($service_name)
*   $app->$service_name()
*   $app->request()
*   $app->respond()
*   $app->url()
*   $app->log()

フィルター

*   $app->setFilter($filter_name, $filter)
*   $app->filter($filter_name)

バッグ（共有データ）

*   $app->bag($bag_name)
*   $app->deco($bag_name, array $data)
*   $app->pub($bag_name, array $data)
*   $app->sub($bag_name)


#### Respondサービス

結果を返す。

*   $app->respond()->test($text)
*   $app->respond()->view($filename)
*   $app->respond()->json(array $data)
*   $app->respond()->subRequest(Request $request)

別URLに飛ばす

*   $app->respond()->redirect($url)
*   $app->respond()->reload($url)
*   $app->respond()->named($route_name)
*   $app->respond()->$route_name()

エラーの場合。

*   $app->respond()->error($status, $file=null)
*   $app->respond()->notFound($file=null)



### Middleware Stack

*   $stack->match($route)
*   $stack->before($filter_name)
*   $stack->after($filter_name)


URLMap

*   $map->setMap(array $map)
*   $map->route( $route, $target )
*   $map->name( $route_name )


