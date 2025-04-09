<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Control Measures Reference') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-4 text-right">
                        <x-bladewind::button
                            tag="a"
                            href="{{ route('measures.create') }}"
                            icon="plus-circle"
                            icon_right="true">
                            {{ __('Add Measure') }}
                        </x-bladewind::button>
                    </div>

                    <x-bladewind::table searchable="true" search_placeholder="Search measures...">
                        <x-slot name="header">
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Due Date') }}</th>
                            <th>{{ __('Responsible') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </x-slot>
                        @forelse ($measures as $measure)
                            <tr>
                                <td class="whitespace-normal">{{ $measure->description }}</td>
                                <td><x-bladewind::tag label="{{ ucfirst(str_replace('_', ' ', $measure->status)) }}"
                                    color="{{ $measure->status == 'completed' ? 'green' : ($measure->status == 'in_progress' ? 'blue' : 'gray') }}" /></td>
                                <td>{{ $measure->due_date ? $measure->due_date->format('d/m/Y') : '--' }}</td>
                                <td>{{ $measure->responsibleUser->name ?? '--' }}</td>
                                <td>
                                    <x-bladewind::button.circle
                                        tag="a"
                                        href="{{ route('measures.edit', $measure) }}"
                                        icon="pencil-square"
                                        size="tiny"
                                        tooltip="{{ __('Edit') }}"/>

                                    <form action="{{ route('measures.destroy', $measure) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This measure might be linked to risk assessments.');">
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
                                <td colspan="5" class="text-center">{{ __('No measures found.') }}</td>
                            </tr>
                        @endforelse
                    </x-bladewind::table>

                    <div class="mt-4">
                        {{ $measures->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
