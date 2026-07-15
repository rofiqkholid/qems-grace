<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'GRACE'))</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('image/sai_logo_circle.png') }}">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- DataTables CSS (loaded before Vite so custom styles can override) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <!-- Viewer.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.6/viewer.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.6/viewer.min.js"></script>

    <!-- Vite Assets (loads last to override CDN styles) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Preload styles to prevent transition flickers on load -->
    <style>
        .preload, .preload * {
            -webkit-transition: none !important;
            -moz-transition: none !important;
            -ms-transition: none !important;
            -o-transition: none !important;
            transition: none !important;
        }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-900 preload">
    @yield('content')

    @if(!isset($hideCentralToast) || !$hideCentralToast)
        @include('components.central-toast')
    @endif

    <!-- jQuery and DataTables must load before @stack('scripts') -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.remove('preload');
        });

        // Global Loading Bar Logic
        window.addEventListener('load', function() {
            const loader = document.getElementById('page-loader');
            if (loader && !document.body.classList.contains('data-loading')) {
                loader.classList.add('hidden');
            }
        });

        // Handle bfcache (back/forward navigation)
        window.addEventListener('pageshow', function(event) {
            const loader = document.getElementById('page-loader');
            if (loader) loader.classList.add('hidden');
        });

        // Show loader on navigation
        window.addEventListener('beforeunload', function() {
            const loader = document.getElementById('page-loader');
            if (loader) loader.classList.remove('hidden');
        });
        // Global DataTable Resize Adjustment
        $(window).on('resize', function() {
            $('.dataTable').each(function() {
                if ($.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable().columns.adjust();
                }
            });
        });
    </script>
</body>

</html>