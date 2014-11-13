<?php
namespace tests\Functional\Controllers;

use WScore\Pile\Controller\AbstractController;

class MoreController extends AbstractController
{
    public function onGet()
    {
        return 'received onGet method';
    }
}