<?php
namespace WScore\Pile\Piles;

use WScore\Pile\Frames\TemplateInterface;

class PhpEngine implements TemplateInterface
{
    /**
     * @var
     */
    protected $dir;

    /**
     * @var string
     */
    public $extension = '.php';
    
    /**
     * @param $dir
     */
    public function __construct( $dir )
    {
        $dir .= substr($dir,-1)===DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR;
        $this->dir = $dir;
    }

    /**
     * @param $file
     * @return bool|string
     */
    protected function locate( $file )
    {
        $file .= substr($file, strlen($this->extension) )===$this->extension ? null: $this->extension;
        if( is_string( $this->dir ) ) {
            $file = $this->dir . $file;
        }
        if( $this->dir instanceof LocatorInterface ) {
            $file = $this->dir->locate( $file );
        }
        if( !file_exists($file) ) {
            throw new \RuntimeException( 'cannot locate a template file: '.$file );
        }
        return $file;
    }
    
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
            /** @noinspection PhpIncludeInspection */
            include( $this->locate($file) );
            return ob_get_clean();

        } catch( \Exception $e ) {
            ob_end_clean();
            throw $e;
        }
    }
    
}