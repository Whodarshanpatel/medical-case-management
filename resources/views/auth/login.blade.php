<x-guest-layout>
    <!-- Medical App Header Branding -->
    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-teal-600 text-white font-bold text-xl shadow-md mb-2">
            J
        </div>
        <h1 class="text-xl font-bold text-slate-800">Jajal Medical Case Management</h1>
        <p class="text-xs text-slate-500 mt-1">Sign in to access clinical records and surgical case planning</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@jajalmedical.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between text-sm">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-teal-600 shadow-sm focus:ring-teal-500" name="remember">
                <span class="ms-2 text-xs text-slate-600">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs text-teal-600 hover:text-teal-800 hover:underline" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <div>
            <button type="submit" id="btn-login-submit" class="w-full py-2.5 px-4 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition">
                {{ __('Sign In to Portal') }}
            </button>
        </div>
    </form>

    <!-- Demo Credentials Helper Card -->
    <div class="mt-6 pt-5 border-t border-slate-200">
        <div class="rounded-lg bg-slate-50 border border-slate-200 p-3.5 text-xs text-slate-600">
            <div class="font-semibold text-slate-800 mb-2 flex items-center justify-between">
                <span>Demo Review Credentials</span>
                <span class="text-[10px] uppercase font-bold text-teal-700 bg-teal-50 px-1.5 py-0.5 rounded border border-teal-200">Pre-seeded</span>
            </div>
            <div class="space-y-2">
                <div class="bg-white p-2 rounded border border-slate-200">
                    <span class="font-bold text-slate-800">Admin Account</span> (Full Access):<br/>
                    <code class="text-teal-700 font-mono">admin@jajalmedical.com</code> &bull; <code class="text-slate-700 font-mono">AdminPassword123!</code>
                </div>
                <div class="bg-white p-2 rounded border border-slate-200">
                    <span class="font-bold text-slate-800">Viewer Account</span> (Read-Only):<br/>
                    <code class="text-teal-700 font-mono">viewer@jajalmedical.com</code> &bull; <code class="text-slate-700 font-mono">ViewerPassword123!</code>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
