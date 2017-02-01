<?php

namespace Swalker2\Cpanel;


use GuzzleHttp\Client;

class CpanelFunction
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
        
        return $apiData;
    }
    
    /**
     * Adiciona handler para fins de teste
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
        if (isset($cpanelresult->error)) {
            throw new \Exception($cpanelresult->error);
        }
        
        if (isset($cpanelresult->data[0]->status)) {
            if ($cpanelresult->data[0]->status == 0) {
                if (preg_match('/permission to read the zone/', $cpanelresult->data[0]->statusmsg)) {
                    throw new \Exception("You don't have permissions to read data from this domain");
                }
                
                throw new \Exception($cpanelresult->data[0]->statusmsg);
            }
        }
    }
    
}