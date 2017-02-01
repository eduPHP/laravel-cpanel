<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Swalker2\Cpanel\Cpanel;

abstract class BaseCpanel extends PHPUnit_Framework_TestCase
{
    
    /**
     * @var Cpanel
     */
    protected $cpanel;
    
    function __construct()
    {
        parent::__construct();
        
        $this->cpanel = make(Cpanel::class);
    }
    
    /**
     * returns the content of the given json file
     *
     * @param $string
     *
     * @return string
     */
    protected function getResponseFile($string)
    {
        $filename = str_replace('.','/',$string);
        $file = __DIR__ . "/mocks/responses/" . $filename . ".json";
        if (file_exists($file)) {
            return file_get_contents($file);
        }
        
        return "";
    }
    
    /**
     * @param $files
     *
     * @return MockHandler
     */
    protected function mockResponse($files = [])
    {
        if ( ! is_array($files)) {
            $files = [
                $files,
            ];
        }
        
        $responses = [];
        if (empty($files)) {
            $responses[] = new Response();
        } else {
            foreach ($files as $file) {
                $responses[] = new Response(
                    200,
                    [],
                    $this->getResponseFile($file)
                );
            }
        }
        
        return new MockHandler($responses);
    }
}