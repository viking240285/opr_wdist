@props([
    'options' => [],
    'selected' => null, // Может быть одиночным значением или массивом для multiple
    'multiple' => false,
    'placeholder' => __('Выберите...'),
    'required' => false,
    'disabled' => false,
    'searchable' => false,
    'id' => uniqid('select-'),
    'name' => '',
    'valueField' => 'value', // Поле для значения опции
    'labelField' => 'label', // Поле для отображения опции
])

@php
    // 1. Готовим ТОЛЬКО данные в PHP
    $initialOptions = collect($options)->map(function ($option) use ($valueField, $labelField) {
        $value = null;
        $label = null;

        if (is_array($option)) {
            $value = $option[$valueField] ?? null;
            $label = $option[$labelField] ?? $value; // Используем value как fallback label
        } elseif (is_object($option)) {
            $value = $option->$valueField ?? null;
            $label = $option->$labelField ?? $value;
        } else { // Скалярное значение
            $value = $option;
            $label = $option;
        }
        // Пропускаем опции без значения
        if ($value === null) {
            return null;
        }
        // Приводим к строке для консистентности сравнения в JS
        return ['value' => (string) $value, 'label' => (string) $label];

    })->filter()->values()->all(); // filter() без аргументов удалит null

    $initialSelected = $selected;
    // Приводим выбранные значения к строкам для сравнения с опциями в JS
    if ($multiple && is_array($initialSelected)) {
        $initialSelected = array_map('strval', $initialSelected);
    } elseif (!$multiple && $initialSelected !== null) {
        // --- ИСПРАВЛЕНИЕ: Обрабатываем массив для single select ---
        if (is_array($initialSelected)) {
            // Если для одиночного выбора передан массив, берем первый элемент
            $firstValue = reset($initialSelected); // reset() возвращает первый элемент или false
            $initialSelected = ($firstValue !== false) ? (string) $firstValue : null;
        } else {
             // Иначе просто приводим к строке
            $initialSelected = (string) $initialSelected;
        }
        // --- Конец исправления ---
    } elseif ($multiple && !is_array($initialSelected)) {
        // Если multiple, но передано не массив, инициализируем пустым массивом
         $initialSelected = [];
    } else {
        // Убедимся, что initialSelected - это массив для multiple или строка/null для single
        $initialSelected = $multiple ? (is_array($initialSelected) ? array_map('strval', $initialSelected) : [])
                                   : ($initialSelected !== null ? (string) $initialSelected : null);
    }

    // Данные для инициализации Alpine
    $alpineInitData = [
        'options' => $initialOptions,
        'initialSelected' => $initialSelected,
        'multiple' => $multiple,
        'placeholder' => $placeholder,
        'searchable' => $searchable,
        'id' => $id,
        'name' => $name,
        'required' => $required, // Передаем required в Alpine
    ];

    // Классы Tailwind (оставляем в PHP для удобства)
    $defaultClasses = 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';
    $buttonClasses = 'relative block w-full text-left px-3 py-2 cursor-pointer ' . $defaultClasses . ($disabled ? ' bg-gray-100 dark:bg-gray-800 cursor-not-allowed opacity-75' : '');
    $dropdownClasses = 'absolute z-20 mt-1 w-full bg-white dark:bg-gray-900 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm';
    $optionClasses = 'cursor-pointer select-none relative py-2 pl-3 pr-9 text-gray-900 dark:text-gray-300 hover:bg-indigo-500 dark:hover:bg-indigo-600 hover:text-white';
    $selectedOptionClasses = 'font-semibold bg-indigo-600 text-white';
    $searchClasses = 'block w-full px-3 py-2 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm sticky top-0 z-10'; // sticky top-0
@endphp

