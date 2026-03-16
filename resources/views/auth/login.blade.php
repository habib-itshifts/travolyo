<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign In - {{ config('app.name', 'Travolyo') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --auth-accent: #17c3ce;
            --auth-accent-dark: #11a8b2;
            --auth-text: #0f172a;
            --auth-muted: #64748b;
            --auth-border: #e2e8f0;
            --auth-surface: rgba(255, 255, 255, 0.96);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Inter", sans-serif;
            color: var(--auth-text);
            background:
                radial-gradient(circle at top left, rgba(23, 195, 206, 0.18), transparent 34%),
                radial-gradient(circle at bottom right, rgba(15, 23, 42, 0.12), transparent 28%),
                linear-gradient(135deg, #f8fbff 0%, #eef6fb 100%);
        }

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .auth-shell {
            width: 100%;
            max-width: 1020px;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            border-radius: 30px;
            overflow: hidden;
            background: var(--auth-surface);
            border: 1px solid rgba(255, 255, 255, 0.75);
            box-shadow: 0 30px 90px rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(14px);
        }

        .auth-side {
            position: relative;
            padding: 2.5rem;
            background: linear-gradient(160deg, #18c1cf 0%, #11a8b2 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .auth-side::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(255,255,255,0.24), transparent 24%),
                radial-gradient(circle at bottom left, rgba(255,255,255,0.14), transparent 28%);
            pointer-events: none;
        }

        .auth-side__content,
        .auth-side__footer {
            position: relative;
            z-index: 1;
        }

        .auth-brand {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
            text-decoration: none;
            color: #fff;
        }

        .auth-brand img {
            width: 134px;
            height: auto;
            filter: brightness(0) invert(1);
        }

        .auth-side__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.85rem;
            border-radius: 999px;
            background: rgba(255,255,255,0.12);
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .auth-side h1 {
            margin: 1.35rem 0 0.8rem;
            font-size: clamp(2rem, 4vw, 3rem);
            line-height: 1.05;
            font-weight: 800;
            max-width: 11ch;
        }

        .auth-side p {
            margin: 0;
            max-width: 34ch;
            color: rgba(255, 255, 255, 0.82);
            line-height: 1.75;
            font-size: 0.98rem;
        }

        .auth-side__footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            font-size: 0.84rem;
            color: rgba(255, 255, 255, 0.76);
        }

        .auth-card {
            padding: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.85);
        }

        .auth-panel {
            width: 100%;
            max-width: 430px;
        }

        .auth-panel__tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            padding: 0.4rem;
            border-radius: 999px;
            background: #f1f5f9;
        }

        .auth-panel__tab {
            flex: 1;
            border-radius: 999px;
            padding: 0.85rem 1rem;
            text-align: center;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            color: #475569;
        }

        .auth-panel__tab.is-active {
            background: #fff;
            color: var(--auth-accent);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }

        .auth-panel__title {
            margin: 0;
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .auth-panel__sub {
            margin: 0.55rem 0 1.75rem;
            color: var(--auth-muted);
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .auth-status,
        .auth-alert {
            border-radius: 16px;
            padding: 0.9rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .auth-status {
            background: #ecfeff;
            color: #0f766e;
            border: 1px solid #a5f3fc;
        }

        .auth-alert {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .auth-field + .auth-field {
            margin-top: 1rem;
        }

        .auth-label {
            display: block;
            margin-bottom: 0.45rem;
            font-size: 0.88rem;
            font-weight: 700;
            color: #334155;
        }

        .auth-input-group {
            position: relative;
        }

        .auth-input-icon {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1rem;
            pointer-events: none;
        }

        .auth-input {
            width: 100%;
            height: 54px;
            border-radius: 16px;
            border: 1px solid var(--auth-border);
            background: #fff;
            padding: 0 1rem 0 2.95rem;
            font-size: 0.96rem;
            color: var(--auth-text);
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
        }

        .auth-input--with-action {
            padding-right: 3.25rem;
        }

        .auth-input-action {
            position: absolute;
            top: 50%;
            right: 0.85rem;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 999px;
            background: transparent;
            color: #94a3b8;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        .auth-input-action:hover {
            background: #f1f5f9;
            color: #475569;
        }

        .auth-input:focus {
            outline: none;
            border-color: rgba(23, 195, 206, 0.65);
            box-shadow: 0 0 0 4px rgba(23, 195, 206, 0.12);
        }

        .auth-input.is-invalid {
            border-color: #f87171;
        }

        .auth-field__error {
            margin-top: 0.45rem;
            font-size: 0.82rem;
            color: #dc2626;
        }

        .auth-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1rem;
            margin-bottom: 1.45rem;
        }

        .auth-check {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            color: #475569;
            font-size: 0.92rem;
        }

        .auth-check input {
            width: 18px;
            height: 18px;
            accent-color: var(--auth-accent);
        }

        .auth-link {
            color: var(--auth-accent);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-link:hover {
            color: var(--auth-accent-dark);
        }

        .auth-btn {
            width: 100%;
            height: 54px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--auth-accent) 0%, var(--auth-accent-dark) 100%);
            color: #fff;
            font-size: 0.97rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            cursor: pointer;
            box-shadow: 0 18px 28px rgba(23, 195, 206, 0.22);
            transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
        }

        .auth-btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.03);
        }

        .auth-btn:focus-visible {
            outline: none;
            box-shadow: 0 0 0 4px rgba(23, 195, 206, 0.18);
        }

        .auth-bottom {
            margin-top: 1.2rem;
            text-align: center;
            color: var(--auth-muted);
            font-size: 0.9rem;
        }

        @media (max-width: 900px) {
            .auth-shell {
                grid-template-columns: 1fr;
                max-width: 520px;
            }

            .auth-side {
                min-height: 320px;
            }
        }

        @media (max-width: 575px) {
            .auth-page {
                padding: 1rem;
            }

            .auth-side,
            .auth-card {
                padding: 1.35rem;
            }

            .auth-panel__title {
                font-size: 1.7rem;
            }

            .auth-row {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <main class="auth-page">
        <div class="auth-shell">
            <section class="auth-side">
                <div class="auth-side__content">
                    <a href="{{ url('/') }}" class="auth-brand">
                        <img src="{{ asset('assets/images/logo/travolyo-logo.svg') }}" alt="Travolyo">
                    </a>
                    <span class="auth-side__eyebrow">
                        <i class="bi bi-compass"></i>
                        Smart travel access
                    </span>
                    <h1>Welcome back to Travolyo</h1>
                    <p>Sign in to manage bookings, keep your trips organized, and continue planning flights, hotels, and activities from one place.</p>
                </div>

                <div class="auth-side__footer">
                    <span>Secure traveler account</span>
                    <span>{{ now()->format('Y') }} Travolyo</span>
                </div>
            </section>

            <section class="auth-card">
                <div class="auth-panel">
                    <div class="auth-panel__tabs">
                        <span class="auth-panel__tab is-active">Sign In</span>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="auth-panel__tab">Register</a>
                        @else
                            <span class="auth-panel__tab">Register</span>
                        @endif
                    </div>

                    <h1 class="auth-panel__title">Sign in</h1>
                    <p class="auth-panel__sub">Use your email and password to access your bookings and saved travel plans.</p>

                    @if (session('status'))
                        <div class="auth-status">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="auth-alert">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="auth-field">
                            <label for="email" class="auth-label">Email</label>
                            <div class="auth-input-group">
                                <i class="bi bi-envelope auth-input-icon"></i>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="auth-input @error('email') is-invalid @enderror"
                                    placeholder="you@example.com"
                                    required
                                    autofocus
                                    autocomplete="username"
                                >
                            </div>
                            @error('email')
                                <div class="auth-field__error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="auth-field">
                            <label for="password" class="auth-label">Password</label>
                            <div class="auth-input-group">
                                <i class="bi bi-lock auth-input-icon"></i>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="auth-input auth-input--with-action @error('password') is-invalid @enderror"
                                    placeholder="Enter your password"
                                    required
                                    autocomplete="current-password"
                                >
                                <button type="button" class="auth-input-action" data-toggle-password="password" aria-label="Show password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="auth-field__error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="auth-row">
                            <label for="remember_me" class="auth-check">
                                <input id="remember_me" type="checkbox" name="remember">
                                <span>Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
                            @endif
                        </div>

                        <button type="submit" class="auth-btn">Sign In</button>
                    </form>

                    @if (Route::has('register'))
                        <div class="auth-bottom">
                            New here?
                            <a href="{{ route('register') }}" class="auth-link">Create an account</a>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </main>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach((button) => {
            button.addEventListener('click', () => {
                const inputId = button.getAttribute('data-toggle-password');
                const input = document.getElementById(inputId);
                const icon = button.querySelector('i');

                if (!input || !icon) {
                    return;
                }

                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
                button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            });
        });
    </script>
</body>
</html>
