<?php

namespace Swalker2\Cpanel\SSL;

use Swalker2\Cpanel\CpanelBaseModule;

/**
 * TODO: make it work and write tests... or write tests and make it work...
 * Class CpanelSSL.
 */
class CpanelSSL extends CpanelBaseModule
{
    public function __construct()
    {
        parent::__construct();
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
