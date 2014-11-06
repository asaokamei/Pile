<?php
namespace WScore\Pile\Handler;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StackBackstage
 *
 * class is from the back-stage class below with respect.
 * https://github.com/atst/stack-backstage
 * 
 * to demonstrate the difference between the middleware and handler
 */
class StackBackstage implements HttpKernelInterface
{
    protected $path;

    public function __construct(HttpKernelInterface $app, $path)
    {
        $this->app = $app;
        $this->path = $path;
    }

    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $path = realpath($this->path);

        if (false !== $path) {
            return new Response(file_get_contents($path), 503);
        }
        return null;
    }
}