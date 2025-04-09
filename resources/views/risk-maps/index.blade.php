<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Карты рисков для рабочего места:') }} {{ $workplace->name }} ({{ $workplace->department->organization->name }})
            </h2>
             {{-- Кнопка Назад к РМ --}}
             @can('view', $workplace->department->organization)
                 <x-secondary-button tag="a" size="tiny" href="{{ route('organizations.workplaces.index', $workplace->department->organization) }}">
                    {{ __('К списку рабочих мест') }}
                 </x-secondary-button>
             @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-6">
                         <h3 class="text-lg font-medium">{{ __('Список карт оценки рисков') }}</h3>
                         {{-- Кнопка "Создать" видна тем, кто может создавать Карты Рисков --}}
                         @can('create', [App\Models\RiskMap::class, $workplace])
                            <x-primary-button tag="a" href="{{ route('workplaces.risk-maps.create', $workplace) }}">
                                {{ __('Создать новую карту рисков') }}
                            </x-primary-button>
                         @endcan
                    </div>

                     <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ID Карты') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Дата оценки') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Статус') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Проведена') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Действия') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($riskMaps as $riskMap)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{-- Ссылка на просмотр карты --}}
                                            @can('view', $riskMap)
                                                <a href="{{ route('risk-maps.show', $riskMap) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $riskMap->id }}</a>
                                            @else
                                                {{ $riskMap->id }}
                                            @endcan
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $riskMap->assessment_date->format('d.m.Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $riskMap->status == 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : ($riskMap->status == 'draft' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100') }}">
                                                {{ __(ucfirst($riskMap->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $riskMap->conductedBy->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- Кнопка "Просмотр/Управление" --}}
                                            @can('view', $riskMap)
                                                 <a href="{{ route('risk-maps.show', $riskMap) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3" title="{{ __('Просмотреть/Управлять оценками') }}">
                                                    {{ __('Оценки') }}
                                                 </a>
                                            @endcan
                                            {{-- Кнопка "Редактировать" детали карты --}}
                                            @can('update', $riskMap)
                                                <a href="{{ route('risk-maps.edit', $riskMap) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 mr-3" title="{{ __('Редактировать детали карты') }}">
                                                    {{ __('Редакт.') }}
                                                </a>
                                            @endcan
                                            {{-- Кнопка "Удалить" карту --}}
                                            @can('delete', $riskMap)
                                                <form action="{{ route('risk-maps.destroy', $riskMap) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Вы уверены? Карта и все ее оценки будут удалены!') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="{{ __('Удалить карту') }}">
                                                        {{ __('Удалить') }}
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('Карты рисков для этого рабочего места еще не созданы.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $riskMaps->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
