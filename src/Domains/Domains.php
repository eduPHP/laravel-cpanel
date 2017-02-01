<?php

namespace Swalker2\Cpanel\Domains;


use Swalker2\Cpanel\CpanelBaseModule;

class Domains extends CpanelBaseModule
{
    public function fetch()
    {
        $this->cpanel->mergeFields([
            'cpanel_jsonapi_module' => 'DomainLookup',
            'cpanel_jsonapi_func'   => 'getbasedomains',
        ]);
        $response = $this->getApiData();
        
    }
    
    public function fetchSubDomains($domain='')
    {
        $this->cpanel->mergeFields([
            'cpanel_jsonapi_module' => 'SubDomain',
            'cpanel_jsonapi_func'   => 'listsubdomains',
            'regex' => $domain,
        ]);
        $response = $this->getApiData();
        
    }
    
}