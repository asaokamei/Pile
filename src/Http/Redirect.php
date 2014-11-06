<?php
namespace WScore\Pile\Http;

use Symfony\Component\HttpFoundation\RedirectResponse;

class Redirect extends RedirectResponse
{
    use ResponseWithTrait;
}