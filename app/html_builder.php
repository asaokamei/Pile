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
    'set_token' => [ $builder, 'setToken' ],

    /*
     * specify how to set old input into the html builder.
     */
    'set_input' => [ $builder, 'setInput' ],
);