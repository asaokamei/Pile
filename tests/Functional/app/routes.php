<?php

use WScore\Pile\App;

return array(

    '/' => function($request) {
        return App::reveal( $request )
            ->respond()->view( 'index' );
    },

    '/text' => 'This text is returned as string',

    '/closure' => function($request) {
        return App::reveal( $request )
            ->respond()->text( 'This text is returned from Closure');
    },
);