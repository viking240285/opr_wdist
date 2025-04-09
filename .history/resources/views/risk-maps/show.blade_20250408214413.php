<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                 {{ __('Risk Map Details (ID: :mapId) for Workplace:') }} {{ $workplace->name }}
            </h2>
             <x-bladewind::button
                tag="a"
                size="tiny"
                href="{{ route('workplaces.risk-maps.index', $workplace) }}">
                {{ __('Back to Maps List') }}
            </x-bladewind::button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Map Details Section --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                     <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Map Information') }}</h3>
                    <dl class="mt-4 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Workplace') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $workplace->name }}</dd>
                        </div>
                         <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Department') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $workplace->department->name }}</dd>
                        </div>
                         <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Organization') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $workplace->department->organization->name }}</dd>
                        </div>
                         <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Assessment Date') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $riskMap->assessment_date->format('d/m/Y') }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100"><x-bladewind::tag label="{{ ucfirst($riskMap->status) }}" color="{{ $riskMap->status == 'completed' ? 'green' : ($riskMap->status == 'draft' ? 'yellow' : 'gray') }}" /></dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Conducted By') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $riskMap->conductedBy->name ?? 'N/A' }}</dd>
                        </div>
                        {{-- Add Commission and Participants if needed --}}
                    </dl>
                     <div class="mt-4 text-right">
                        <x-bladewind::button size="tiny" tag="a" href="{{ route('risk-maps.edit', $riskMap) }}">{{ __('Edit Map Details') }}</x-bladewind::button>
                    </div>
                </div>
            </div>

            {{-- Risk Assessments Section --}}
             <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                 <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ __('Risk Assessments') }}</h3>
                    {{-- Link to add new assessment (opens modal or separate page) --}}
                    <x-bladewind::button
                        tag="a"
                        href="{{ route('risk-maps.assessments.create', $riskMap) }}"
                        icon="plus-circle"
                        icon_right="true"
                        size="small">
                        {{ __('Add Assessment') }}
                    </x-bladewind::button>
                </div>

                 <x-bladewind::table divider="thin">
                    <x-slot name="header">
                        <th>{{ __('Hazard Code') }}</th>
                        <th>{{ __('Hazard Source') }}</th>
                        <th>V</th>
                        <th>T</th>
                        <th>E</th>
                        <th>R</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Existing Measures') }}</th>
                        <th>{{ __('Planned Measures') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </x-slot>
                    @forelse ($assessments as $assessment)
                        <tr>
                            <td>{{ $assessment->hazard->code ?? 'N/A' }}</td>
                            <td class="whitespace-normal">{{ $assessment->hazard->source ?? 'N/A' }}</td>
                            <td>{{ $assessment->probability }}</td>
                            <td>{{ $assessment->severity }}</td>
                            <td>{{ $assessment->exposure }}</td>
                            <td><strong>{{ $assessment->calculated_risk ?? '-' }}</strong></td>
                            <td>{{ $assessment->risk_category ?? '-' }}</td> {{-- Consider using a Tag component --}}
                            <td>
                                @forelse($assessment->existingMeasures as $measure)
                                    <span class="block text-xs">- {{ Str::limit($measure->description, 40) }}</span>
                                @empty
                                    <span class="text-xs italic text-gray-500">{{ __('None') }}</span>
                                @endforelse
                            </td>
                            <td>
                                @forelse($assessment->plannedMeasures as $measure)
                                     <span class="block text-xs">- {{ Str::limit($measure->description, 40) }}</span>
                                @empty
                                     <span class="text-xs italic text-gray-500">{{ __('None') }}</span>
                                @endforelse
                            </td>
                            <td>
                                {{-- Shallow route usage --}}
                                <x-bladewind::button.circle
                                    tag="a"
                                    href="{{ route('assessments.edit', $assessment) }}"
                                    icon="pencil-square"
                                    size="tiny"
                                    tooltip="{{ __('Edit Assessment') }}"/>

                                <form action="{{ route('assessments.destroy', $assessment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <x-bladewind::button.circle
                                        icon="trash"
                                        color="red"
                                        size="tiny"
                                        can_submit="true"
                                        tooltip="{{ __('Delete Assessment') }}" />
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">{{ __('No risk assessments added to this map yet.') }}</td>
                        </tr>
                    @endforelse
                </x-bladewind::table>

             </div>
        </div>
    </div>
</x-app-layout>
