@extends('layouts.app')

@section('title', 'External Audit Form')

@section('content')
@include('layouts.sidebar')
@include('components.toast')

<!-- Main Content -->
<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">External Audit Form</h1>
            <p class="text-slate-500 mt-1">Manage External Audit agendas, compliance, and findings</p>
        </div>

        <!-- Main Card Container -->
        <div class="bg-white rounded-xl border border-slate-200 p-8 shadow-sm">
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-4 text-2xl">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
                <h2 class="text-xl font-semibold text-slate-800 mb-2">Ini halaman External Audit Form</h2>
                <p class="text-slate-500 max-w-md text-sm">
                    Modul ini sedang disiapkan untuk proses pengelolaan audit eksternal.
                </p>
            </div>
        </div>
    </main>
</div>
@endsection
