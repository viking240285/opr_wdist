<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Add settings properties for risk scales and categories
        $this->migrator->add('risk.probability_scale', []); // Default to empty array
        $this->migrator->add('risk.severity_scale', []);
        $this->migrator->add('risk.exposure_scale', []);
        $this->migrator->add('risk.risk_categories', []);
    }

    // Optional: Define down method if needed
    public function down(): void
    {
        $this->migrator->delete('risk.probability_scale');
        $this->migrator->delete('risk.severity_scale');
        $this->migrator->delete('risk.exposure_scale');
        $this->migrator->delete('risk.risk_categories');
    }
};
