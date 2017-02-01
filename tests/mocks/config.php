<?php

function config($str)
{
    $str = str_replace('cpanel.', '', $str);
    
    $configs = include "./config/cpanel.php";
    if (key_exists($str, $configs)) {
        return $configs[$str];
    }
    
    throw new Exception("nope");
}

class appmock
{
    
    /**
     * @var $cpanel \Swalker2\Cpanel\Cpanel
     */
    public static $cpanel = null;
    
    public function make($class)
    {
        if (static::$cpanel === null) {
            static::$cpanel = new $class;
        }
        
        return static::$cpanel;
    }
}

function app()
{
    return new appmock();
}

function env($key, $default = null)
{
    if (file_exists(__DIR__ . "/../../.env")) {
        $dotenv = new \Dotenv\Dotenv(__DIR__ . "/../../");
        $dotenv->load();
    }
    
    $value = getenv($key);
    
    if ($value === false) {
        return value($default);
    }
    
    return $value;
}