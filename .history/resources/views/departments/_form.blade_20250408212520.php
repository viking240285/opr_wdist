@csrf

{{-- Hidden field for organization_id --}}
<input type="hidden" name="organization_id" value="{{ $organization->id }}">

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <x-bladewind::input
        name="name"
        label="{{ __('Department Name') }}"
        required="true"
        value="{{ old('name', $department->name ?? null) }}" />

    <x-bladewind::select
        name="parent_id"
        label="{{ __('Parent Department') }}"
        :data="$parentDepartments ?? []" {{-- Pass available parent departments from controller --}}
        selectedValue="{{ old('parent_id', $department->parent_id ?? null) }}"
        searchable="true"
        placeholder="{{ __('Select Parent Department (Optional)') }}"
    />
</div>

<div class="mt-6 text-right">
    <x-bladewind::button
        can_submit="true"
        name="save-dept">
        {{ isset($department) ? __('Update Department') : __('Save Department') }}
    </x-bladewind::button>
</div>
