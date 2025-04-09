@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <x-bladewind::input
        name="code"
        label="{{ __('Hazard Code') }}"
        required="true"
        value="{{ old('code', $hazard->code ?? null) }}" />

     <x-bladewind::input
        name="type"
        label="{{ __('Hazard Type (Optional)') }}"
        value="{{ old('type', $hazard->type ?? null) }}" />

    <x-bladewind::textarea
        name="source"
        label="{{ __('Source of Hazard') }}"
        required="true"
        class="md:col-span-2"
        value="{{ old('source', $hazard->source ?? null) }}" />

    <x-bladewind::textarea
        name="threat"
        label="{{ __('Potential Threat/Consequence') }}"
        required="true"
        class="md:col-span-2"
        value="{{ old('threat', $hazard->threat ?? null) }}" />
</div>

<div class="mt-6 text-right">
    <x-bladewind::button
        can_submit="true"
        name="save-hazard">
        {{ isset($hazard) ? __('Update Hazard') : __('Save Hazard') }}
    </x-bladewind::button>
</div>
