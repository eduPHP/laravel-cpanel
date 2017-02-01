<?php

namespace Swalker2\Cpanel;


use GuzzleHttp\Client;

abstract class CpanelBaseModule
{
    
    /**
     * @var Cpanel
     */
    protected $cpanel;
    protected $config = [];
    protected $mock;
    
    function __construct()
    {
        $this->cpanel = app()->make(Cpanel::class);
        $this->config['base_uri'] = $this->cpanel->url;
    }
    
    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getApiData()
    {
        if (getenv("APP_ENV") == 'testing') {
            $this->config['handler'] = $this->cpanel->mock;
        }
        
        $client = new Client($this->config);
        
        $response = $client->post('/json-api/cpanel', [
            'auth'        => [config('cpanel.user'), config('cpanel.pass')],
            'form_params' => $this->cpanel->fields,
        ]);

        $apiData = json_decode($response->getBody()->getContents());
        
        $this->cpanel->cleanConfig();
        
        $this->errorHandler($apiData->cpanelresult);
        
        return $apiData->cpanelresult;
    }
    
    /**
     * Add mock handler for testing purposes
     *
     * @param $mock \GuzzleHttp\Handler\MockHandler
     *
     * @return mixed
     */
    public function testHandler(\GuzzleHttp\Handler\MockHandler $mock)
    {
        if (getenv("APP_ENV") == 'testing') {
            $this->cpanel->mock = $mock;
        }
        
        return $this;
    }
    
    /**
     * throws an Exception if there is any error
     *
     * @param $cpanelresult
     *
     * @throws \Exception
     */
    protected function errorHandler($cpanelresult)
    {
        
        if (isset($cpanelresult->data[0]->status) || isset($cpanelresult->error)) {
            
            if (isset($cpanelresult->data[0]->status) && $cpanelresult->data[0]->status == 0) {
                if (preg_match('/permission to read the zone/', $cpanelresult->data[0]->statusmsg)) {
                    throw new \Exception("You don't have permissions to read data from this domain");
                }
                
                throw new \Exception($cpanelresult->data[0]->statusmsg);
            }
            
            if (isset($cpanelresult->error)) {
                if (preg_match('/Permission denied/', $cpanelresult->error)) {
                    throw new \Exception("You don't have permissions to execute this action.");
                }
                if (preg_match('/You do not have an email account named/', $cpanelresult->error)) {
                    throw new \Exception("You do not own this email account.");
                }
                
                throw new \Exception($cpanelresult->error);
            }
        }
    }
    
}