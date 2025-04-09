@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <x-input-label for="description" :value="__('Measure Description')" />
        <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required autofocus>{{ old('description', $measure->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="due_date" :value="__('Due Date (Optional)')" />
        <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date', isset($measure) ? $measure->due_date?->format('Y-m-d') : null)" />
        <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" :value="__('Status')" />
        @php
            $statusOptions = [
                ['value' => 'planned', 'label' => __('Planned')],
                ['value' => 'in_progress', 'label' => __('In Progress')],
                ['value' => 'completed', 'label' => __('Completed')],
            ];
        @endphp
        <x-select
            id="status"
            name="status"
            class="mt-1 block w-full"
            :options="$statusOptions"
            :selectedValue="old('status', $measure->status ?? 'planned')"
            required
        />
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="responsible_user_id" :value="__('Responsible User (Optional)')" />
        <x-select
            id="responsible_user_id"
            name="responsible_user_id"
            class="mt-1 block w-full"
            :options="$users"
            :selectedValue="old('responsible_user_id', $measure->responsible_user_id ?? null)"
            :searchable="true"
            :placeholder="__('Select User')"
            valueField="id"
            labelField="name"
        />
        <x-input-error :messages="$errors->get('responsible_user_id')" class="mt-2" />
    </div>
</div>

<div class="flex items-center justify-end mt-6">
    <x-primary-button class="ms-4">
        {{ isset($measure) ? __('Update Measure') : __('Save Measure') }}
    </x-primary-button>
</div>
