<x-ref="hiddenSelect">
    >
        {{-- Пустая опция для placeholder в одиночном выборе (только если НЕ обязательно) --}}
        @if(!$multiple && $placeholder && !$required)
            <option value=""></option>
        @endif
        {{-- Опции дублируются здесь для корректной отправки формы --}}
        <template x-for="option in options" :key="option.value">
</x-ref>
