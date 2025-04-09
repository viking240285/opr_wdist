@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <x-bladewind::input
        name="name"
        label="{{ __('Organization Name') }}"
        required="true"
        value="{{ old('name', $organization->name ?? null) }}" />

    <x-bladewind::input
        name="inn"
        label="{{ __('INN') }}"
        numeric="true" {{-- Assuming INN is numeric --}}
        value="{{ old('inn', $organization->inn ?? null) }}" />

    <x-bladewind::input
        name="kpp"
        label="{{ __('KPP') }}"
        numeric="true" {{-- Assuming KPP is numeric --}}
        value="{{ old('kpp', $organization->kpp ?? null) }}" />

    {{-- Placeholder for logo upload --}}
    {{-- <x-bladewind::input
        name="logo"
        label="{{ __('Logo') }}"
        type="file" /> --}}

    <x-bladewind::textarea
        name="address"
        label="{{ __('Address') }}"
        class="md:col-span-2" {{-- Span across two columns on medium screens --}}
        value="{{ old('address', $organization->address ?? null) }}" />

</div>

<div class="mt-6 text-right">
    <x-bladewind::button
        can_submit="true"
        name="save-org">
        {{ __('Save Organization') }}
    </x-bladewind::button>
</div>
