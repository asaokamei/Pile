<?php

use WScore\Pile\App;

return array(

    '/reply' => 'tests\Functional\RedirectWith\Controller',

    '/redirect' => function($request) {
        return App::reveal( $request )
            ->respond()->redirect('redirect-test.php')
            ->withValidationMsg( ['more' => 'errors'])
            ->withInput( [ 'test' => 'tested'])
            ->withMessage( 'redirected' )
            ;
    },
);