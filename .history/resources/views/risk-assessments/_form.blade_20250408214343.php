@csrf

{{-- Hidden field for risk_map_id --}}
<input type="hidden" name="risk_map_id" value="{{ $riskMap->id }}">

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
    {{-- Hazard Selection --}}
    <div class="md:col-span-3">
        <x-bladewind::select
            name="hazard_id"
            label="{{ __('Identified Hazard') }}"
            required="true"
            searchable="true"
            :data="$hazards ?? []" {{-- Pass available hazards (code + name) from controller --}}
            selectedValue="{{ old('hazard_id', $assessment->hazard_id ?? null) }}"
            placeholder="{{ __('Select Hazard') }}"
        />
         {{-- Display Hazard Details on Selection (Future Enhancement with JS) --}}
         {{-- <div id="hazard-details" class="mt-2 text-sm text-gray-600 dark:text-gray-400"></div> --}}
    </div>

    {{-- Risk Parameters --}}
    <x-bladewind::input
        name="probability"
        label="{{ __('Probability (V)') }}"
        required="true"
        numeric="true"
        type="number" {{-- Ensure numeric input --}}
        step="0.1" {{-- Allow decimals if needed --}}
        value="{{ old('probability', $assessment->probability ?? null) }}" />

    <x-bladewind::input
        name="severity"
        label="{{ __('Severity (T)') }}"
        required="true"
        numeric="true"
        type="number"
        step="0.1"
        value="{{ old('severity', $assessment->severity ?? null) }}" />

    <x-bladewind::input
        name="exposure"
        label="{{ __('Exposure (E)') }}"
        required="true"
        numeric="true"
        type="number"
        step="0.1"
        value="{{ old('exposure', $assessment->exposure ?? null) }}" />

    {{-- Calculated Risk (Display Only?) --}}
    {{-- <div class="md:col-span-3">
        <p>Calculated Risk (R = V * T * E): <span id="calculated-risk">{{ $assessment->calculated_risk ?? 'N/A' }}</span></p>
        <p>Risk Category: <span id="risk-category">{{ $assessment->risk_category ?? 'N/A' }}</span></p>
    </div> --}}

    {{-- Measures Selection --}}

    <div class="md:col-span-3 mt-4 border-t dark:border-gray-700 pt-4">
        <h3 class="text-lg font-medium mb-2 text-gray-900 dark:text-gray-100">{{ __('Control Measures') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-bladewind::select
                    name="existing_measures[]"
                    label="{{ __('Existing Measures') }}"
                    multiple="true"
                    searchable="true"
                    :data="$measures ?? []"
                    placeholder="Select existing measures..."
                    selected_value="{{ old('existing_measures', isset($assessment) ? $assessment->existingMeasures->pluck('id')->toArray() : []) }}"
                />
            </div>
            <div>
                 <x-bladewind::select
                    name="planned_measures[]"
                    label="{{ __('Planned Measures') }}"
                    multiple="true"
                    searchable="true"
                    :data="$measures ?? []"
                    placeholder="Select planned measures..."
                    selected_value="{{ old('planned_measures', isset($assessment) ? $assessment->plannedMeasures->pluck('id')->toArray() : []) }}"
                />
            </div>
        </div>
         <div class="text-right mt-2">
            <x-bladewind::button type="secondary" size="tiny" tag="a" href="{{ route('measures.create') }}" target="_blank">{{ __('Add New Measure') }}</x-bladewind::button>
        </div>
    </div>


</div>

<div class="mt-6 text-right">
    <x-bladewind::button
        can_submit="true"
        name="save-assessment">
        {{ isset($assessment) ? __('Update Assessment') : __('Save Assessment') }}
    </x-bladewind::button>
</div>
