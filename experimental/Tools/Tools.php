<?php
namespace WScore\Pile\Tools;

use Symfony\Component\HttpFoundation\Request;

class Tools
{
    /**
     * @param string $url
     * @return string
     */
    public static function addSlashAtRight( $url )
    {
        return '/' . ltrim( $url, '/' );
    }

    /**
     * check if the pile is for the request. handles the next pile if not matched.
     *
     * @param string|\Closure $url
     * @param Request $request
     * @return bool
     */
    public static function matcher( $url, Request $request )
    {
        if( !$url ) return true;
        $path = $request->getPathInfo();
        if( $url instanceof \Closure ) {
            return $url( $path );
        }
        if( stripos( $path, $url ) === 0 ) return true;
        return false;
    }

}