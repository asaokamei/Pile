<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2014/10/22
 * Time: 17:57
 */
namespace WScore\Pile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

interface PileInterface extends HttpKernelInterface
{
    /**
     * @param PileInterface $pile
     * @return $this
     */
    public function pile( $pile );
}