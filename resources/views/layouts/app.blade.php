<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FinanceTracker - @yield('title')</title>
    
    <!-- Bootstrap 5.3 CDN -->
        <!-- Incluir CSS del loader primero -->
    <link rel="stylesheet" href="{{ asset('assets/css/loader.css') }}" media="print" onload="this.media='all'"> 
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="{{ asset('assets/css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/utilities.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dark-theme.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/light-theme.css') }}">

    <!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    @yield('styles')
        <script>
        // Pequeño script inicial para mostrar el loader
        document.addEventListener('DOMContentLoaded', function() {
            if (!document.getElementById('custom-loader')) {
                const loader = document.createElement('div');
                loader.id = 'custom-loader';
                loader.innerHTML = `@include('components.loader')`;
                document.body.appendChild(loader);
            }
            document.getElementById('custom-loader').style.display = 'flex';
            document.body.classList.add('no-scroll');
        });
    </script>
</head>
<body>
    <!-- Incluir el componente de transición -->
    @include('components.loader')

    <div class="app-container">
        @include('components.shared.sidebar')
        
        <div class="main-content">
            @include('components.shared.header')
            
            <main class="content-wrapper">
                @yield('content')
            </main>
            
            
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (necesario para Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- JavaScript Core -->
    <script src="{{ asset('assets/js/core/api.js') }}"></script>
    <script src="{{ asset('assets/js/core/storage.js') }}"></script>
    <script src="{{ asset('assets/js/core/validator.js') }}"></script>
    
    <!-- JavaScript Modules -->
    <script src="{{ asset('assets/js/modules/darkMode.js') }}"></script>
    <script src="{{ asset('assets/js/modules/sidebar.js') }}"></script>



    <script src="{{ asset('assets/js/modules/loader.js') }}"></script>

    <script src="{{ asset('assets/js/modules/pageTransitions.js') }}"></script>
    <!-- Nuevo módulo para filtros -->
    <script src="{{ asset('assets/js/modules/transactionFilters.js') }}"></script>

    @yield('scripts')
</body>
</html>