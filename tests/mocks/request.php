<?php

class mockrequest
{
    public function ip()
    {
        return $_SERVER['REMOTE_ADDR'] = '10.10.10.10';
    }
}

function request()
{
    return new mockrequest();
}
