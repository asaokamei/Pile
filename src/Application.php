<?php
namespace WScore\Pile;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application implements PileInterface
{
    /**
     * @var PileInterface
     */
    protected $head;

    /**
     * @var PileInterface
     */
    protected $tail;

    /**
     * Handles a Request to convert it to a Response.
     * start the pile of handlers.
     *
     * @param Request $request A Request instance
     * @param int     $type The type of the request
     *                          (one of HttpKernelInterface::MASTER_REQUEST or HttpKernelInterface::SUB_REQUEST)
     * @param bool    $catch    Whether to catch exceptions or not
     *
     * @return Response|null    A Response instance
     *
     * @throws \Exception When an Exception occurs during processing
     *
     * @api
     */
    public function handle( Request $request, $type = self::MASTER_REQUEST, $catch = true )
    {
        try {

            return $this->head->handle( $request, self::SUB_REQUEST, false );

        } catch( \Exception $e ) {
            if( $catch ) return new Response( $e->getMessage(), $e->getCode() );
            throw $e;
        }
    }

    /**
     * piles the handlers keeping the head and the tail of piles.
     * the handler piles up to the last pile.
     *
     * @param PileInterface $pile
     * @return Application
     */
    public function pile( $pile )
    {
        if( !$this->head ) {
            $this->head = $pile;
        }
        if( $this->tail ) {
            $this->tail->pile( $pile );
        }
        $this->tail = $pile;
        return $this;
    }
}