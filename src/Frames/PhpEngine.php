<?php
namespace WScore\Pile\Frames;

class PhpEngine implements TemplateInterface
{

    /**
     * renders a $file with $data as input
     *
     * @param string $file
     * @param array  $data
     * @throws \Exception
     * @return string
     */
    public function render( $file, $data )
    {
        extract($data);
        try {

            ob_start();
            include($file);
            return ob_get_clean();

        } catch( \Exception $e ) {
            ob_end_clean();
            throw $e;
        }
    }
}