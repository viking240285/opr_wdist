@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-input-label for="code" :value="__('Код опасности')" />
        <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code', $hazard->code ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="type" :value="__('Тип опасности (необязательно)')" />
        <x-text-input id="type" class="block mt-1 w-full" type="text" name="type" :value="old('type', $hazard->type ?? '')" />
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="source" :value="__('Источник опасности')" />
        <textarea id="source" name="source" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('source', $hazard->source ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('source')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="threat" :value="__('Потенциальная угроза/последствие')" />
        <textarea id="threat" name="threat" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>{{ old('threat', $hazard->threat ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('threat')" class="mt-2" />
    </div>
</div>

<div class="flex items-center justify-end mt-6">
    <x-primary-button class="ms-4">
        {{ isset($hazard) ? __('Обновить опасность') : __('Сохранить опасность') }}
    </x-primary-button>
</div>
