@extends('layouts.app')

@section('title', 'Login - QMS')

@section('content')
<div class="relative min-h-screen flex items-center justify-end p-4 lg:p-16 bg-center bg-no-repeat" style="background-image: url('{{ asset('image/login-bg.png') }}'); background-size: 100% 100%;">
    <!-- Premium overlay to soften background and enhance contrast -->
    <div class="absolute inset-0 bg-slate-900/20"></div>

    <div class="relative z-10 w-full max-w-[460px] lg:mr-[100px]">
        <!-- Login Card -->
        <div class="bg-white/30 backdrop-blur-sm rounded-2xl shadow-2xl border border-white/30 overflow-hidden">
            <!-- Header -->
            <div class="bg-transparent text-center border-b border-white/20">
                <div class="flex justify-center overflow-hidden py-1">
                    <img src="{{ asset('image/sai_logo.png') }}" alt="SAI Logo" class="h-[180px] w-auto mix-blend-multiply scale-110">
                </div>
            </div>

            <!-- Form -->
            <div class="p-8">
                @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div class="flex items-center gap-2 text-red-600">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span class="font-medium">Terjadi kesalahan:</span>
                    </div>
                    <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}" class="space-y-5">
                    @csrf

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fa-solid fa-user mr-2 text-slate-600"></i>Username
                        </label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-6 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="Masukkan username"
                            required
                            autofocus>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="fa-solid fa-lock mr-2 text-slate-600"></i>Password
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                placeholder="Masukkan password"
                                required>
                            <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fa-solid fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <span class="text-sm text-slate-600">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-500/30 hover:shadow-blue-500/40 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Login</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-sm text-slate-300 mt-6 drop-shadow-sm">
            &copy; {{ date('Y') }} ICT - SAI. All rights reserved.
        </p>
    </div>
</div>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId + '-icon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection