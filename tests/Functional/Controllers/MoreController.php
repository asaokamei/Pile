<?php
namespace tests\Functional\Controllers;

use WScore\Pile\Controller\AbstractController;

class MoreController extends AbstractController
{
    public function onGet()
    {
        return 'received onGet method';
    }

    public function onView()
    {
        return $this->respond->view( 'view' )
            ->with( 'test', 'tested' )
            ->fill( ['more' => 'tested' ])
            ;
    }
}