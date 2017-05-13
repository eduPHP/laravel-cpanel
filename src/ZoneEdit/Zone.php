<?php

namespace Swalker2\Cpanel\ZoneEdit;

use Swalker2\Cpanel\Cpanel;

class Zone
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
        $this->setProperties($item);
    }

    public function update($newValues = [])
    {
        $this->setProperties($newValues);

        $cpanel = app()->make(Cpanel::class);

        return $cpanel
            ->zoneEdit($this->domain)
            ->update($this);
    }

    public function destroy()
    {
        $cpanel = app()->make(Cpanel::class);

        return $cpanel
            ->zoneEdit($this->domain)
            ->destroy($this);
    }

    /**
     * @param $item
     */
    private function setProperties($item)
    {
        if ($item != null) {
            foreach ($item as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
        }
    }
}
