<?php
namespace WScore\Pile\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ForgedControllerInterface extends ControllerInterface
{
    /**
     * @return ForgedControllerInterface|Mixed
     */
    public static function forge();
}