{{-- 2. Определяем всю логику Alpine в x-data --}}
<div x-data="selectComponent({{ json_encode($alpineInitData) }})"
     x-init="init()"
     @click.outside="open = false"
     class="relative {{ $attributes->get('class') }}">

    {{-- Скрытый select для отправки данных формы --}}
    <select
        :id="'hidden-' + id"
        :name="name + (multiple ? '[]' : '')"
        :multiple="multiple"
        class="hidden"
        x-ref="hiddenSelect" {{-- Добавляем ref --}}
        {{-- Убран required --}}
    >
        {{-- Пустая опция для placeholder в одиночном выборе (управляется Alpine) --}}
        <template x-if="!multiple && placeholder && !required">
            <option value=""></option>
        </template>
        {{-- Опции дублируются здесь для корректной отправки формы --}}
        <template x-for="option in options" :key="option.value">
            <option :value="option.value" x-text="option.label" :selected="isSelected(option.value)"></option>
        </template>
    </select>

    {{-- Видимая кнопка-селект --}}
    <button
        type="button"
        @click="toggleDropdown()"
        :class="{ '{{ $disabled ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed opacity-75' : '' }}': {{ $disabled ? 'true': 'false' }} }"
        class="{{ $buttonClasses }}"
        aria-haspopup="listbox"
        :aria-expanded="open"
        :aria-labelledby="'listbox-label-' + id"
        :disabled="{{ $disabled ? 'true': 'false' }}"
    >
        {{-- Отображение выбранных значений --}}
        <span class="block truncate">
             <template x-if="multiple && selectedValues.length > 0">
                 <span x-text="getSelectedLabels().join(', ')"></span>
             </template>
             <template x-if="!multiple && selectedValue !== null">
                 <span x-text="getSelectedLabel()"></span>
             </template>
             <template x-if="(multiple && selectedValues.length === 0) || (!multiple && selectedValue === null)">
                 <span class="text-gray-500 dark:text-gray-400" x-text="placeholder"></span>
             </template>
        </span>

        {{-- Иконка стрелки --}}
        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
            {{-- Иконка chevron-up-down из Breeze --}}
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M10 3a.75.75 0 01.53.22l3.75 3.75a.75.75 0 01-1.06 1.06L10 5.06 6.78 8.28a.75.75 0 01-1.06-1.06l3.75-3.75A.75.75 0 0110 3zm-3.72 9.72a.75.75 0 011.06 0L10 14.94l2.66-2.66a.75.75 0 111.06 1.06l-3.25 3.25a.75.75 0 01-1.06 0L6.28 13.78a.75.75 0 010-1.06z" clip-rule="evenodd" />
            </svg>
        </span>
    </button>

    {{-- Выпадающий список --}}
    <div
        x-show="open"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="{{ $dropdownClasses }}"
        style="display: none;" {{-- Начальное состояние скрыто, управляется Alpine --}}
        @keydown.escape.prevent.stop="closeDropdown()"
        @keydown.tab="closeDropdown()"
        @keydown.enter.prevent.stop="selectActiveOption()"
        @keydown.arrow-up.prevent.stop="focusPreviousOption()"
        @keydown.arrow-down.prevent.stop="focusNextOption()"
        x-ref="dropdown"
        tabindex="-1" {{-- Для обработки keydown --}}
        role="listbox"
        :aria-labelledby="'listbox-label-' + id"
    >
        {{-- Поиск --}}
        <div x-show="searchable" class="{{ $searchClasses }}"> {{-- Убрали li, добавили стили обертке --}}
            <input
                type="text"
                x-model.debounce.300ms="search"
                placeholder="{{ __('Поиск...') }}"
                class="w-full border-0 focus:ring-0 focus:outline-none bg-transparent dark:text-gray-300 p-0" {{-- Убрали паддинги, т.к. они в $searchClasses --}}
                @click.stop
                x-ref="searchInput"
            />
        </div>

        {{-- Список опций --}}
        <ul class="max-h-[18rem] overflow-y-auto" x-ref="listbox"> {{-- Добавили x-ref --}}
            <template x-for="(option, index) in filteredOptions" :key="option.value">
                <li
                    :id="id + '-option-' + index"
                    @click="toggleOption(option.value)"
                    @mouseenter="activeIndex = index"
                    @mouseleave="activeIndex = null"
                    :class="['{{ $optionClasses }}', { '{{ $selectedOptionClasses }}': isSelected(option.value), 'bg-indigo-100 dark:bg-indigo-700': activeIndex === index }]"
                    role="option"
                    :aria-selected="isSelected(option.value)"
                >
                    <span class="block truncate" :class="{ 'font-semibold': isSelected(option.value) }" x-text="option.label"></span>

                     {{-- Галочка для выбранных --}}
                     <span x-show="isSelected(option.value)" class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600 dark:text-white" :class="{ 'text-white': activeIndex === index && !isSelected(option.value), 'text-white': isSelected(option.value) }"> {{-- Уточнили цвет галочки --}}
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                          <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                        </svg>
                      </span>
                </li>
            </template>

            {{-- Сообщение, если ничего не найдено --}}
            <template x-if="filteredOptions.length === 0 && search.length > 0">
                <li class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Ничего не найдено') }}</li>
            </template>
             {{-- Сообщение, если опций нет вообще --}}
             <template x-if="options.length === 0 && filteredOptions.length === 0"> {{-- Уточнили условие --}}
                 <li class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Нет доступных опций') }}</li>
             </template>
        </ul>
    </div>
</div>

{{-- 3. Логика Alpine.js вынесена в отдельную функцию для читаемости --}}
<script>
    function selectComponent(initData) {
        return {
            // --- Данные из PHP ---
            options: initData.options || [],
            multiple: initData.multiple || false,
            placeholder: initData.placeholder || 'Выберите...',
            searchable: initData.searchable || false,
            id: initData.id,
            name: initData.name,
            initialSelected: initData.initialSelected, // Обрабатывается в PHP (без обработки массива для single)
            required: initData.required || false, // Добавлено

            // --- Состояние компонента ---
            open: false,
            search: '',
            selectedValues: [], // Массив для multiple=true
            selectedValue: null, // Строка/число для multiple=false
            activeIndex: null, // Индекс активной опции для навигации клавиатурой

            // --- Refs ---
            // Определяются через x-ref в шаблоне: hiddenSelect, searchInput, dropdown, listbox

            // --- Методы ---
            init() {
                // Инициализация выбранных значений
                if (this.multiple) {
                    this.selectedValues = Array.isArray(this.initialSelected)
                        ? this.initialSelected.filter(val => this.options.some(opt => String(opt.value) === String(val))) // Сравнение строк
                        : [];
                } else {
                     this.selectedValue = this.options.some(opt => String(opt.value) === String(this.initialSelected))
                        ? String(this.initialSelected) // Сохраняем как строку
                        : null; // Устанавливаем null, если значение не найдено или null
                }

                // Синхронизация скрытого селекта при инициализации
                this.$nextTick(() => this.updateHiddenSelect());

                 // Следим за изменением selectedValue/selectedValues и обновляем скрытый select
                 this.$watch('selectedValue', () => this.$nextTick(() => this.updateHiddenSelect()));
                 this.$watch('selectedValues', () => this.$nextTick(() => this.updateHiddenSelect()), { deep: true }); // deep для массивов
            },

            toggleDropdown() {
                if (this.open) {
                    return this.closeDropdown();
                }
                this.openDropdown();
            },

            openDropdown() {
                if(this.$el.attributes.disabled) return; // Не открывать, если disabled
                this.open = true;
                this.$nextTick(() => {
                    if (this.searchable) {
                         this.$refs.searchInput?.focus();
                    } else {
                         this.$refs.dropdown?.focus(); // Фокус на сам дропдаун для навигации
                    }
                    this.ensureActiveOptionIsVisible(); // Прокрутка к выбранной опции при открытии
                });
            },

            closeDropdown() {
                this.open = false;
                this.activeIndex = null; // Сбрасываем активный индекс
                this.search = ''; // Очищаем поиск при закрытии
            },

            isSelected(value) {
                const stringValue = String(value); // Сравнение строк
                if (this.multiple) {
                    return this.selectedValues.includes(stringValue);
                }
                return this.selectedValue === stringValue;
            },

            toggleOption(value) {
                 const stringValue = String(value); // Работаем со строками
                 if (this.multiple) {
                    const index = this.selectedValues.indexOf(stringValue);
                    if (index === -1) {
                        this.selectedValues.push(stringValue);
                    } else {
                        this.selectedValues.splice(index, 1);
                    }
                 } else {
                    // Если уже выбрано, не снимаем выбор (стандартное поведение select)
                    // Чтобы разрешить снятие выбора, можно добавить условие:
                    // this.selectedValue = this.isSelected(stringValue) ? null : stringValue;
                    this.selectedValue = stringValue;
                    this.closeDropdown(); // Закрываем после выбора в single-select
                 }
                 // Обновление скрытого поля не нужно здесь, т.к. есть $watch
            },

            getSelectedLabel() { // Только для single select
                 if (this.multiple || this.selectedValue === null) return this.placeholder;
                 const selectedOption = this.options.find(option => String(option.value) === this.selectedValue);
                 return selectedOption ? selectedOption.label : this.placeholder;
            },

            getSelectedLabels() { // Только для multiple select
                 if (!this.multiple || this.selectedValues.length === 0) return [];
                 return this.options
                           .filter(option => this.selectedValues.includes(String(option.value)))
                           .map(option => option.label);
            },

             // Обновляет <select> который будет отправлен с формой
             updateHiddenSelect() {
                const select = this.$refs.hiddenSelect;
                if (!select) return;

                // Очищаем текущие selected
                Array.from(select.options).forEach(option => {
                    // Не трогаем пустую опцию, если она есть
                    if (option.value !== "") {
                        option.selected = false;
                    }
                });


                // Устанавливаем новые selected
                if (this.multiple) {
                    this.selectedValues.forEach(val => {
                        // Ищем опцию безопасно, экранируя значение
                        const option = select.querySelector(`option[value="${CSS.escape(String(val))}"]`);
                        if (option) option.selected = true;
                    });
                } else if (this.selectedValue !== null) {
                     // Ищем опцию безопасно, экранируя значение
                    const option = select.querySelector(`option[value="${CSS.escape(String(this.selectedValue))}"]`);
                    if (option) {
                        option.selected = true;
                    } else {
                        // Если выбранное значение не найдено в опциях,
                        // но есть пустая опция (для !required), выбираем ее
                        const emptyOption = select.querySelector('option[value=""]');
                        if (emptyOption) {
                             emptyOption.selected = true;
                        }
                    }
                } else {
                     // Если selectedValue === null (и не multiple), выбираем пустую опцию, если она есть
                     const emptyOption = select.querySelector('option[value=""]');
                     if (emptyOption) {
                         emptyOption.selected = true;
                     }
                }

                // Триггерим событие change для совместимости (если нужно)
                // select.dispatchEvent(new Event('change', { bubbles: true }));
             },

             // --- Фильтрация и навигация ---
             get filteredOptions() {
                 if (!this.searchable || this.search === '') {
                     return this.options;
                 }
                 const searchLower = this.search.toLowerCase();
                 // Фильтруем и оставляем оригинальный тип данных value
                 return this.options.filter(
                     option => option.label.toLowerCase().includes(searchLower)
                 );
             },

             focusPreviousOption() {
                if (!this.open) return this.openDropdown(); // Открываем, если закрыто

                const optionsCount = this.filteredOptions.length;
                if (optionsCount === 0) return;

                let newIndex = this.activeIndex === null ? optionsCount - 1 : this.activeIndex - 1;
                if (newIndex < 0) newIndex = optionsCount - 1; // Зацикливание вверх

                this.activeIndex = newIndex;
                this.ensureActiveOptionIsVisible();
             },

             focusNextOption() {
                if (!this.open) return this.openDropdown(); // Открываем, если закрыто

                const optionsCount = this.filteredOptions.length;
                if (optionsCount === 0) return;

                 let newIndex = this.activeIndex === null ? 0 : this.activeIndex + 1;
                 if (newIndex >= optionsCount) newIndex = 0; // Зацикливание вниз

                 this.activeIndex = newIndex;
                 this.ensureActiveOptionIsVisible();
             },

             selectActiveOption() {
                if (!this.open || this.activeIndex === null) return;

                const activeOption = this.filteredOptions[this.activeIndex];
                if (activeOption) {
                    this.toggleOption(activeOption.value);
                }
             },

             ensureActiveOptionIsVisible() {
                if (this.activeIndex === null || !this.$refs.listbox) return;

                const optionElement = this.$refs.listbox.querySelector(`#${this.id}-option-${this.activeIndex}`);
                if (optionElement) {
                    optionElement.scrollIntoView({ block: 'nearest' });
                }
             }
        }
    }
</script>
