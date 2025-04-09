<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Departments for') }} {{ $organization->name }}
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
                            href="{{ route('organizations.departments.create', $organization) }}"
                            icon="plus-circle"
                            icon_right="true">
                            {{ __('Add Department') }}
                        </x-bladewind::button>
                    </div>

                    <x-bladewind::table>
                        <x-slot name="header">
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Parent Department') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </x-slot>
                        @forelse ($departments as $department)
                            <tr>
                                <td>{{ $department->name }}</td>
                                <td>{{ $department->parent->name ?? '--' }}</td> {{-- Display parent name or placeholder --}}
                                <td>
                                    {{-- Shallow route usage: route('departments.edit', $department) --}}
                                    <x-bladewind::button.circle
                                        tag="a"
                                        href="{{ route('departments.edit', $department) }}"
                                        icon="pencil-square"
                                        size="tiny" />

                                     {{-- Positions link --}}
                                     <x-bladewind::button.circle
                                        tag="a"
                                        href="{{ route('departments.positions.index', $department) }}"
                                        icon="user-group"
                                        color="green"
                                        size="tiny"
                                        tooltip="{{ __('Positions') }}"/>

                                     {{-- Shallow route usage: route('departments.destroy', $department) --}}
                                    <form action="{{ route('departments.destroy', $department) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-bladewind::button.circle
                                            icon="trash"
                                            color="red"
                                            size="tiny"
                                            can_submit="true" />
                                    </form>

                                    {{-- TODO: Add button/link for Positions within this department --}}
                                    {{-- <x-bladewind::button.circle
                                        tag="a"
                                        href="{{ route('departments.positions.index', $department) }}"
                                        icon="users"
                                        color="blue"
                                        size="tiny"
                                        title="{{ __('Positions') }}"/> --}}
                                </td>
                            </tr>
                        @empty
                             <tr>
                                <td colspan="3" class="text-center">{{ __('No departments found for this organization.') }}</td>
                            </tr>
                        @endforelse
                    </x-bladewind::table>

                    <div class="mt-4">
                        {{ $departments->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
