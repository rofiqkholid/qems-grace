@extends('layouts.app')

@section('content')
@include('layouts.sidebar')

<div class="lg:ml-20 min-h-screen flex flex-col bg-slate-50">
    @include('layouts.header')

    <!-- Page Content -->
    <main class="flex-1 p-6">
        <!-- Page Title -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Check Item Master</h1>
            <p class="text-slate-500 mt-1">Manage check item definitions and their associated details</p>
        </div>

        <!-- Main Card -->
        
    </main>
    @include('layouts.footer')
</div>
@endsection