<?php

namespace Swalker\Cpanel;


class CpanelSSL extends CpanelFunction
{
    
    function __construct(Cpanel $cpanel)
    {
        parent::__construct($cpanel);
        $this->cpanel->mergeFields([
            'cpanel_jsonapi_module' => 'SSL',
        ]);
    }
    
    public function fetch($domain)
    {
        $this->cpanel->mergeFields([
            'cpanel_jsonapi_module' => 'SSLInfo',
            'cpanel_jsonapi_func'   => 'fetchinfo',
            'domain'                => $domain,
        ]);
        
        
        $response = $this->getApiData();
        
        dd($response);
    }
    
    public function install($domain, $cabundle, $crt, $key)
    {
        $this->cpanel->mergeFields([
            'cpanel_jsonapi_func' => 'installssl',
            'cabundle'            => $cabundle,
            'crt'                 => $crt,
            'domain'              => $domain,
            'key'                 => $key,
        ]);
        
        $response = $this->getApiData();
        dd($response);
    }
}