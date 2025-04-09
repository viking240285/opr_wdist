@csrf

{{-- organization_id будет автоматически привязан через маршрут/контроллер --}}

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Название РМ --}}
    <div>
        <x-input-label for="name" :value="__('Название рабочего места')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $workplace->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    {{-- Код РМ --}}
    <div>
        <x-input-label for="code" :value="__('Код рабочего места (необязательно)')" />
        <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code', $workplace->code ?? '')" />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    {{-- Отдел --}}
    <div>
        <x-input-label for="department_id" :value="__('Отдел')" />
        <x-select
            id="department_id"
            name="department_id"
            class="mt-1 block w-full"
            :options="$departments ?? []" {{-- Ожидаем коллекцию объектов --}}
            :selectedValue="old('department_id', $workplace->department_id ?? null)"
            required
            searchable="true"
            placeholder="{{ __('Выберите отдел') }}"
            valueField="id"      {{-- Указываем поля для <x-select> --}}
            labelField="name"
        />
        <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
    </div>

    {{-- Должность --}}
    <div>
        <x-input-label for="position_id" :value="__('Должность')" />
        <x-select
            id="position_id"
            name="position_id"
            class="mt-1 block w-full"
            :options="$positions ?? []" {{-- Ожидаем коллекцию объектов --}}
            :selectedValue="old('position_id', $workplace->position_id ?? null)"
            required
            searchable="true"
            placeholder="{{ __('Выберите должность') }}"
            valueField="id"      {{-- Указываем поля для <x-select> --}}
            labelField="name"
        />
         {{-- Примечание: Динамическое обновление списка должностей при выборе отдела потребует JS --}}
        <x-input-error :messages="$errors->get('position_id')" class="mt-2" />
    </div>

</div>

<div class="flex items-center justify-end mt-6">
    <x-primary-button class="ms-4">
        {{ isset($workplace) ? __('Обновить рабочее место') : __('Сохранить рабочее место') }}
    </x-primary-button>
</div>
