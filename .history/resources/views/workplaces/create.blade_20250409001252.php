<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Workplace to') }} {{ $organization->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('organizations.workplaces.store', $organization) }}">
                        <input type="hidden" name="organization_id" value="{{ $organization->id }}">
                        @include('workplaces._form', [
                            'organization' => $organization,
                            'departments' => $departments,
                            'positions' => $positions
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
