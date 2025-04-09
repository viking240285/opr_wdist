<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Система ОПР</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-gray-100 dark:bg-gray-900">
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-red-500 selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <main class="mt-12 text-center">
                     <h1 class="text-4xl font-semibold text-gray-900 dark:text-white mb-4">
                         Система Оценки Профессиональных Рисков (ОПР)
                     </h1>
                     <p class="text-lg text-gray-600 dark:text-gray-400">
                         Платформа для проведения оценки профессиональных рисков на рабочих местах, управления опасностями, мерами контроля и формирования отчетности.
                     </p>
                     <div class="mt-8 flex justify-center gap-4">
                         @if (Route::has('login'))
                            @guest
                                <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-gray-200 dark:text-gray-800 dark:hover:bg-white dark:focus:bg-white dark:active:bg-gray-300 dark:focus:ring-offset-gray-800">
                                    Войти
                                </a>
                                @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 dark:bg-gray-800 dark:border-gray-500 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800">
                                    Зарегистрироваться
                                </a>
                                @endif
                            @endguest
                         @endif

                     </div>
                </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                </footer>
            </div>
        </div>
    </body>
</html>
