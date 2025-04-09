<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Risk Maps for Workplace:') }} {{ $workplace->name }} ({{ $workplace->department->organization->name }})
            </h2>
             <x-bladewind::button
                tag="a"
                size="tiny"
                href="{{ route('organizations.workplaces.index', $workplace->department->organization) }}">
                {{ __('Back to Workplaces') }}
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
                            href="{{ route('workplaces.risk-maps.create', $workplace) }}"
                            icon="plus-circle"
                            icon_right="true">
                            {{ __('Create New Risk Map') }}
                        </x-bladewind::button>
                    </div>

                    <x-bladewind::table>
                        <x-slot name="header">
                            <th>{{ __('Map ID') }}</th>
                            <th>{{ __('Assessment Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Conducted By') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </x-slot>
                        @forelse ($riskMaps as $riskMap)
                            <tr>
                                <td>{{ $riskMap->id }}</td>
                                <td>{{ $riskMap->assessment_date->format('d/m/Y') }}</td>
                                <td><x-bladewind::tag label="{{ ucfirst($riskMap->status) }}" color="{{ $riskMap->status == 'completed' ? 'green' : ($riskMap->status == 'draft' ? 'yellow' : 'gray') }}" /></td>
                                <td>{{ $riskMap->conductedBy->name ?? 'N/A' }}</td>
                                <td>
                                     {{-- Shallow route usage --}}
                                    <x-bladewind::button.circle
                                        tag="a"
                                        href="{{ route('risk-maps.show', $riskMap) }}"
                                        icon="eye"
                                        color="blue"
                                        size="tiny"
                                        tooltip="{{ __('View/Manage Assessments') }}"/>

                                    <x-bladewind::button.circle
                                        tag="a"
                                        href="{{ route('risk-maps.edit', $riskMap) }}"
                                        icon="pencil-square"
                                        size="tiny"
                                        tooltip="{{ __('Edit Map Details') }}"/>

                                    <form action="{{ route('risk-maps.destroy', $riskMap) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This will delete the map and all its assessments!');">
                                        @csrf
                                        @method('DELETE')
                                        <x-bladewind::button.circle
                                            icon="trash"
                                            color="red"
                                            size="tiny"
                                            can_submit="true"
                                            tooltip="{{ __('Delete Map') }}" />
                                    </form>
                                </td>
                            </tr>
                        @empty
                             <tr>
                                <td colspan="5" class="text-center">{{ __('No risk maps found for this workplace.') }}</td>
                            </tr>
                        @endforelse
                    </x-bladewind::table>

                    <div class="mt-4">
                        {{ $riskMaps->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
