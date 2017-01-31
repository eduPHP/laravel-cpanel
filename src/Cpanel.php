<?php

namespace App\Cpanel;

class Cpanel
{
    
    public $url;
    public $fields;
    
    function __construct()
    {
        $this->url = config('cpanel.host') . ':' . config('cpanel.port');
        $this->fields = [
            'cpanel_jsonapi_user'       => config('cpanel.user'),
            'cpanel_jsonapi_apiversion' => '2',
            'cpanel_jsonapi_module'     => '',
            'cpanel_jsonapi_func'       => '',
        ];
    }
    
    /**
     * Retorna instãncia da classe ZoneEdit
     *
     * @param string $domain dominio no formato example.com
     *
     * @return CpanelZoneEdit
     */
    public function zoneEdit($domain = 'rdo.blog.br')
    {
        return new CpanelZoneEdit($this, $domain);
    }
    
    public function ssl()
    {
        return new CpanelSSL($this);
    }
    
    /**
     * Adiciona campos da array informada nas configurações
     *
     * @param array $fields
     */
    public function mergeFields(array $fields)
    {
        if (is_array($fields)) {
            $this->fields = array_merge($this->fields, $fields);
        }
    }
    
}