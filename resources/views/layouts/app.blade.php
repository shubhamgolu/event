<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        body {
            background: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .auth-card {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
        }
        
        /* Hide navbar on admin pages */
        .hide-on-admin nav.navbar {
            display: none !important;
        }
        .hide-on-admin main.py-4 {
            padding-top: 0 !important;
        }
    </style>
</head>
<body class="@if(request()->is('admin/*') || request()->routeIs('admin.*')) hide-on-admin @endif">
    <div id="app">
        <!-- Conditionally show navbar only on NON-admin pages -->
        @unless(request()->is('admin/*') || request()->routeIs('admin.*'))
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <!-- Brand/Logo -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fas fa-calendar-alt me-2"></i>{{ config('app.name') }}
                </a>
                
                <!-- Mobile Toggle Button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar Content -->
                <div class="collapse navbar-collapse" id="navbarContent">
                    <!-- Left Side Links -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            @if(Auth::user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link text-danger" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-crown me-1"></i>Admin Panel
                                    </a>
                                </li>
                            @endif
                            <!-- These links only show on user pages, NOT admin -->
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.events.index') }}">
                                    <i class="fas fa-calendar-alt me-1"></i>Events
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('user.events.my-events') }}">
                                    <i class="fas fa-calendar-check me-1"></i>My Events
                                </a>
                            </li>
                        @endauth
                    </ul>

                    <!-- Right Side Links -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <!-- Show when NOT logged in -->
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">
                                        <i class="fas fa-sign-in-alt me-1"></i>Login
                                    </a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">
                                        <i class="fas fa-user-plus me-1"></i>Register
                                    </a>
                                </li>
                            @endif
                        @else
                            <!-- Show when LOGGED IN -->
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" 
                                   data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-user-circle me-1"></i>
                                    {{ Auth::user()->name }}
                                    @if(Auth::user()->isAdmin())
                                        <span class="badge bg-danger ms-1">Admin</span>
                                    @endif
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <div class="dropdown-header">
                                        <strong>{{ Auth::user()->name }}</strong>
                                        <div class="small text-muted">{{ Auth::user()->email }}</div>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                    </a>
                                    
                                    @if(Auth::user()->isAdmin())
                                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                            <i class="fas fa-crown me-2"></i>Admin Panel
                                        </a>
                                    @endif
                                    
                                    <div class="dropdown-divider"></div>
                                    
                                    <!-- LOGOUT BUTTON -->
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </a>

                                    <!-- Hidden Logout Form -->
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        @endunless
        
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Simple form validation
        document.addEventListener('DOMContentLoaded', function() {
            // Enable Bootstrap tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            
            // Flash message auto-hide
            setTimeout(function() {
                var alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>
</body>
</html>