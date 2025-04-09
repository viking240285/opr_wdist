@csrf

{{-- Hidden field for department_id --}}
<input type="hidden" name="department_id" value="{{ $department->id }}">

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <x-bladewind::input
        name="name"
        label="{{ __('Position Name') }}"
        required="true"
        value="{{ old('name', $position->name ?? null) }}"
        class="md:col-span-2" />
</div>

<div class="mt-6 text-right">
    <x-bladewind::button
        can_submit="true"
        name="save-pos">
        {{ isset($position) ? __('Update Position') : __('Save Position') }}
    </x-bladewind::button>
</div>
