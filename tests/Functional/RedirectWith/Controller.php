<?php
namespace tests\Functional\RedirectWith;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WScore\Pile\Controller\AbstractController;

class Controller extends AbstractController
{
    /**
     * @param Request $request
     * @param         $type
     * @param         $catch
     * @return Response
     */
    public static function call( Request $request, $type, $catch )
    {
        return 'called controller';
    }

    public function onGet()
    {
        return 'tested controller';
    }
}