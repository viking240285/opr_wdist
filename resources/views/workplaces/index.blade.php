<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Рабочие места организации:') }} {{ $organization->name }} {{-- Перевод --}}
            </h2>
            {{-- Кнопка "Назад" видна всем, кто может видеть организацию --}}
            @can('view', $organization)
                <x-secondary-button tag="a" size="tiny" href="{{ route('organizations.index') }}">
                    {{ __('К списку организаций') }} {{-- Уже было, но для консистентности --}}
                </x-secondary-button>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-6">
                         <h3 class="text-lg font-medium">{{ __('Список рабочих мест') }}</h3>
                         {{-- Кнопка "Добавить" видна тем, кто может создавать РМ --}}
                         @can('create', [App\Models\Workplace::class, $organization]) {{-- Контекст организации --}}
                            <x-primary-button tag="a" href="{{ route('organizations.workplaces.create', $organization) }}">
                                {{ __('Добавить рабочее место') }} {{-- Перевод --}}
                            </x-primary-button>
                         @endcan
                    </div>

                     <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Код') }}</th> {{-- Перевод --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Название') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Отдел') }}</th> {{-- Перевод --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Должность') }}</th> {{-- Перевод --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Действия') }}</th>
                                </tr>
                            </thead>
                             <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($workplaces as $workplace)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $workplace->code ?? '--' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{-- Ссылка на карты рисков --}}
                                            @can('view', $workplace) {{-- Если может видеть РМ, может видеть и карты рисков? --}}
                                                <a href="{{ route('workplaces.risk-maps.index', $workplace) }}" class="hover:underline">{{ $workplace->name }}</a>
                                            @else
                                                 {{ $workplace->name }}
                                            @endcan
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $workplace->department->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $workplace->position->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- Кнопка "Карты рисков" --}}
                                             @can('view', $workplace) {{-- Или нужна проверка на viewAny RiskMap? --}}
                                                <a href="{{ route('workplaces.risk-maps.index', $workplace) }}"
                                                   class="text-purple-600 dark:text-purple-400 hover:text-purple-900 dark:hover:text-purple-300 mr-3"
                                                   title="{{ __('Карты рисков') }}">
                                                   {{ __('Карты рисков') }} {{-- Перевод --}}
                                                </a>
                                            @endcan
                                            {{-- Кнопка "Редактировать" --}}
                                            @can('update', $workplace)
                                                <a href="{{ route('workplaces.edit', $workplace) }}"
                                                   class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 mr-3">
                                                   {{ __('Редактировать') }}
                                                </a>
                                            @endcan
                                            {{-- Кнопка "Удалить" --}}
                                            @can('delete', $workplace)
                                                <form action="{{ route('workplaces.destroy', $workplace) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Вы уверены?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                        {{ __('Удалить') }}
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('Рабочие места для этой организации не найдены.') }} {{-- Перевод --}}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $workplaces->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
