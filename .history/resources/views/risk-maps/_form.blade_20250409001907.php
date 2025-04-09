@csrf

{{-- Hidden field for workplace_id --}}
<input type="hidden" name="workplace_id" value="{{ $workplace->id }}">

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Дата оценки --}}
    <div>
        <x-input-label for="assessment_date" :value="__('Дата оценки')" />
        <x-text-input id="assessment_date" class="block mt-1 w-full" type="date" name="assessment_date" :value="old('assessment_date', $riskMap->assessment_date?->format('Y-m-d') ?? now()->format('Y-m-d'))" required />
        <x-input-error :messages="$errors->get('assessment_date')" class="mt-2" />
    </div>

    {{-- Статус карты --}}
    <div>
        <x-input-label for="status" :value="__('Статус карты')" />
        @php
            $statusOptions = [
                ['value' => 'draft', 'label' => __('Черновик')],
                ['value' => 'completed', 'label' => __('Завершено')],
                ['value' => 'archived', 'label' => __('Архивировано')],
            ];
        @endphp
        <x-select
            id="status"
            name="status"
            class="mt-1 block w-full"
            :options="$statusOptions"
            :selectedValue="old('status', $riskMap->status ?? 'draft')"
            required
        />
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    {{-- Члены комиссии --}}
    <div class="md:col-span-2">
        <x-input-label for="commission_members" :value="__('Члены комиссии (JSON или текст)')" />
        <textarea id="commission_members"
                  name="commission_members"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm font-mono text-sm"
                  placeholder='[{{ "name": "Иванов И.И.", "position": "Инженер по ОТ" }}, ...] или просто текстом'
                  rows="4"
                  >{{ old('commission_members', isset($riskMap) && is_array($riskMap->commission_members) ? json_encode($riskMap->commission_members, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($riskMap->commission_members ?? '')) }}</textarea>
        <x-input-error :messages="$errors->get('commission_members')" class="mt-2" />
    </div>

    {{-- Участники --}}
    <div class="md:col-span-2">
        <x-input-label for="participants" :value="__('Участники (необязательно, JSON или текст)')" />
        <textarea id="participants"
                  name="participants"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm font-mono text-sm"
                  placeholder='[{{ "name": "Петров П.П.", "position": "Работник" }}] или просто текстом'
                  rows="3"
                  >{{ old('participants', isset($riskMap) && is_array($riskMap->participants) ? json_encode($riskMap->participants, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($riskMap->participants ?? '')) }}</textarea>
        <x-input-error :messages="$errors->get('participants')" class="mt-2" />
    </div>

     {{-- conducted_by_user_id будет установлен автоматически в контроллере --}}

</div>

<div class="flex items-center justify-end mt-6">
    <x-primary-button class="ms-4">
        {{ isset($riskMap) ? __('Обновить детали карты') : __('Создать карту рисков') }}
    </x-primary-button>
</div>
