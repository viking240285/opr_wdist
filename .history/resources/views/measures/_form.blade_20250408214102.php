@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <x-bladewind::textarea
        name="description"
        label="{{ __('Measure Description') }}"
        required="true"
        class="md:col-span-2"
        value="{{ old('description', $measure->description ?? null) }}" />

    <x-bladewind::datepicker
        name="due_date"
        label="{{ __('Due Date (Optional)') }}"
        placeholder="YYYY-MM-DD"
        value="{{ old('due_date', isset($measure) ? $measure->due_date?->format('Y-m-d') : null) }}"
    />

     <x-bladewind::select
        name="status"
        label="{{ __('Status') }}"
        required="true"
        :data="[ // Define statuses here or pass from controller/config
            ['id' => 'planned', 'name' => __('Planned')],
            ['id' => 'in_progress', 'name' => __('In Progress')],
            ['id' => 'completed', 'name' => __('Completed')],
        ]"
        selectedValue="{{ old('status', $measure->status ?? 'planned') }}"
    />

    <x-bladewind::select
        name="responsible_user_id"
        label="{{ __('Responsible User (Optional)') }}"
        searchable="true"
        :data="$users ?? []" {{-- Pass users (id, name) from controller --}}
        selectedValue="{{ old('responsible_user_id', $measure->responsible_user_id ?? null) }}"
        placeholder="{{ __('Select User') }}"
        class="md:col-span-2"
    />
</div>

<div class="mt-6 text-right">
    <x-bladewind::button
        can_submit="true"
        name="save-measure">
        {{ isset($measure) ? __('Update Measure') : __('Save Measure') }}
    </x-bladewind::button>
</div>
