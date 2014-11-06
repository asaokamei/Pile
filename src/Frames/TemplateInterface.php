<?php
namespace WScore\Pile\Frames;

interface TemplateInterface
{
    /**
     * renders a $file with $data as input
     *
     * @param string $file
     * @param array  $data
     * @return string
     */
    public function render( $file, $data );
}