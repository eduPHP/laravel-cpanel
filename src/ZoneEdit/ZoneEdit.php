<?php

namespace Swalker2\Cpanel\ZoneEdit;

use Illuminate\Support\Collection;
use Swalker2\Cpanel\CpanelBaseModule;

class ZoneEdit extends CpanelBaseModule
{
    /**
     * @var
     */
    private $domain;

    public function __construct($domain)
    {
        parent::__construct();
        $this->domain = $domain;
        $this->cpanel->mergeFields([
            'cpanel_jsonapi_module' => 'ZoneEdit',
            'domain'                => $domain,
        ]);
    }

    public function fetch()
    {
        $this->cpanel->mergeFields([
            'cpanel_jsonapi_func' => 'fetchzone',
            'type'                => 'A',
            'customonly'          => '1',
        ]);

        $response = $this->getApiData();

        return $this->collectZones($response);
    }

    /**
     * Stores a new DNS Zone.
     *
     * @param Zone $zone
     *
     * @throws \Exception
     *
     * @return Collection
     */
    public function store(Zone $zone)
    {
        if ($this->cpanel->zoneEdit($zone->domain)->filter($zone->name)->fetch()->count()) {
            throw new \Exception('The Zone '.$zone->name.'.'.$zone->domain.' already exists');
        }

        $this->cpanel->mergeFields([
            'cpanel_jsonapi_func' => 'add_zone_record',
            'domain'              => $zone->domain,
            'type'                => isset($zone->type) ? $zone->type : 'A',
            'name'                => $zone->name,
            'address'             => isset($zone->address) ? $zone->address : request()->ip(),
        ]);

        $response = $this->getApiData();

        if ($response->data[0]->result->status == 0) {
            throw new \Exception('Error trying to insert new Zone');
        }

        return $this->cpanel->zoneEdit($zone->domain)->filter($zone->name)->fetch();
    }

    /**
     * @param Zone $zone
     *
     * @throws \Exception
     *
     * @return Zone
     */
    public function update(Zone $zone)
    {
        $this->verifyLineOrDifferentZoneExists($zone);

        $campos = [];

        foreach ($zone as $key => $value) {
            $campos[$key] = $value;
        }
        $campos['cpanel_jsonapi_func'] = 'edit_zone_record';

        $this->cpanel->mergeFields($campos);

        $response = $this->getApiData();

        if ($response->data[0]->result->status == 0) {
            throw new \Exception('Error trying to update DNS Zone.');
        }

        return $zone;
    }

    public function destroy(Zone $zone)
    {
        if (!$zone->line) {
            throw new \Exception('Invalid Object, the "line" property is neccessary while removing a Zone.');
        }

        $this->cpanel->mergeFields([
            'cpanel_jsonapi_func' => 'remove_zone_record',
            'domain'              => $zone->domain,
            'type'                => $zone->type,
            'line'                => $zone->line,
        ]);

        $response = $this->getApiData();

        if ($response->data[0]->result->status == 0) {
            throw new \Exception('Error trying to remove Zone.');
        }

        return true;
    }

    /**
     * Filtra a busca de acordo com os parametros informados
     * a busca fica "$nome.dominio." por exemplo "users.exemplo.com.".
     *
     * @param $name
     *
     * @return $this
     */
    public function filter($name)
    {
        $this->cpanel->mergeFields([
            'name' => $name.'.'.$this->domain.'.',
        ]);

        return $this;
    }

    /**
     * @param $response
     *
     * @throws \Exception
     *
     * @return Collection
     */
    private function collectZones($response)
    {
        if ($response->data[0]->status == 0) {
            throw new \Exception('Erro ao tentar obter coleção de zonas. Você deve informar um dominio para a pesquisa.');
        }

        $itens = $response->data[0]->record;
        $zones = new Collection();

        foreach ($itens as $item) {
            $item->name = str_replace('.'.$this->domain.'.', '', $item->name);
            $item->domain = $this->domain;
            $zones->push(
                new Zone($item)
            );
        }

        return $zones;
    }

    /**
     * @param Zone $zone
     *
     * @throws \Exception
     */
    private function verifyLineOrDifferentZoneExists(Zone $zone)
    {
        if (!$zone->line) {
            throw new \Exception('Invalid object, the "line" must be a valid line number.');
        }

        if ($existing = $this->cpanel->zoneEdit($zone->domain)->filter($zone->name)->fetch()->first()) {
            if ($existing->line != $zone->line) {
                throw new \Exception("Can't update Zone, the Zone ".$zone->name.'.'.$zone->domain.' already exists.');
            }
        }
    }
}
