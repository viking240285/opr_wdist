@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Название организации --}}
    <div>
        <x-input-label for="name" :value="__('Название организации')" />
        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $organization->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    {{-- ИНН --}}
    <div>
        <x-input-label for="inn" :value="__('ИНН')" />
        <x-text-input id="inn" class="block mt-1 w-full" type="text" name="inn" :value="old('inn', $organization->inn ?? '')" /> {{-- тип text, валидация в контроллере --}}
        <x-input-error :messages="$errors->get('inn')" class="mt-2" />
    </div>

    {{-- КПП --}}
    <div>
        <x-input-label for="kpp" :value="__('КПП')" />
        <x-text-input id="kpp" class="block mt-1 w-full" type="text" name="kpp" :value="old('kpp', $organization->kpp ?? '')" /> {{-- тип text, валидация в контроллере --}}
        <x-input-error :messages="$errors->get('kpp')" class="mt-2" />
    </div>

    {{-- Логотип (пока не реализовано) --}}
    {{-- <div class="md:col-span-2">
        <x-input-label for="logo" :value="__('Логотип')" />
        <x-text-input id="logo" type="file" name="logo" class="mt-1 block w-full" />
        <x-input-error :messages="$errors->get('logo')" class="mt-2" />
    </div> --}}

    {{-- Адрес --}}
    <div class="md:col-span-2">
        <x-input-label for="address" :value="__('Адрес')" />
        <textarea id="address" name="address" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('address', $organization->address ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('address')" class="mt-2" />
    </div>

</div>

<div class="flex items-center justify-end mt-6">
    <x-primary-button class="ms-4">
        {{ __('Сохранить организацию') }}
    </x-primary-button>
</div>
