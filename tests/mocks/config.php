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

/**
 * @var $cpanel \Swalker2\Cpanel\Cpanel
 */
$cpanel = null;
function make($classe)
{
    global $cpanel;
    if ($cpanel === null) {
        $cpanel = new $classe;
    }
    
    return $cpanel;
}

function env($a,$b=''){
    return $b;
}