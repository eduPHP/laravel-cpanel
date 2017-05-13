<?php

namespace Swalker2\Cpanel;

use Swalker2\Cpanel\Domains\Domains;
use Swalker2\Cpanel\Email\CpanelEmail;
use Swalker2\Cpanel\SSL\CpanelSSL;
use Swalker2\Cpanel\ZoneEdit\ZoneEdit;

class Cpanel
{
    public $url;
    public $fields;
    private $fallbackFields;

    /**
     * If testing, this will fake the responses.
     *
     * @var \GuzzleHttp\Handler\MockHandler
     */
    public $mock;

    public function __construct()
    {
        $this->url = config('cpanel.host').':'.config('cpanel.port');
        $this->cleanConfig();
    }

    /**
     * Retorna instãncia da classe ZoneEdit.
     *
     * @param string $domain dominio no formato example.com
     *
     * @throws \Exception
     *
     * @return ZoneEdit
     */
    public function zoneEdit($domain = '')
    {
        if (!$domain) {
            throw new \Exception('Domain name required');
        }

        return new ZoneEdit($domain);
    }

    public function ssl()
    {
        return new CpanelSSL();
    }

    public function email()
    {
        return new CpanelEmail();
    }

    public function domains()
    {
        return new Domains();
    }

    /**
     * Adiciona campos da array informada nas configurações.
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
