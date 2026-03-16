<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Register - {{ config('app.name', 'Travolyo') }}</title>

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

        .auth-alert {
            border-radius: 16px;
            padding: 0.9rem 1rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
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

        .auth-input:focus {
            outline: none;
            border-color: rgba(23, 195, 206, 0.65);
            box-shadow: 0 0 0 4px rgba(23, 195, 206, 0.12);
        }

        .auth-input.is-invalid {
            border-color: #f87171;
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

        .auth-field__error {
            margin-top: 0.45rem;
            font-size: 0.82rem;
            color: #dc2626;
        }

        .auth-btn {
            width: 100%;
            height: 54px;
            margin-top: 1.45rem;
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

        .auth-link {
            color: var(--auth-accent);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-link:hover {
            color: var(--auth-accent-dark);
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
                    <h1>Create your Travolyo account</h1>
                    <p>Join Travolyo to save your bookings, manage future trips, and access flights, hotels, and activities from a single traveler dashboard.</p>
                </div>

                <div class="auth-side__footer">
                    <span>Fast sign up experience</span>
                    <span>{{ now()->format('Y') }} Travolyo</span>
                </div>
            </section>

            <section class="auth-card">
                <div class="auth-panel">
                    <div class="auth-panel__tabs">
                        <a href="{{ route('login') }}" class="auth-panel__tab">Sign In</a>
                        <span class="auth-panel__tab is-active">Register</span>
                    </div>

                    <h1 class="auth-panel__title">Register</h1>
                    <p class="auth-panel__sub">Create your account and start managing travel plans with the same streamlined experience as the sign-in flow.</p>

                    @if ($errors->any())
                        <div class="auth-alert">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="auth-field">
                            <label for="name" class="auth-label">Full Name</label>
                            <div class="auth-input-group">
                                <i class="bi bi-person auth-input-icon"></i>
                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="auth-input @error('name') is-invalid @enderror"
                                    placeholder="John Doe"
                                    required
                                    autofocus
                                    autocomplete="name"
                                >
                            </div>
                            @error('name')
                                <div class="auth-field__error">{{ $message }}</div>
                            @enderror
                        </div>

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
                                    placeholder="Create a password"
                                    required
                                    autocomplete="new-password"
                                >
                                <button type="button" class="auth-input-action" data-toggle-password="password" aria-label="Show password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="auth-field__error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="auth-field">
                            <label for="password_confirmation" class="auth-label">Confirm Password</label>
                            <div class="auth-input-group">
                                <i class="bi bi-shield-lock auth-input-icon"></i>
                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    class="auth-input auth-input--with-action @error('password_confirmation') is-invalid @enderror"
                                    placeholder="Repeat your password"
                                    required
                                    autocomplete="new-password"
                                >
                                <button type="button" class="auth-input-action" data-toggle-password="password_confirmation" aria-label="Show password confirmation">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="auth-field__error">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="auth-btn">Create Account</button>
                    </form>

                    <div class="auth-bottom">
                        Already registered?
                        <a href="{{ route('login') }}" class="auth-link">Sign in here</a>
                    </div>
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
