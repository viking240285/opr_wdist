<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Positions for Department:') }} {{ $department->name }} ({{ $department->organization->name }})
            </h2>
            <x-bladewind::button
                tag="a"
                size="tiny"
                href="{{ route('organizations.departments.index', $department->organization) }}">
                {{ __('Back to Departments') }}
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
                            href="{{ route('departments.positions.create', $department) }}"
                            icon="plus-circle"
                            icon_right="true">
                            {{ __('Add Position') }}
                        </x-bladewind::button>
                    </div>

                    <x-bladewind::table>
                        <x-slot name="header">
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </x-slot>
                        @forelse ($positions as $position)
                            <tr>
                                <td>{{ $position->name }}</td>
                                <td>
                                    {{-- Shallow route usage: route('positions.edit', $position) --}}
                                    <x-bladewind::button.circle
                                        tag="a"
                                        href="{{ route('positions.edit', $position) }}"
                                        icon="pencil-square"
                                        size="tiny"
                                        tooltip="{{ __('Edit') }}"/>

                                     {{-- Shallow route usage: route('positions.destroy', $position) --}}
                                    <form action="{{ route('positions.destroy', $position) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
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
                                <td colspan="2" class="text-center">{{ __('No positions found for this department.') }}</td>
                            </tr>
                        @endforelse
                    </x-bladewind::table>

                    <div class="mt-4">
                        {{ $positions->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
