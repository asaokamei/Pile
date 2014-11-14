<?php

use WScore\Pile\App;

return array(

    '/call' => 'tests\Functional\Controllers\CallController',

    '/more' => 'tests\Functional\Controllers\MoreController',

    '/view' => 'tests\Functional\Controllers\MoreController',

    '/redirect' => function($request) {
        return App::reveal( $request )
            ->respond()->redirect('redirect test')
            ->withErrors( ['more' => 'errors'])
            ->withInput( [ 'test' => 'tested'])
            ->withMessage( 'redirected' )
            ;
    },
);