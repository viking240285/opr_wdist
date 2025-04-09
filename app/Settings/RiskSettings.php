<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class RiskSettings extends Settings
{
    // Example: Fine-Kinney like scales (Value => Description)
    public array $probability_scale;
    public array $severity_scale;
    public array $exposure_scale;

    // Example: Categories (Upper Threshold => Name)
    // Assumes ascending order of thresholds
    public array $risk_categories;

    public static function group(): string
    {
        return 'risk'; // Group name for the settings table
    }

    // Provide default values
    protected function defaults(): void
    {
        $this->probability_scale = [
            0.1 => 'Almost impossible',
            0.2 => 'Very unlikely',
            0.5 => 'Unlikely',
            1 => 'Possible, but unusual',
            3 => 'Possible',
            6 => 'Likely',
            10 => 'Very likely',
        ];
        $this->severity_scale = [
            1 => 'Minor injury (first aid)',
            3 => 'Moderate injury (medical treatment)',
            7 => 'Serious injury (lost time)',
            15 => 'Very serious injury (permanent disability)',
            40 => 'Fatality',
            100 => 'Multiple fatalities / catastrophe',
        ];
        $this->exposure_scale = [
            0.5 => 'Very rarely',
            1 => 'Rarely (yearly)',
            2 => 'Occasionally (monthly)',
            3 => 'Sometimes (weekly)',
            6 => 'Regularly (daily)',
            10 => 'Continuously',
        ];
        $this->risk_categories = [
            // Threshold => Category Name
            20 => 'Acceptable',
            70 => 'Tolerable',
            200 => 'Moderate',
            400 => 'High',
            PHP_INT_MAX => 'Very High', // Use a large number for the highest category
        ];
    }
}
