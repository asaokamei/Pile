<?php

use WScore\Form\Builder;

$builder = Builder::forge();
return array(

    /*
     * specify html form builder
     */
    'builder'   => $builder,

    /*
     * specify how to set csrf token into the html builder.
     */
    'set_token' => function( $token ) use( $builder ) {
        $builder->setToken( $token );
    },

    /*
     * specify how to set old input into the html builder.
     */
    'set_input' => function( $input ) use( $builder ) {
        $builder->setInput( $input );
    },
);