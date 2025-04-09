<x-ref="hiddenSelect" {{-- Добавляем ref --}}
    >
        {{-- Пустая опция для placeholder в одиночном выборе (только если НЕ обязательно) --}}
        @if(!$multiple && $placeholder && !$required)
            <option value=""></option>
        @endif
        {{-- Опции дублируются здесь для корректной отправки формы --}}
        <template x-for="option in options" :key="option.value">
            <option :value="option.value" x-text="option.label" :selected="isSelected(option.value)"></option>
        </template>
    </select>
</x-ref>
