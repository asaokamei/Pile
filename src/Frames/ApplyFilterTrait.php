<?php
namespace WScore\Pile\Frames;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait ApplyFilterTrait
{
    /**
     * @var string|\Closure
     */
    protected $_filters = [];

    /**
     * @param string|\Closure $filter
     */
    public function setFilter( $filter )
    {
        $this->_filters[] = $filter;
    }

    /**
     * @param Request $request
     * @return Response|null
     */
    protected function applyFilters( $request )
    {
        $app = \WScore\Pile\App( $request );
        $response = null;
        foreach( $this->_filters as $filter ) {
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