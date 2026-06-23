@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 flex flex-col items-center justify-center p-6 text-center">
        <div class="max-w-md w-full">
            <!-- 404 Illustration placeholder or simple SVG -->
            <div class="w-32 h-32 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h1 class="text-4xl font-bold text-slate-800 mb-2">Error 404</h1>
            <p class="text-lg text-slate-600 font-medium">Halaman Tidak Tersedia</p>
            <p class="text-sm text-slate-500 mb-8">Page Not Found</p>

            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition-colors">
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

