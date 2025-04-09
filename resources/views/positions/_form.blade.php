@csrf

{{-- Скрытое поле department_id будет передано в контроллер через маршрут --}}
{{-- или можно оставить: <input type="hidden" name="department_id" value="{{ $department->id }}"> --}}

<div class="grid grid-cols-1 gap-6">
    {{-- Название должности --}}
    <div>
        <x-input-label for="name" :value="__('Название должности')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $position->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>
</div>

<div class="flex items-center justify-end mt-6">
    <x-primary-button class="ms-4">
        {{ isset($position) ? __('Обновить должность') : __('Сохранить должность') }}
    </x-primary-button>
</div>
