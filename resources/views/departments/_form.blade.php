@csrf

{{-- organization_id будет передано в контроллер через маршрут --}}
{{-- или можно оставить: <input type="hidden" name="organization_id" value="{{ $organization->id }}"> --}}

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Название отдела --}}
    <div>
        <x-input-label for="name" :value="__('Название отдела')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $department->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    {{-- Вышестоящий отдел --}}
    <div>
        <x-input-label for="parent_id" :value="__('Вышестоящий отдел')" />
        {{-- Предполагаем, что $parentDepartments - это коллекция объектов Department --}}
        <x-select
            id="parent_id"
            name="parent_id"
            class="mt-1 block w-full"
            :options="$parentDepartments ?? []"
            :selectedValue="old('parent_id', $department->parent_id ?? null)"
            searchable="true"
            placeholder="{{ __('Выберите вышестоящий отдел (необязательно)') }}"
            valueField="id" {{-- Указываем поля для <x-select> --}}
            labelField="name"
        />
        <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
    </div>
</div>

<div class="flex items-center justify-end mt-6">
    <x-primary-button class="ms-4">
        {{ isset($department) ? __('Обновить отдел') : __('Сохранить отдел') }}
    </x-primary-button>
</div>
