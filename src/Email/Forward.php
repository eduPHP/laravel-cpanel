<?php

namespace Swalker2\Cpanel\Email;


class Forward
{
    
    public $dest;
    public $forward;
    
    public $uri_forward;
    public $html_forward;
    public $html_dest;
    public $uri_dest;
    
    /**
     * Forward constructor.
     *
     * @param $item
     */
    public function __construct($item = null)
    {
        $this->setProperties($item);
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
        
        if (! $this->uri_forward && $this->forward){
            $this->uri_forward = str_replace('@','%40',$this->forward);
            $this->uri_dest = str_replace('@','%40',$this->dest);
            $this->html_forward = $this->forward;
            $this->html_dest = $this->dest;
        }
    }
}