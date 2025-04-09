<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Справочник опасностей') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">{{ __('Список опасностей') }}</h3>
                        <x-primary-button>
                            <a href="{{ route('hazards.create') }}" class="text-white">{{ __('Добавить опасность') }}</a>
                        </x-primary-button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Название') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Действия') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($hazards as $hazard)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $hazard->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('hazards.edit', $hazard) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-3">{{ __('Редактировать') }}</a>
                                            <form action="{{ route('hazards.destroy', $hazard) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Вы уверены? Эта опасность может использоваться в оценках риска.') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">{{ __('Удалить') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('Опасности не найдены.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $hazards->links() }}
                    </div>
                </div>
            </div>
        </div>
                    <div class="mb-4 text-right">
                        <x-bladewind::button
                            tag="a"
                            href="{{ route('hazards.create') }}"
                            icon="plus-circle"
                            icon_right="true">
                            {{ __('Add Hazard') }}
                        </x-bladewind::button>
                    </div>

                    <x-bladewind::table searchable="true" search_placeholder="Search hazards...">
                        <x-slot name="header">
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Source') }}</th>
                            <th>{{ __('Threat') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </x-slot>
                        @forelse ($hazards as $hazard)
                            <tr>
                                <td>{{ $hazard->code }}</td>
                                <td>{{ $hazard->type ?? '--' }}</td>
                                <td class="whitespace-normal">{{ $hazard->source }}</td>
                                <td class="whitespace-normal">{{ $hazard->threat }}</td>
                                <td>
                                    <x-bladewind::button.circle
                                        tag="a"
                                        href="{{ route('hazards.edit', $hazard) }}"
                                        icon="pencil-square"
                                        size="tiny"
                                        tooltip="{{ __('Edit') }}"/>

                                    <form action="{{ route('hazards.destroy', $hazard) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This hazard might be used in risk assessments.');">
                                        @csrf
                                        @method('DELETE')
                                        <x-bladewind::button.circle
                                            icon="trash"
                                            color="red"
                                            size="tiny"
                                            can_submit="true"
                                            tooltip="{{ __('Delete') }}" />
                                    </form>
                                </td>
                            </tr>
                        @empty
                             <tr>
                                <td colspan="5" class="text-center">{{ __('No hazards found.') }}</td>
                            </tr>
                        @endforelse
                    </x-bladewind::table>

                    <div class="mt-4">
                        {{ $hazards->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
