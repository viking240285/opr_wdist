<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Карта оценки рисков для:') }} {{ $workplace->name }} ({{ $workplace->department->name }} / {{ $workplace->department->organization->name }})
            </h2>
            <div>
                {{-- Кнопка Назад к рабочим местам --}}
                @can('view', $workplace->department->organization)
                    <x-secondary-button tag="a" size="tiny" href="{{ route('organizations.workplaces.index', $workplace->department->organization) }}" class="mr-2">
                        {{ __('К списку рабочих мест') }}
                    </x-secondary-button>
                @endcan
                 {{-- Кнопка Редактировать Карту Рисков (если нужно) --}}
                 @can('update', $riskMap)
                    <x-secondary-button tag="a" size="tiny" href="{{ route('risk-maps.edit', $riskMap) }}">
                        {{ __('Редактировать карту') }}
                    </x-secondary-button>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Информация о Карте Рисков --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">{{ __('Детали карты оценки рисков') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div><span class="font-semibold">{{ __('Дата оценки:') }}</span> {{ $riskMap->assessment_date->format('d.m.Y') }}</div>
                        <div><span class="font-semibold">{{ __('Статус:') }}</span> {{ __(ucfirst($riskMap->status)) }}</div>
                        <div><span class="font-semibold">{{ __('Проведена:') }}</span> {{ $riskMap->conductedBy->name ?? 'N/A' }}</div>
                        {{-- TODO: Отобразить комиссию и участников, если нужно --}}
                        {{-- <div><span class="font-semibold">Комиссия:</span> ...</div> --}}
                        {{-- <div><span class="font-semibold">Участники:</span> ...</div> --}}
                    </div>
                </div>
            </div>

            {{-- Список Оценок Риска для этой Карты --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                         <h3 class="text-lg font-medium">{{ __('Оцененные риски') }}</h3>
                         {{-- Кнопка "Добавить оценку" видна тем, кто может создавать RiskAssessment --}}
                         @can('create', [App\Models\RiskAssessment::class, $workplace])
                            <x-primary-button tag="a" href="{{ route('risk-maps.assessments.create', $riskMap) }}">
                                {{ __('Добавить оценку риска') }}
                            </x-primary-button>
                         @endcan
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Опасность (Код)') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('В') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Т') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Э') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Риск (Р)') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Категория') }}</th>
                                    {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Меры') }}</th> --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Действия') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($assessments as $assessment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100" title="{{ $assessment->hazard->source ?? '' }}">
                                            {{ $assessment->hazard->code ?? 'N/A' }}
                                            {{-- <span class="text-xs text-gray-500">{{ Str::limit($assessment->hazard->source ?? '', 30) }}</span> --}}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $assessment->probability }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $assessment->severity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $assessment->exposure }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $assessment->calculated_risk }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $assessment->risk_category === 'Высокий' ? 'text-red-600 dark:text-red-400' : ($assessment->risk_category === 'Средний' ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400') }}">
                                            {{ __($assessment->risk_category) }}
                                        </td>
                                        {{-- TODO: Отобразить кратко меры? --}}
                                        {{-- <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">...</td> --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- Кнопка "Редактировать" оценку --}}
                                            @can('update', $assessment)
                                                <a href="{{ route('risk-assessments.edit', $assessment) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 mr-3">{{ __('Редакт.') }}</a>
                                            @endcan
                                            {{-- Кнопка "Удалить" оценку --}}
                                            @can('delete', $assessment)
                                                <form action="{{ route('risk-assessments.destroy', $assessment) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Вы уверены, что хотите удалить эту оценку риска?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">{{ __('Удалить') }}</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('Оценки рисков для этой карты еще не добавлены.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Пагинация для $assessments, если нужна --}}
                    {{-- <div class="mt-4">
                        {{ $assessments->links() }}
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
