<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Должности отдела:') }} {{ $department->name }} ({{ $department->organization->name }})
            </h2>
            <x-secondary-button tag="a" href="{{ route('organizations.departments.index', $department->organization) }}">
                {{ __('Назад к отделам') }}
            </x-secondary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">{{ __('Список должностей') }}</h3>
                        <x-primary-button tag="a" href="{{ route('departments.positions.create', $department) }}">
                            {{ __('Добавить должность') }}
                        </x-primary-button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Название') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Действия') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($positions as $position)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $position->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('positions.edit', $position) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300 mr-3" title="{{ __('Редактировать') }}">{{ __('Редактировать') }}</a>

                                            <form action="{{ route('positions.destroy', $position) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Вы уверены?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="{{ __('Удалить') }}">{{ __('Удалить') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">{{ __('Должности для этого отдела не найдены.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $positions->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
