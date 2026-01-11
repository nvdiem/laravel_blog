<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} — Installation</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    <style>
        .install-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
        }

        .step {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            font-weight: bold;
            margin: 0 0.5rem;
            position: relative;
        }

        .step.active {
            background-color: #007bff;
            color: white;
        }

        .step.completed {
            background-color: #28a745;
            color: white;
        }

        .step-line {
            height: 2px;
            background-color: #e9ecef;
            flex: 1;
            max-width: 50px;
        }

        .step-line.completed {
            background-color: #28a745;
        }

        .install-card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.375rem;
        }

        .check-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
        }

        .check-status {
            margin-right: 0.5rem;
        }

        .check-passed .check-status {
            color: #28a745;
        }

        .check-failed .check-status {
            color: #dc3545;
        }

        .progress-container {
            margin-bottom: 1rem;
        }

        .install-footer {
            text-align: center;
            padding: 2rem 0;
            color: #6c757d;
        }
    </style>
</head>
<body class="bg-light">

    <!-- ===== INSTALL HEADER ===== -->
    <header class="install-header">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="display-4 mb-2">
                        <i class="fas fa-cogs me-3"></i>
                        {{ config('app.name') }} Installation
                    </h1>
                    <p class="lead mb-0">Welcome to the installation wizard. Follow the steps below to set up your CMS.</p>
                </div>
            </div>
        </div>
    </header>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="container">
        @yield('content')
    </main>

    <!-- ===== FOOTER ===== -->
    <footer class="install-footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
