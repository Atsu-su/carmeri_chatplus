<?php

namespace App\Messages;

class Session
{
    public static function exists($key)
    {
        return !empty(session($key)) ? session($key) : null;
    }
}