<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class RiskSettings extends Settings
{

    public static function group(): string
    {
        return 'default';
    }
}