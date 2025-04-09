<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
             {{ __('Edit Workplace') }}: {{ $workplace->name }} ({{ $organization->name }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Shallow route usage: route('workplaces.update', $workplace) --}}
                    <form method="POST" action="{{ route('workplaces.update', $workplace) }}">
                        @method('PATCH')
                        @include('workplaces._form', [
                            'organization' => $organization,
                            'workplace' => $workplace,
                            'departments' => $departments,
                            'positions' => $positions
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
