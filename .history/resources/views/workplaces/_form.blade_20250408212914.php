@csrf

{{-- Hidden field for organization_id might be needed if not directly nested --}}
{{-- <input type="hidden" name="organization_id" value="{{ $organization->id }}"> --}}

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <x-bladewind::input
        name="name"
        label="{{ __('Workplace Name') }}"
        required="true"
        value="{{ old('name', $workplace->name ?? null) }}" />

    <x-bladewind::input
        name="code"
        label="{{ __('Workplace Code (Optional)') }}"
        value="{{ old('code', $workplace->code ?? null) }}" />

    <x-bladewind::select
        name="department_id"
        label="{{ __('Department') }}"
        required="true"
        :data="$departments ?? []" {{-- Pass available departments from controller --}}
        selectedValue="{{ old('department_id', $workplace->department_id ?? null) }}"
        searchable="true"
        placeholder="{{ __('Select Department') }}"
    />

    <x-bladewind::select
        name="position_id"
        label="{{ __('Position') }}"
        required="true"
        :data="$positions ?? []" {{-- Pass available positions from controller (dynamic based on dept?) --}}
        selectedValue="{{ old('position_id', $workplace->position_id ?? null) }}"
        searchable="true"
        placeholder="{{ __('Select Position') }}"
    />
    {{-- Note: Position dropdown might need to be dynamically updated based on selected Department using JS --}}

</div>

<div class="mt-6 text-right">
    <x-bladewind::button
        can_submit="true"
        name="save-wp">
        {{ isset($workplace) ? __('Update Workplace') : __('Save Workplace') }}
    </x-bladewind::button>
</div>
