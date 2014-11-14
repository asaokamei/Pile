<?php

use WScore\Pile\App;

return array(

    '/call' => 'tests\Functional\Controllers\CallController',

    '/more' => 'tests\Functional\Controllers\MoreController',

    '/view' => 'tests\Functional\Controllers\MoreController',

    '/error' => function($request) {
        return App::reveal( $request )->respond()->notFound()
            ->withErrorMsg( 'not found' )
            ;
    },
);