<?php

namespace Swalker2\Cpanel;

use Swalker2\Cpanel\SSL\CpanelSSL;
use Swalker2\Cpanel\ZoneEdit\CpanelZoneEdit;

class Cpanel
{
    
    public $url;
    public $fields;
    private $fallbackFields;
    
    /**
     * If testing, this will fake the responses
     * @var \GuzzleHttp\Handler\MockHandler
     */
    public $mock;
    
    function __construct()
    {
        $this->url = config('cpanel.host') . ':' . config('cpanel.port');
        $this->cleanConfig();
    }
    
    /**
     * Retorna instãncia da classe ZoneEdit
     *
     * @param string $domain dominio no formato example.com
     *
     * @return CpanelZoneEdit
     * @throws \Exception
     */
    public function zoneEdit($domain = '')
    {
        if ( ! $domain) {
            throw new \Exception("Domain name required");
        }
        
        return new CpanelZoneEdit($domain);
    }
    
    public function ssl()
    {
        return new CpanelSSL();
    }
    
    /**
     * Adiciona campos da array informada nas configurações
     *
     * @param array $fields
     */
    public function mergeFields(array $fields)
    {
        $this->fallbackFields = $this->fields;
        
        if (is_array($fields)) {
            $this->fields = array_merge($this->fields, $fields);
        }
    }
    
    
    public function cleanConfig()
    {
        $this->fields = [
            'cpanel_jsonapi_user'       => config('cpanel.user'),
            'cpanel_jsonapi_apiversion' => '2',
            'cpanel_jsonapi_module'     => '',
            'cpanel_jsonapi_func'       => '',
        ];
    }
    
}