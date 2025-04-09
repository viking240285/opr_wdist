<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Workplaces for Organization:') }} {{ $organization->name }}
            </h2>
             <x-bladewind::button
                tag="a"
                size="tiny"
                href="{{ route('organizations.index') }}">
                {{ __('Back to Organizations') }}
            </x-bladewind::button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-4 text-right">
                        <x-bladewind::button
                            tag="a"
                            href="{{ route('organizations.workplaces.create', $organization) }}"
                            icon="plus-circle"
                            icon_right="true">
                            {{ __('Add Workplace') }}
                        </x-bladewind::button>
                    </div>

                    <x-bladewind::table>
                        <x-slot name="header">
                            <th>{{ __('Code') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Department') }}</th>
                            <th>{{ __('Position') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </x-slot>
                        @forelse ($workplaces as $workplace)
                            <tr>
                                <td>{{ $workplace->code ?? '--' }}</td>
                                <td>{{ $workplace->name }}</td>
                                <td>{{ $workplace->department->name ?? 'N/A' }}</td>
                                <td>{{ $workplace->position->name ?? 'N/A' }}</td>
                                <td>
                                    {{-- TODO: Add link to Risk Maps for this workplace --}}
                                     <x-bladewind::button.circle
                                        tag="a"
                                        href="#" {{-- Replace with route('workplaces.risk-maps.index', $workplace) later --}}
                                        icon="map"
                                        color="purple"
                                        size="tiny"
                                        tooltip="{{ __('Risk Maps') }}"/>

                                    {{-- Shallow route usage: route('workplaces.edit', $workplace) --}}
                                    <x-bladewind::button.circle
                                        tag="a"
                                        href="{{ route('workplaces.edit', $workplace) }}"
                                        icon="pencil-square"
                                        size="tiny"
                                        tooltip="{{ __('Edit') }}"/>

                                     {{-- Shallow route usage: route('workplaces.destroy', $workplace) --}}
                                    <form action="{{ route('workplaces.destroy', $workplace) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
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
                                <td colspan="5" class="text-center">{{ __('No workplaces found for this organization.') }}</td>
                            </tr>
                        @endforelse
                    </x-bladewind::table>

                    <div class="mt-4">
                        {{ $workplaces->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
