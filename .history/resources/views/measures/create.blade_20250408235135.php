<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ isset($measure) ? __('Редактировать меру контроля') : __('Добавить новую меру контроля') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ isset($measure) ? route('measures.update', $measure) : route('measures.store') }}">
                        @csrf
                        @if(isset($measure))
                            @method('PUT')
                        @endif
                        @include('measures._form', ['users' => $users, 'measure' => $measure ?? null])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
