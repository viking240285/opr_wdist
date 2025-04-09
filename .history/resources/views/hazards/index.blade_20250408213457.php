<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Hazards Reference') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

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
