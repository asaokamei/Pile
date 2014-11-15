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
*   $app->push($middleware)
*   $app->handle($request, $type, $catch)

サービス

*   $app->setService($name, $service)
*   $app->service($name)
*   $app->respond()
*   $app->url()

フィルター

*   $app->setFilter($name, $filter)
*   $app->filter($name)

バッグ（共有データ）

*   $app->bag($name)
*   $app->deco($name, $data)
*   $app->pub($name, $data)
*   $app->sub($name)

### Middleware Stack

*   $stack->match($route)
*   $stack->before($filter)
*   $stack->after($filter)


### UrlMap

*   $map->setMap($map)
*   $map->route( $route, $target )
*   $map->name( $name )


### Respond

結果を返す。

*   $respond->test($text)
*   $respond->view($filename)
*   $respond->json($data)
*   $respond->subRequest($request)

別URLに飛ばす

*   $respond->redirect($url)
*   $respond->reload($url)
*   $respond->named($name)
*   $respond->$name()

エラーの場合。

*   $respond->error($status,$file=null)
*   $respond->notFound($file=null)


