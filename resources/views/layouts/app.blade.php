<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'GRACE'))</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('image/sai_logo_circle.png') }}">
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables-1.13.7.min.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables-1.13.7.min.js') }}"></script>

    <script>
        if (typeof $.fn.dataTable !== 'undefined') {
            $.fn.dataTable.ext.errMode = 'none';
        }
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            if (xhr.status === 419) {
                window.location.reload();
            }
        });
    </script>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.remove('preload');
        });

        window.addEventListener('load', function() {
            const loader = document.getElementById('page-loader');
            if (loader && !document.body.classList.contains('data-loading')) {
                loader.classList.add('hidden');
            }
        });

        window.addEventListener('pageshow', function(event) {
            const loader = document.getElementById('page-loader');
            if (loader) loader.classList.add('hidden');
        });

        window.addEventListener('beforeunload', function() {
            const loader = document.getElementById('page-loader');
            if (loader) loader.classList.remove('hidden');
        });

        window.toggleBodyScroll = function(showModal) {
            if (showModal) {
                document.body.classList.add('overflow-hidden');
            } else {
                const visibleModals = document.querySelectorAll('.fixed.inset-0.z-50:not(.hidden), [id*="Modal"]:not(.hidden)');
                if (visibleModals.length <= 1) {
                    document.body.classList.remove('overflow-hidden');
                }
            }
        };

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