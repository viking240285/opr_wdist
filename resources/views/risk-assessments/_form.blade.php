@csrf

{{-- Скрытое поле для risk_map_id --}}
<input type="hidden" name="risk_map_id" value="{{ $riskMap->id }}">

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
    {{-- Выбор Опасности --}}
    <div class="md:col-span-3">
        <x-input-label for="hazard_id" :value="__('Идентифицированная опасность')" />
        {{-- Убираем временный дамп --}}
        {{-- @dump($hazards ?? 'Переменная $hazards не задана') --}}
        <x-select id="hazard_id" name="hazard_id" class="mt-1 block w-full" required searchable placeholder="{{ __('Выберите опасность...') }}" :options="$hazards ?? []" :selected="old('hazard_id', $assessment->hazard_id ?? null)"
            valueField="id"
            labelField="name"
        />
        <x-input-error class="mt-2" :messages="$errors->get('hazard_id')" />
        {{-- TODO: Отображение деталей опасности при выборе (JS) --}}
        {{-- <div id="hazard-details" class="mt-2 text-sm text-gray-600 dark:text-gray-400"></div> --}}
    </div>

    {{-- Параметры риска --}}
    <div>
        <x-input-label for="probability" :value="__('Вероятность (В)')" />
        <x-text-input id="probability" name="probability" type="number" step="0.1" min="0" class="mt-1 block w-full" :value="old('probability', $assessment->probability ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('probability')" />
    </div>

    <div>
        <x-input-label for="severity" :value="__('Тяжесть (Т)')" />
        <x-text-input id="severity" name="severity" type="number" step="0.1" min="0" class="mt-1 block w-full" :value="old('severity', $assessment->severity ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('severity')" />
    </div>

     <div>
        <x-input-label for="exposure" :value="__('Экспозиция (Э)')" />
        <x-text-input id="exposure" name="exposure" type="number" step="0.1" min="0" class="mt-1 block w-full" :value="old('exposure', $assessment->exposure ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('exposure')" />
    </div>

    {{-- Рассчитанный риск (Отображение) - пока статично, можно добавить JS для динамики --}}
    @if(isset($assessment) && $assessment->calculated_risk !== null)
    <div class="md:col-span-3 mt-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-md">
        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Рассчитанный риск (Р = В * Т * Э)') }}: <span class="font-bold">{{ $assessment->calculated_risk }}</span></p>
        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('Категория риска') }}: <span class="font-bold {{ $assessment->risk_category === 'Высокий' ? 'text-red-600' : ($assessment->risk_category === 'Средний' ? 'text-yellow-600' : 'text-green-600') }}">{{ __($assessment->risk_category) }}</span></p>
    </div>
    @endif

    {{-- Выбор Мер Контроля --}}
    <div class="md:col-span-3 mt-6 border-t dark:border-gray-700 pt-6">
        <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">{{ __('Меры управления риском') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <x-input-label for="existing_measures" :value="__('Существующие меры')" />
                <x-select id="existing_measures" name="existing_measures[]" class="mt-1 block w-full" multiple searchable placeholder="{{ __('Выберите существующие меры...') }}" :options="$measures ?? []" :selected="old('existing_measures', isset($assessment) ? $assessment->existingMeasures->pluck('id')->toArray() : [])"
                    valueField="id"
                    labelField="description"
                />
                 <x-input-error class="mt-2" :messages="$errors->get('existing_measures')" />
            </div>
            <div>
                 <x-input-label for="planned_measures" :value="__('Планируемые меры')" />
                 <x-select id="planned_measures" name="planned_measures[]" class="mt-1 block w-full" multiple searchable placeholder="{{ __('Выберите планируемые меры...') }}" :options="$measures ?? []" :selected="old('planned_measures', isset($assessment) ? $assessment->plannedMeasures->pluck('id')->toArray() : [])"
                    valueField="id"
                    labelField="description"
                 />
                 <x-input-error class="mt-2" :messages="$errors->get('planned_measures')" />
            </div>
        </div>
         <div class="text-right mt-3">
             {{-- Кнопка "Добавить новую меру" видна тем, кто может создавать меры --}}
            @can('create', App\Models\Measure::class)
                <x-secondary-button type="button" tag="a" href="{{ route('measures.create') }}" target="_blank" size="sm">
                    {{ __('Добавить новую меру в справочник') }}
                </x-secondary-button>
            @endcan
        </div>
    </div>

</div>

<div class="mt-8 flex justify-end">
    <x-primary-button>
        {{ isset($assessment) ? __('Обновить оценку') : __('Сохранить оценку') }}
    </x-primary-button>
</div>
