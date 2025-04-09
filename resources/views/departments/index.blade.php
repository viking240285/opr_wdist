<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Отделы организации:') }} {{ $organization->name }} {{-- Перевод --}}
            </h2>
            {{-- Кнопка "Назад" видна всем, кто может видеть организацию --}}
            @can('view', $organization)
            <x-secondary-button tag="a" size="tiny" href="{{ route('organizations.index') }}">
                {{ __('К списку организаций') }} {{-- Перевод --}}
            </x-secondary-button>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-6">
                         <h3 class="text-lg font-medium">{{ __('Список отделов') }}</h3>
                         {{-- Кнопка "Добавить" видна тем, кто может создавать отделы --}}
                         @can('create', App\Models\Department::class)
                            <x-primary-button tag="a" href="{{ route('organizations.departments.create', $organization) }}">
                                {{ __('Добавить отдел') }} {{-- Перевод --}}
                            </x-primary-button>
                         @endcan
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Название') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Родительский отдел') }}</th> {{-- Перевод --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Действия') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($departments as $department)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{-- Ссылка на просмотр должностей --}}
                                            @can('view', $department)
                                                 <a href="{{ route('departments.positions.index', $department) }}" class="hover:underline">{{ $department->name }}</a>
                                            @else
                                                {{ $department->name }}
                                            @endcan
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $department->parent->name ?? '--' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- Кнопка "Должности" --}}
                                            @can('view', $department) {{-- Если может видеть отдел, может видеть и должности? Или нужна отдельная проверка? Пока так. --}}
                                                <a href="{{ route('departments.positions.index', $department) }}"
                                                   class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-3"
                                                   title="{{ __('Должности') }}">
                                                   {{ __('Должности') }}
                                                </a>
                                            @endcan
                                            {{-- Кнопка "Редактировать" --}}
                                            @can('update', $department)
                                                <a href="{{ route('departments.edit', $department) }}"
                                                   class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 mr-3">
                                                   {{ __('Редактировать') }}
                                                </a>
                                            @endcan
                                            {{-- Кнопка "Удалить" --}}
                                            @can('delete', $department)
                                                <form action="{{ route('departments.destroy', $department) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Вы уверены? Все дочерние отделы потеряют связь.') }}');"> {{-- Уточненное сообщение --}}
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
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('Отделы для этой организации не найдены.') }} {{-- Перевод --}}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $departments->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
