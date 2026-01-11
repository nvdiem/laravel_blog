<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Log In') }} ‹ {{ config('brand.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0f0f1;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.13);
            overflow: hidden;
        }

        .login-header {
            background: #ffffff;
            padding: 24px 24px 0;
            text-align: center;
            border-bottom: none;
        }

        .login-logo {
            font-size: 20px;
            font-weight: 600;
            color: #3c434a;
            margin: 0 0 16px 0;
            letter-spacing: -0.5px;
        }

        .login-logo span {
            color: #2271b1;
        }

        .login-form {
            padding: 24px;
            padding-top: 0;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            margin-bottom: 4px;
            font-size: 14px;
            font-weight: 600;
            color: #3c434a;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #8c8f94;
            border-radius: 4px;
            font-size: 16px;
            line-height: 1.5;
            background-color: #ffffff;
            transition: border-color 0.1s ease-in-out, box-shadow 0.1s ease-in-out;
        }

        .form-control:focus {
            border-color: #2271b1;
            box-shadow: 0 0 0 2px rgba(34, 113, 177, 0.2);
            outline: none;
        }

        .form-control.error {
            border-color: #d63638;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #50575e;
            cursor: pointer;
            padding: 4px;
            border-radius: 2px;
            transition: color 0.1s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: #2271b1;
            background-color: rgba(34, 113, 177, 0.1);
        }

        .password-toggle:focus {
            outline: 2px solid #2271b1;
            outline-offset: 2px;
        }

        .password-toggle svg {
            width: 18px;
            height: 18px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }

        .checkbox-input {
            margin-right: 8px;
        }

        .checkbox-label {
            font-size: 14px;
            color: #3c434a;
            margin: 0;
        }

        .login-button {
            width: 100%;
            padding: 8px 12px;
            background-color: #2271b1;
            border: 1px solid #2271b1;
            border-radius: 4px;
            color: #ffffff;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.5;
            cursor: pointer;
            transition: background-color 0.1s ease-in-out, border-color 0.1s ease-in-out;
        }

        .login-button:hover {
            background-color: #135e96;
            border-color: #135e96;
        }

        .login-button:focus {
            outline: 2px solid #2271b1;
            outline-offset: 2px;
        }

        .login-links {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dcdcde;
        }

        .login-links a {
            color: #2271b1;
            text-decoration: none;
            font-size: 14px;
            margin: 0 8px;
        }

        .login-links a:hover {
            color: #135e96;
        }

        .alert {
            margin-bottom: 20px;
            padding: 12px 16px;
            border-radius: 4px;
            font-size: 14px;
            border: none;
        }

        .alert-danger {
            background-color: #fce4e4;
            color: #d63638;
        }

        .alert-success {
            background-color: #d1f0e1;
            color: #1e8c5e;
        }

        .back-link {
            position: fixed;
            top: 20px;
            left: 20px;
            color: #2271b1;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            color: #135e96;
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 10px;
            }

            .back-link {
                position: static;
                display: block;
                margin-bottom: 20px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <a href="{{ url('/') }}" class="back-link">← {{ __('Back to') }} {{ config('brand.name') }}</a>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1 class="login-logo">
                    <span></></span> {{ config('brand.name') }}
                </h1>
            </div>

            <div class="login-form">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @if($errors->has('email') || $errors->has('password'))
                            <strong>Error:</strong> Invalid username or password.
                        @else
                            @foreach($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        @endif
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">Username or Email Address</label>
                        <input type="text"
                               class="form-control {{ $errors->has('email') ? 'error' : '' }}"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="username">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-wrapper">
                            <input type="password"
                                   class="form-control {{ $errors->has('password') ? 'error' : '' }}"
                                   id="password"
                                   name="password"
                                   required
                                   autocomplete="current-password">
                            <button type="button"
                                    class="password-toggle"
                                    id="password-toggle"
                                    aria-label="Toggle password visibility">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox"
                               class="checkbox-input"
                               id="remember"
                               name="remember">
                        <label for="remember" class="checkbox-label">Remember Me</label>
                    </div>

                    <button type="submit" class="login-button">
                        Log In
                    </button>
                </form>

                <div class="login-links">
                    <!-- Password reset functionality not implemented -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('password-toggle');

            if (passwordInput && passwordToggle) {
                passwordToggle.addEventListener('click', function() {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';

                    // Update the icon based on visibility state
                    const iconPath = isPassword
                        ? '<path d="M17.293 13.293A8 8 0 0 0 6.707 2.707a8.001 8.001 0 0 0 10.586 10.586z"></path><path d="M2 2l16 16"></path>'
                        : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';

                    passwordToggle.innerHTML = `
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            ${iconPath}
                        </svg>
                    `;

                    // Update aria-label for accessibility
                    passwordToggle.setAttribute('aria-label',
                        isPassword ? 'Hide password' : 'Show password'
                    );
                });
            }
        });
    </script>
</body>
</html>
