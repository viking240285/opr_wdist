<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Organizations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <div class="mb-4 text-right">
                        <x-bladewind::button
                            tag="a"
                            href="{{ route('organizations.create') }}"
                            icon="plus-circle"
                            icon_right="true">
                            {{ __('Add Organization') }}
                        </x-bladewind::button>
                    </div>

                    <x-bladewind::table>
                        <x-slot name="header">
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('INN') }}</th>
                            <th>{{ __('KPP') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </x-slot>
                        {{-- Data will be populated from the controller --}}
                        {{-- Example row structure:
                        @foreach ($organizations as $organization)
                            <tr>
                                <td>{{ $organization->name }}</td>
                                <td>{{ $organization->inn }}</td>
                                <td>{{ $organization->kpp }}</td>
                                <td>
                                    <x-bladewind::button.circle
                                        tag="a"
                                        href="{{ route('organizations.edit', $organization) }}"
                                        icon="pencil-square"
                                        size="tiny" />

                                    <form action="{{ route('organizations.destroy', $organization) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-bladewind::button.circle
                                            icon="trash"
                                            color="red"
                                            size="tiny"
                                            can_submit="true" />
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        --}}
                    </x-bladewind::table>

                    {{-- Pagination links if needed --}}
                    {{-- <div class="mt-4">
                        {{ $organizations->links() }}
                    </div> --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
