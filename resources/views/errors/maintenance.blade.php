@extends('layouts.app')

@section('title', 'Under Maintenance')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 flex flex-col items-center justify-center p-6 text-center">
        <div class="max-w-md w-full">
            <!-- Maintenance Illustration -->
            <div class="w-32 h-32 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-16 h-16 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-slate-800 mb-2">Under Maintenance</h1>
            <p class="text-lg text-slate-600 font-medium">Halaman Sedang Dalam Pemeliharaan</p>
            <p class="text-sm text-slate-500 mb-8">Maaf, halaman ini sementara hanya dapat diakses oleh Departemen QMS & ICT.</p>

            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </main>

    @include('layouts.footer')
</div>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden"></div>
@endsection
