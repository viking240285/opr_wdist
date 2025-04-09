@props([
    'options' => [],
    'selected' => null,
    'multiple' => false,
    'placeholder' => __('Выберите...'), // По умолчанию
    'required' => false,
    'disabled' => false,
    'searchable' => false,
    'id' => uniqid('select-'),
    'name' => '', // Имя должно передаваться
])

@php
    $alpineData = [
        'open' => false,
        'search' => '',
        'options' => collect($options)->map(function ($option) {
            // Определяем value и label, проверяя, массив ли $option
            $value = is_array($option) ? ($option['id'] ?? $option['value'] ?? null) : $option;
            $label = is_array($option) ? ($option['name'] ?? $option['label'] ?? $value) : $option; // Исправлено: $value -> $option, если не массив
             if (!is_array($option) && !is_string($option) && !is_numeric($option)) {
                 // Если опция - это объект или что-то еще, пытаемся получить id/value и name/label
                 $value = $option->id ?? $option->value ?? null;
                 $label = $option->name ?? $option->label ?? $value; // Используем $value как fallback
             }

            return [
                'value' => $value,
                'label' => $label,
            ];
        })->filter(function($o) { return $o['value'] !== null; })->values()->all(),
        'selectedValues' => [],
        'selectedValue' => null, // Для одиночного выбора
        'multiple' => $multiple,
        'placeholder' => $placeholder,
        'getFilteredOptions' => function() {
            return this.options.filter(
                option => option.label.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        'isSelected' => function($value) {
            if (this.multiple) {
                return this.selectedValues.includes(value);
            } else {
                return this.selectedValue === value;
            }
        },
        'toggleOption' => function($value) {
            if (this.multiple) {
                const index = this.selectedValues.indexOf(value);
                if (index === -1) {
                    this.selectedValues.push(value);
                } else {
                    this.selectedValues.splice(index, 1);
                }
            } else {
                this.selectedValue = this.isSelected(value) ? null : value;
                this.open = false; // Закрыть после выбора
            }
            // Обновляем скрытый селект
            this.updateHiddenSelect();
        },
        'getSelectedLabel' => function() {
            if (this.multiple) {
                if (this.selectedValues.length === 0) return this.placeholder;
                let labels = this.options
                                 .filter(option => this.selectedValues.includes(option.value))
                                 .map(option => option.label);
                return labels.join(', ') || this.placeholder;
                 // Можно ограничить количество отображаемых
                 // if (labels.length > 2) return labels.slice(0, 2).join(', ') + `, +${labels.length - 2}`;
                 // return labels.join(', ') || this.placeholder;
            } else {
                const selectedOption = this.options.find(option => option.value === this.selectedValue);
                return selectedOption ? selectedOption.label : this.placeholder;
            }
        },
        'removeSelected' => function(value) {
             if (this.multiple) {
                const index = this.selectedValues.indexOf(value);
                if (index !== -1) {
                    this.selectedValues.splice(index, 1);
                    this.updateHiddenSelect();
                }
             } // для одиночного не делаем
        },
        'updateHiddenSelect' => function() {
            // Эта функция будет использоваться для обновления скрытого select
            // который будет отправляться с формой
            const hiddenSelect = document.getElementById('hidden-{{ $id }}');
            if (!hiddenSelect) return;

            // Очищаем текущие выбранные опции
            Array.from(hiddenSelect.options).forEach(option => {
                option.selected = false;
            });

            // Выбираем нужные
            if (this.multiple) {
                 this.selectedValues.forEach(value => {
                    const option = hiddenSelect.querySelector(`option[value="${value}"]`);
                    if (option) option.selected = true;
                 });
            } else {
                 const option = hiddenSelect.querySelector(`option[value="${this.selectedValue}"]`);
                 if (option) option.selected = true;
            }
            // Генерируем событие change для совместимости
            hiddenSelect.dispatchEvent(new Event('change'));
        },
        'initSelect' => function() {
            // Инициализируем selectedValues/selectedValue из PHP
            let initialSelected = @json($selected);
             if (this.multiple) {
                 if (Array.isArray(initialSelected)) {
                    // Убедимся, что значения существуют в опциях
                    this.selectedValues = initialSelected.filter(val => this.options.some(opt => opt.value == val));
                 } else {
                    this.selectedValues = [];
                 }
            } else {
                // Убедимся, что значение существует в опциях
                this.selectedValue = this.options.some(opt => opt.value == initialSelected) ? initialSelected : null;
            }
            // Сразу обновим скрытый селект при инициализации
             this.$nextTick(() => { this.updateHiddenSelect(); });
        }
    ];

    // Классы по умолчанию из Breeze для input
    $defaultClasses = 'relative block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
    $buttonClasses = 'cursor-pointer w-full text-left px-3 py-2 ' . $defaultClasses . ($disabled ? ' bg-gray-100 dark:bg-gray-800 cursor-not-allowed' : '');
    $dropdownClasses = 'absolute z-10 mt-1 w-full bg-white dark:bg-gray-900 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm';
    $optionClasses = 'cursor-pointer select-none relative py-2 pl-3 pr-9 text-gray-900 dark:text-gray-300 hover:bg-indigo-500 dark:hover:bg-indigo-600 hover:text-white';
    $selectedOptionClasses = 'font-semibold bg-indigo-600 text-white'; // Класс для выбранной опции
    $searchClasses = 'block w-full px-3 py-2 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm';
@endphp

<div x-data='@json($alpineData)' x-init="initSelect()" class="relative {{ $attributes->get('class') }}">
    {{-- Скрытый select для отправки данных формы --}}
    <select
        id="hidden-{{ $id }}"
        name="{{ $name }}{{ $multiple ? '[]' : '' }}"
        {{ $multiple ? 'multiple' : '' }}
        class="hidden"
    >
        @if($placeholder && !$multiple)
            <option value=""></option>
        @endif
        @foreach ($alpineData['options'] as $option)
            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
        @endforeach
    </select>

    {{-- Видимая часть компонента --}}
    <button
        type="button"
        @click="open = !open"
        @click.away="open = false"
        :class="{ '{{ $disabled ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed' : '' }}': true }"
        class="{{ $buttonClasses }}"
        aria-haspopup="listbox"
        :aria-expanded="open"
        aria-labelledby="listbox-label-{{ $id }}"
        {{ $disabled ? 'disabled' : '' }}
    >
        <span class="block truncate" x-text="getSelectedLabel()"></span>
        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 3a.75.75 0 01.53.22l3.75 3.75a.75.75 0 01-1.06 1.06L10 5.06 6.78 8.28a.75.75 0 01-1.06-1.06l3.75-3.75A.75.75 0 0110 3zM10 17a.75.75 0 01-.53-.22l-3.75-3.75a.75.75 0 111.06-1.06L10 14.94l3.22-3.22a.75.75 0 111.06 1.06l-3.75 3.75A.75.75 0 0110 17z" clip-rule="evenodd" />
            </svg>
        </span>
    </button>

    {{-- Выпадающий список --}}
    <ul
        x-show="open"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="{{ $dropdownClasses }}"
        tabindex="-1"
        role="listbox"
        aria-labelledby="listbox-label-{{ $id }}"
        style="display: none;" {{-- Управляется Alpine --}}
    >
        {{-- Поиск (если searchable) --}}
        @if($searchable)
            <li> {{-- Оборачиваем инпут в li для структуры --}}
                <input
                    type="text"
                    x-model.debounce.300ms="search"
                    placeholder="{{ __('Поиск...') }}"
                    class="{{ $searchClasses }}"
                    @click.stop {{-- Предотвращаем закрытие списка при клике на поиск --}}
                />
            </li>
        @endif

        {{-- Опции --}}
        <template x-for="option in getFilteredOptions()" :key="option.value">
            <li
                @click="toggleOption(option.value)"
                class="{{ $optionClasses }}"
                :class="{ '{{ $selectedOptionClasses }}': isSelected(option.value) }"
                role="option"
                :aria-selected="isSelected(option.value)"
            >
                <span class="block truncate" :class="{ 'font-semibold': isSelected(option.value) }" x-text="option.label"></span>

                 {{-- Галочка для выбранных --}}
                 <span x-show="isSelected(option.value)" class="absolute inset-y-0 right-0 flex items-center pr-4">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                    </svg>
                  </span>
            </li>
        </template>

        {{-- Сообщение, если ничего не найдено --}}
        <template x-if="getFilteredOptions().length === 0 && search.length > 0">
            <li class="px-3 py-2 text-sm text-gray-500">{{ __('Ничего не найдено') }}</li>
        </template>
    </ul>
</div>
