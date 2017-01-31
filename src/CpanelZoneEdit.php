<?php

namespace Swalker\Cpanel;


use Illuminate\Support\Collection;

class CpanelZoneEdit extends CpanelFunction
{
    
    function __construct(Cpanel $cpanel, $domain)
    {
        parent::__construct($cpanel);
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

//        dd($response);
        return $this->collectZones($response);
    }
    
    /**
     * Adiciona uma nova zona dns
     *
     * @param ZonaDNS $zona
     *
     * @return Collection
     * @throws \Exception
     */
    public function store(ZonaDNS $zona)
    {
        if ((new Cpanel())->zoneEdit()->filter($zona->name, $zona->domain)->fetch()->count()) {
            throw new \Exception("Zona " . $zona->name . "." . $zona->domain . " já existe");
        }
        
        $this->cpanel->mergeFields([
            'cpanel_jsonapi_func' => 'add_zone_record',
            'domain'              => $zona->domain,
            'type'                => isset($zona->type) ? $zona->type : 'A',
            'name'                => $zona->name,
            'address'             => isset($zona->address) ? $zona->address : request()->ip(),
        ]);
    
        $response = $this->getApiData();
        if ($response->cpanelresult->data[0]->result->status == 0) {
            throw new \Exception("Erro ao tentar adicionar Zona");
        }
        
        return (new Cpanel())->zoneEdit()->filter($zona->name, $zona->domain)->fetch();
    }
    
    public function update(ZonaDNS $zona)
    {
        if ( ! $zona->line) {
            throw new \Exception("Objeto inválido, para atualizar é necessário o campo \"line\".");
        }
        $campos = [];
        
        foreach ($zona as $key => $value) {
            $campos[$key] = $value;
        }
        $campos['cpanel_jsonapi_func'] = 'edit_zone_record';
        
        $this->cpanel->mergeFields($campos);
        
        $response = $this->getApiData();
        
        if ($response->cpanelresult->data[0]->result->status == 0) {
            throw new \Exception("Erro ao tentar modificar Zona");
        }
        
        return $zona;
    }
    
    public function destroy(ZonaDNS $zona)
    {
        if ( ! $zona->line) {
            throw new \Exception("Objeto inválido, para atualizar é necessário o campo \"line\".");
        }
        
        $this->cpanel->mergeFields([
            'cpanel_jsonapi_func' => 'remove_zone_record',
            'domain'              => $zona->domain,
            'type'                => $zona->type,
            'line'                => $zona->line,
        ]);
        
        $response = $this->getApiData();
        if ($response->cpanelresult->data[0]->result->status == 0) {
            throw new \Exception("Erro ao tentar remover Zona");
        }
        
        return true;
    }
    
    /**
     * Filtra a busca de acordo com os parametros informados
     * a busca fica "$nome.$dominio." por exemplo "users.exemplo.com."
     *
     * @param $name
     * @param $domain
     *
     * @return $this
     */
    public function filter($name, $domain)
    {
        $this->cpanel->mergeFields([
            'name' => $name . '.' . $domain . '.',
        ]);
        
        return $this;
    }
    
    /**
     * @param $response
     *
     * @return Collection
     * @throws \Exception
     */
    private function collectZones($response): Collection
    {
        if ($response->cpanelresult->data[0]->status == 0) {
            throw new \Exception("Erro ao tentar obter coleção de zonas. Você deve informar um dominio para a pesquisa.");
        }
        
        $itens = $response->cpanelresult->data[0]->record;
        $zonas = new Collection();
        
        foreach ($itens as $item) {
            $item->name = str_replace('.' . $this->cpanel->fields['domain'] . '.', '', $item->name);
            $item->domain = $this->cpanel->fields['domain'];
            $zonas->push(
                new ZonaDNS($item)
            );
        }
        
        return $zonas;
    }
    
    
}