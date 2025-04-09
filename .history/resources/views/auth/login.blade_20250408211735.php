<x-guest-layout>
    <x-bladewind::centered-content size="medium">

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <x-bladewind::input
                name="email"
                label="{{ __('Email') }}"
                type="email"
                value="{{ old('email') }}"
                required="true"
                autofocus="true"
                autocomplete="username" />

            <!-- Password -->
            <div class="mt-4">
                <x-bladewind::input
                    name="password"
                    label="{{ __('Password') }}"
                    type="password"
                    required="true"
                    viewable="true"
                    autocomplete="current-password" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <x-bladewind::checkbox
                    name="remember"
                    id="remember_me"
                    label="{{ __('Remember me') }}" />
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mr-4" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-bladewind::button
                    can_submit="true"
                    class="ms-3">
                    {{ __('Log in') }}
                </x-bladewind::button>
            </div>
        </form>

    </x-bladewind::centered-content>
</x-guest-layout>
