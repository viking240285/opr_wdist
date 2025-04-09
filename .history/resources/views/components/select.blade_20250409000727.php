    {{-- Скрытый select для отправки данных формы --}}
    <select
        :id="'hidden-' + id"
        :name="name + (multiple ? '[]' : '')"
        :multiple="multiple"
        class="hidden"
        x-ref="hiddenSelect" {{-- Добавляем ref --}}
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
            initialSelected: initData.initialSelected,
            required: initData.required || false,

            // --- Состояние компонента ---
            open: false,
        }
    }
</script>
