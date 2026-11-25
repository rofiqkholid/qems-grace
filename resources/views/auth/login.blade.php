@extends('layouts.app')

@section('title', 'Login GRACE')

@section('content')
<style>
    #three-canvas {
        position: fixed;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        display: block;
    }
    .grace-glow {
        text-shadow: 0 4px 24px rgba(14, 165, 233, 0.25);
    }
</style>

<div id="login-bg" class="relative min-h-screen flex items-center justify-center lg:justify-between p-4 lg:p-16 overflow-hidden bg-gradient-to-br from-slate-100 via-sky-50 to-blue-100">
    <!-- Three.js 3D Manufacturing Canvas -->
    <canvas id="three-canvas"></canvas>

    <!-- Left Branding & Industry 4.0 Info (Desktop - Bottom Left) -->
    <div class="relative z-10 hidden lg:flex lg:self-end flex-col max-w-xl pl-2 pb-2 text-slate-800 select-none">
        <h1 class="text-6xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-slate-900 via-sky-700 to-blue-600 grace-glow mb-3">
            GRACE
        </h1>
        <p class="text-xl font-semibold text-slate-700 mb-2">
            General Report, Achievement Control and Evaluation
        </p>
        <p class="text-sm font-bold tracking-[0.25em] text-slate-700 uppercase mb-8">
            SEE. SOLVE. EVOLVE.
        </p>

        <div class="grid grid-cols-3 gap-4">
            <div class="p-4 rounded-2xl bg-white/70 border border-slate-200/80 shadow-sm backdrop-blur-md hover:bg-white/90 transition-colors">
                <div class="w-9 h-9 rounded-xl bg-sky-500/15 text-sky-600 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <p class="text-xs text-slate-700 font-medium leading-relaxed">
                    Temukan &amp; Catat Setiap Temuan
                </p>
            </div>
            <div class="p-4 rounded-2xl bg-white/70 border border-slate-200/80 shadow-sm backdrop-blur-md hover:bg-white/90 transition-colors">
                <div class="w-9 h-9 rounded-xl bg-blue-500/15 text-blue-600 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-gears"></i>
                </div>
                <p class="text-xs text-slate-700 font-medium leading-relaxed">
                    Lakukan Tindakan &amp; Selesaikan Akar Masalah
                </p>
            </div>
            <div class="p-4 rounded-2xl bg-white/70 border border-slate-200/80 shadow-sm backdrop-blur-md hover:bg-white/90 transition-colors">
                <div class="w-9 h-9 rounded-xl bg-emerald-500/15 text-emerald-600 flex items-center justify-center mb-3">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <p class="text-xs text-slate-700 font-medium leading-relaxed">
                    Tingkatkan Proses Secara Berkelanjutan
                </p>
            </div>
        </div>
    </div>

    <div class="relative z-10 w-full max-w-[400px] lg:max-w-[440px] mx-auto lg:mx-0 lg:mr-[60px] my-auto">
        <!-- Login Card (Glassmorphism Transparent) -->
        <div class="bg-white/35 md:bg-white/30 backdrop-blur-md rounded-2xl border border-white/40 shadow-xl shadow-slate-900/10 overflow-hidden">
            <!-- Header -->
            <div class="bg-transparent text-center border-b border-white/20">
                <div class="flex justify-center overflow-hidden py-1">
                    <img src="{{ asset('image/sai_logo.png') }}" alt="SAI Logo" class="h-[120px] sm:h-[150px] lg:h-[180px] w-auto mix-blend-multiply scale-105 lg:scale-110">
                </div>
            </div>

            <!-- Form -->
            <div class="p-6 sm:p-8">
                @if ($errors->any())
                <div id="error-box" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <div id="error-header" class="flex items-center gap-2 text-red-600">
                        <i id="error-icon" class="fa-solid fa-circle-exclamation"></i>
                        <span id="error-title" class="font-medium">An error occurred:</span>
                    </div>
                    <ul id="error-list" class="mt-2 text-sm text-red-600 list-none space-y-1">
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
                        class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-blue-500/30 hover:shadow-blue-500/40 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Login</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-sm text-slate-300 mt-6 drop-shadow">
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

    document.addEventListener('DOMContentLoaded', function() {
        const errorItems = document.querySelectorAll('.list-none li');
        errorItems.forEach(li => {
            const match = li.textContent.match(/Too many login attempts\. Please try again in (\d+) seconds\./);
            if (match) {
                let seconds = parseInt(match[1], 10);
                const submitBtn = document.querySelector('button[type="submit"]');
                
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
                
                const timer = setInterval(() => {
                    seconds--;
                    if (seconds <= 0) {
                        clearInterval(timer);
                        li.textContent = 'You can try logging in now.';
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        }

                        // Switch style to blue Information
                        const errorBox = document.getElementById('error-box');
                        const errorHeader = document.getElementById('error-header');
                        const errorIcon = document.getElementById('error-icon');
                        const errorTitle = document.getElementById('error-title');
                        const errorList = document.getElementById('error-list');

                        if (errorBox) {
                            errorBox.classList.remove('bg-red-50', 'border-red-200');
                            errorBox.classList.add('bg-blue-50', 'border-blue-200');
                        }
                        if (errorHeader) {
                            errorHeader.classList.remove('text-red-600');
                            errorHeader.classList.add('text-blue-600');
                        }
                        if (errorIcon) {
                            errorIcon.classList.remove('fa-circle-exclamation');
                            errorIcon.classList.add('fa-circle-info');
                        }
                        if (errorTitle) {
                            errorTitle.textContent = 'Information:';
                        }
                        if (errorList) {
                            errorList.classList.remove('text-red-600');
                            errorList.classList.add('text-blue-600');
                        }
                    } else {
                        li.textContent = `Too many login attempts. Please try again in ${seconds} seconds.`;
                    }
                }, 1000);
            }
        });
    });
</script>

<!-- Three.js 3D Manufacturing Animation -->
<script>
    window.SAI_LOGO_URL = "{{ asset('image/sai_logo.png') }}";
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="{{ asset('js/login-3d.js') }}"></script>
@endsection