@props([
    'options' => [],
    'selected' => null,
    'multiple' => false,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'searchable' => false, // Пока не используется для JS, но принимаем атрибут
    'id' => uniqid('select-'), // Генерируем ID по умолчанию
])

@php
    // Убедимся, что selected - это массив, если multiple=true
    if ($multiple && !is_array($selected) && $selected !== null) {
        $selected = [$selected];
    } elseif ($multiple && $selected === null) {
        $selected = [];
    }

    // Классы по умолчанию из Breeze для input + кастомные
    $defaultClasses = 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm';

    // Если multiple=true, увеличим высоту по умолчанию
    $multipleClasses = $multiple ? 'h-32' : ''; // Можно настроить высоту
@endphp

<select
    id="{{ $id }}"
    name="{{ $attributes->get('name') }}"
    {{ $multiple ? 'multiple' : '' }}
    {{ $required ? 'required' : '' }}
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => $defaultClasses . ' ' . $multipleClasses . ' ' . $attributes->get('class')]) }}
>
    @if($placeholder && !$multiple)
        <option value="" {{ $selected === null || $selected === '' ? 'selected' : '' }} disabled>{{ $placeholder }}</option>
    @endif

    {{-- Перебор опций --}}
    @foreach ($options as $option)
        @php
            // Опция может быть массивом ['id' => ..., 'name' => ...] или просто строкой/числом
            $value = is_array($option) ? ($option['id'] ?? $option['value'] ?? null) : $option;
            $label = is_array($option) ? ($option['name'] ?? $option['label'] ?? $value) : $option;
            $isSelected = false;
            if ($multiple) {
                $isSelected = in_array($value, $selected);
            } else {
                $isSelected = ($selected !== null && $selected == $value);
            }
        @endphp
        @if($value !== null)
            <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endif
    @endforeach
</select>
