<?php

namespace Swalker\Cpanel;


use GuzzleHttp\Client;

class CpanelFunction
{
    
    /**
     * @var Cpanel
     */
    protected $cpanel;
    function __construct(Cpanel $cpanel)
    {
        $this->cpanel = $cpanel;
    }
    
    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getApiData()
    {
        $client = new Client([
            'base_uri' => $this->cpanel->url,
        ]);
        
        
        $response = $client->post('/json-api/cpanel', [
            'auth'        => [config('cpanel.user'), config('cpanel.pass')],
            'form_params' => $this->cpanel->fields,
        ]);
        $apiData = json_decode($response->getBody()->getContents());
        
        if (isset($apiData->cpanelresult->error))
            throw new \Exception($apiData->cpanelresult->error);
        
        return $apiData;
    }
}