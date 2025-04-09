@csrf

{{-- Hidden field for workplace_id --}}
<input type="hidden" name="workplace_id" value="{{ $workplace->id }}">

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <x-bladewind::datepicker
        name="assessment_date"
        label="{{ __('Assessment Date') }}"
        required="true"
        value="{{ old('assessment_date', $riskMap->assessment_date ?? now()->format('Y-m-d')) }}"
    />

    <x-bladewind::select
        name="status"
        label="{{ __('Map Status') }}"
        required="true"
        :data="[ // Define statuses here or pass from controller/config
            ['id' => 'draft', 'name' => __('Draft')],
            ['id' => 'completed', 'name' => __('Completed')],
            ['id' => 'archived', 'name' => __('Archived')],
        ]"
        selectedValue="{{ old('status', $riskMap->status ?? 'draft') }}"
    />

     <x-bladewind::textarea
        name="commission_members"
        label="{{ __('Commission Members (JSON or simple text)') }}"
        placeholder='[{{ "name": "Иванов И.И.", "position": "Инженер по ОТ" }}, ...]'
        class="md:col-span-2"
        value="{{ old('commission_members', isset($riskMap) && is_array($riskMap->commission_members) ? json_encode($riskMap->commission_members, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($riskMap->commission_members ?? '')) }}" />

     <x-bladewind::textarea
        name="participants"
        label="{{ __('Participants (Optional, JSON or simple text)') }}"
         placeholder='[{{ "name": "Петров П.П.", "position": "Работник" }}]'
        class="md:col-span-2"
        value="{{ old('participants', isset($riskMap) && is_array($riskMap->participants) ? json_encode($riskMap->participants, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : ($riskMap->participants ?? '')) }}" />

     {{-- conducted_by_user_id will be set automatically to the logged-in user in the controller --}}

</div>

<div class="mt-6 text-right">
    <x-bladewind::button
        can_submit="true"
        name="save-map">
        {{ isset($riskMap) ? __('Update Map Details') : __('Create Risk Map') }}
    </x-bladewind::button>
</div>
