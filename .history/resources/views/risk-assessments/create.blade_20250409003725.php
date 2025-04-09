<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Добавление новой оценки риска для карты ID:') }} {{ $riskMap->id }} ({{ $riskMap->workplace->name }})
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('risk-maps.assessments.store', $riskMap) }}">
                        @include('risk-assessments._form', [
                            'riskMap' => $riskMap,
                            'hazards' => $hazards
                            // 'measures' => $measures // Add later
                            ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
