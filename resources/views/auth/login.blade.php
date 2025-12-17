<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-sky-700 via-blue-600 to-sky-500 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto h-16 w-16 flex items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                    <x-application-logo class="h-10 w-10 text-white" />
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-white">Welcome back</h2>
                <p class="mt-2 text-center text-sm text-white/90">Sign in to continue to the Inventory dashboard</p>
            </div>

            <x-auth-session-status class="mb-4 text-sm text-white" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6 bg-white/95 dark:bg-white rounded-2xl p-8 shadow-lg">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <div class="mt-1">
                        <x-text-input id="email" name="email" type="email" autocomplete="username" required class="block w-full px-3 py-2 rounded-md border border-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent" placeholder="you@example.com" :value="old('email')" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="mt-1">
                        <x-text-input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full px-3 py-2 rounded-md border border-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent" placeholder="Password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-500">Forgot your password?</a>
                        </div>
                    @endif
                </div>

                <div>
                    <x-primary-button class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
