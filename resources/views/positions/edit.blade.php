<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
             {{ __('Edit Position') }}: {{ $position->name }} ({{ $department->name }} / {{ $department->organization->name }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Shallow route usage: route('positions.update', $position) --}}
                    <form method="POST" action="{{ route('positions.update', $position) }}">
                        @method('PATCH')
                        {{-- Pass department for the hidden field, and position for filling the form --}}
                        @include('positions._form', ['department' => $department, 'position' => $position])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
