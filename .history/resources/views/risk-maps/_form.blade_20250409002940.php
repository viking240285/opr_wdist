@csrf

{{-- Hidden field for workplace_id --}}
<input type="hidden" name="workplace_id" value="{{ $workplace->id }}">

@php
    // Вычисляем значения по умолчанию для сложных полей заранее
    $commissionDefault = '';
    if (isset($riskMap)) {
        $commissionDefault = is_array($riskMap->commission_members)
            ? json_encode($riskMap->commission_members, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : ($riskMap->commission_members ?? '');
    }

    $participantsDefault = '';
    if (isset($riskMap)) {
        $participantsDefault = is_array($riskMap->participants)
            ? json_encode($riskMap->participants, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : ($riskMap->participants ?? '');
    }

    // Определяем плейсхолдеры в PHP
    $commissionPlaceholder = __('[{ "name": "Иванов И.И.", "position": "Инженер по ОТ" }}, ...] или просто текстом');
    $participantsPlaceholder = __('[{ "name": "Петров П.П.", "position": "Работник" }}] или просто текстом');

    // Определяем значение по умолчанию для даты оценки
    $assessmentDateDefault = (isset($riskMap) ? $riskMap->assessment_date?->format('Y-m-d') : null) ?? now()->format('Y-m-d');
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Дата оценки --}}
    <div>
        <x-input-label for="assessment_date" :value="__('Дата оценки')" />
        {{-- Используем вычисленное значение по умолчанию --}}
        <x-text-input id="assessment_date" class="block mt-1 w-full" type="date" name="assessment_date" :value="old('assessment_date', $assessmentDateDefault)" required />
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

    {{-- Члены комиссии (Динамический список) --}}
    <div class="md:col-span-2 border-t pt-4 mt-4 border-gray-200 dark:border-gray-700"
         x-data="commissionManager({{ json_encode(old('commission_members', $riskMap->commission_members ?? [])) }}, {{ json_encode($commissionRoles) }})"
         x-init="init()">

        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">{{ __('Члены комиссии') }}</h3>

        {{-- Скрытое поле для отправки JSON --}}
        <input type="hidden" name="commission_members" :value="jsonMembers">

        {{-- Таблица членов комиссии --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mb-4">
                <thead>
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider w-1/4">{{ __('Роль') }}</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ФИО') }}</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Должность') }}</th>
                        <th class="px-2 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Действия') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(member, index) in members" :key="index">
                        <tr>
                            <td class="px-2 py-1 whitespace-nowrap">
                                {{-- Используем стандартный select, стилизованный под Breeze --}}
                                <select x-model="member.role" class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm text-sm">
                                    <option value="">{{ __('Выберите роль...') }}</option>
                                    <template x-for="(roleLabel, roleKey) in roles" :key="roleKey">
                                        <option :value="roleKey" x-text="roleLabel"></option>
                                    </template>
                                </select>
                            </td>
                            <td class="px-2 py-1 whitespace-nowrap">
                                <x-text-input x-model="member.name" class="mt-1 block w-full text-sm" type="text" placeholder="{{ __('ФИО') }}" />
                            </td>
                            <td class="px-2 py-1 whitespace-nowrap">
                                <x-text-input x-model="member.position" class="mt-1 block w-full text-sm" type="text" placeholder="{{ __('Должность') }}" />
                            </td>
                            <td class="px-2 py-1 whitespace-nowrap text-right text-sm font-medium">
                                <button type="button" @click="removeMember(index)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="{{ __('Удалить') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                     <template x-if="members.length === 0">
                         <tr>
                             <td colspan="4" class="px-2 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                 {{ __('Члены комиссии не добавлены.') }}
                             </td>
                         </tr>
                     </template>
                </tbody>
            </table>
        </div>

        {{-- Кнопка Добавить --}}
        <div class="flex justify-end">
            <x-secondary-button type="button" @click="addMember">
                {{ __('Добавить члена комиссии') }}
            </x-secondary-button>
        </div>
         {{-- Выводим ошибку валидации для commission_members (например, если массив пуст, а он required) --}}
         <x-input-error :messages="$errors->get('commission_members')" class="mt-2" />
    </div>

    {{-- Участники --}}
    <div class="md:col-span-2">
        <x-input-label for="participants" :value="__('Участники (необязательно, JSON или текст)')" />
        <textarea id="participants"
                  name="participants"
                  class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm font-mono text-sm"
                  :placeholder="$participantsPlaceholder"
                  rows="3"
                  >{{ old('participants', $participantsDefault) }}</textarea>
        <x-input-error :messages="$errors->get('participants')" class="mt-2" />
    </div>

     {{-- conducted_by_user_id будет установлен автоматически в контроллере --}}

</div>

<div class="flex items-center justify-end mt-6">
    <x-primary-button class="ms-4">
        {{ isset($riskMap) ? __('Обновить детали карты') : __('Создать карту рисков') }}
    </x-primary-button>
</div>

{{-- Alpine.js логика для управления членами комиссии --}}
<script>
    function commissionManager(initialMembers, commissionRoles) {
        // Убедимся, что initialMembers это массив
        let initialData = [];
        if (typeof initialMembers === 'string' && initialMembers.length > 0) {
            try {
                initialData = JSON.parse(initialMembers);
                if (!Array.isArray(initialData)) initialData = [];
            } catch (e) {
                initialData = []; // Если JSON невалидный, начинаем с пустого списка
            }
        } else if (Array.isArray(initialMembers)) {
             initialData = initialMembers;
        }

        // Приводим к нужному формату { role: '', name: '', position: '' }
        initialData = initialData.map(member => ({
            role: member.role || '',
            name: member.name || '',
            position: member.position || ''
        }));

        return {
            members: initialData,
            roles: commissionRoles || {},
            jsonMembers: '', // Сюда будем писать JSON для отправки

            init() {
                this.updateJsonField(); // Обновляем скрытое поле при инициализации
                 // Следим за изменениями в массиве members и обновляем JSON
                 this.$watch('members', () => {
                    this.updateJsonField();
                 }, { deep: true });
            },

            addMember() {
                this.members.push({
                    role: '', // Роль по умолчанию
                    name: '',
                    position: ''
                });
            },

            removeMember(index) {
                this.members.splice(index, 1);
            },

            updateJsonField() {
                 // Удаляем пустые строки перед сохранением в JSON
                 const validMembers = this.members.filter(m => m.role || m.name || m.position);
                 this.jsonMembers = JSON.stringify(validMembers);
            }
        }
    }
</script>
