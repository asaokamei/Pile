<?php
namespace WScore\Pile\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait ApplyFilterTrait
{
    /**
     * @var string|\Closure
     */
    protected $filters;

    /**
     * @param string|\Closure $filter
     */
    public function setFilter( $filter )
    {
        $this->filters[] = $filter;
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    protected function applyFilters( $request )
    {
        $app = \WScore\Pile\App( $request );
        $response = null;
        foreach( $this->filters as $filter ) {
            if( is_string( $filter ) ) {
                $response = $app->filter( $filter, $request );
            }
            elseif( $filter instanceof \Closure ) {
                $response = $filter( $request );
            }
            if( $response ) return $response;
        }
        return null;
    }

}