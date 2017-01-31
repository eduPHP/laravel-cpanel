<?php

namespace Swalker\Cpanel;


class ZonaDNS
{
    
    public $line;
    public $name;
    public $domain;
    public $address;
    public $record;
    public $type = 'A';
    public $ttl = '14400';
    public $class = 'IN';
    
    /**
     * ZonaDNS constructor.
     *
     * @param $item
     */
    public function __construct($item = null)
    {
        
        if (is_array($item)) {
            foreach ($item as $key => $value) {
                if (property_exists($this,$key)) {
                    $this->{$key} = $value;
                }
            }
        } elseif (is_object($item)) {
            $this->line = $item->line;
            $this->type = $item->type;
            $this->ttl = $item->ttl;
            $this->address = $item->address;
            $this->class = $item->class;
            $this->name = $item->name;
            $this->domain = $item->domain;
            $this->record = $item->record;
        }
    }
    
    public function update($newValues = [])
    {
        if (count($newValues)) {
            foreach ($newValues as $key => $value) {
                if (isset($this->{$key})) {
                    $this->{$key} = $value;
                }
            }
        }
        
        return (new Cpanel())
            ->zoneEdit()
            ->update($this);
    }
    
    public function destroy()
    {
        return (new Cpanel())
            ->zoneEdit()
            ->destroy($this);
    }
}