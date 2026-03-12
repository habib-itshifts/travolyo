{{-- Auth Modal (Sign In / Register) --}}
<div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px">
        <div class="modal-content rounded-4 border-0 shadow-lg" style="padding:1.5rem 1.75rem 2rem">

            {{-- Close --}}
            <div class="d-flex justify-content-end mb-1">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Tab switcher --}}
            <div class="d-flex border-bottom mb-4" id="authTabBar">
                <button class="auth-tab-btn flex-grow-1 pb-2 border-0 bg-transparent fw-600 fs-6 auth-tab-active" data-panel="signin-panel" id="btn-signin-tab">
                    Sign In
                </button>
                <button class="auth-tab-btn flex-grow-1 pb-2 border-0 bg-transparent fw-600 fs-6" data-panel="register-panel" id="btn-register-tab">
                    Register
                </button>
            </div>

            {{-- ── SIGN IN PANEL ── --}}
            <div id="signin-panel" class="auth-panel">
                <h5 class="fw-700 text-center mb-1">Welcome back</h5>
                <p class="text-muted text-center mb-4" style="font-size:.9rem">Sign in to access your bookings and saved trips</p>

                <form id="signin-form" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-500 small">Email</label>
                        <div class="input-group">
                            <input type="email" name="email" id="signin-email"
                                   class="form-control rounded-3"
                                   placeholder="you@example.com" autocomplete="email">
                            <span class="input-group-text bg-white border-start-0 rounded-end-3">
                                <i class="bi bi-envelope text-muted"></i>
                            </span>
                        </div>
                        <div class="text-danger small mt-1" id="signin-email-err"></div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-500 small">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="signin-pwd"
                                   class="form-control rounded-3"
                                   placeholder="••••••••" autocomplete="current-password">
                            <span class="input-group-text bg-white border-start-0 rounded-end-3"
                                  style="cursor:pointer" onclick="authTogglePwd('signin-pwd',this)">
                                <i class="bi bi-eye text-muted"></i>
                            </span>
                        </div>
                        <div class="text-danger small mt-1" id="signin-pwd-err"></div>
                    </div>

                    <div class="text-end mb-3">
                        <a href="{{ route('password.request') }}" class="small text-decoration-none fw-500" style="color:#17C3CE">
                            Forgot password?
                        </a>
                    </div>

                    <div class="text-danger small mb-2" id="signin-general-err"></div>

                    <button type="submit" id="signin-submit-btn"
                            class="btn w-100 fw-600 text-white py-2 rounded-pill mb-1"
                            style="background:#17C3CE;border:none">
                        Sign In
                    </button>
                </form>

                <div class="d-flex align-items-center my-3 text-muted" style="font-size:.82rem">
                    <hr class="flex-grow-1 me-2"><span>or continue with</span><hr class="flex-grow-1 ms-2">
                </div>

                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-outline-secondary flex-grow-1 d-flex align-items-center justify-content-center gap-2 rounded-pill fw-500" style="font-size:.88rem">
                        <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.08 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-3.59-13.46-8.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                        Google
                    </a>
                    <a href="#" class="btn btn-outline-secondary flex-grow-1 d-flex align-items-center justify-content-center gap-2 rounded-pill fw-500" style="font-size:.88rem">
                        <i class="bi bi-facebook" style="color:#1877F2;font-size:1.1rem"></i>
                        Facebook
                    </a>
                </div>

                <p class="text-muted text-center mt-3" style="font-size:.75rem">
                    By continuing, you agree to our
                    <a href="#" class="text-decoration-none fw-500" style="color:#17C3CE">Terms of Service</a> and
                    <a href="#" class="text-decoration-none fw-500" style="color:#17C3CE">Privacy Policy</a>
                </p>
            </div>

            {{-- ── REGISTER PANEL ── --}}
            <div id="register-panel" class="auth-panel" style="display:none">
                <h5 class="fw-700 text-center mb-1">Create an account</h5>
                <p class="text-muted text-center mb-4" style="font-size:.9rem">Join us to start planning your perfect trip</p>

                <form id="register-form" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-500 small">Full Name</label>
                        <input type="text" name="name" id="reg-name"
                               class="form-control rounded-3"
                               placeholder="John Doe" autocomplete="name">
                        <div class="text-danger small mt-1" id="reg-name-err"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-500 small">Email</label>
                        <div class="input-group">
                            <input type="email" name="email" id="reg-email"
                                   class="form-control rounded-3"
                                   placeholder="you@example.com" autocomplete="email">
                            <span class="input-group-text bg-white border-start-0 rounded-end-3">
                                <i class="bi bi-envelope text-muted"></i>
                            </span>
                        </div>
                        <div class="text-danger small mt-1" id="reg-email-err"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-500 small">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="reg-pwd"
                                   class="form-control rounded-3"
                                   placeholder="••••••••" autocomplete="new-password">
                            <span class="input-group-text bg-white border-start-0 rounded-end-3"
                                  style="cursor:pointer" onclick="authTogglePwd('reg-pwd',this)">
                                <i class="bi bi-eye text-muted"></i>
                            </span>
                        </div>
                        <div class="text-danger small mt-1" id="reg-pwd-err"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-500 small">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="reg-pwd-confirm"
                               class="form-control rounded-3"
                               placeholder="••••••••" autocomplete="new-password">
                    </div>

                    <div class="text-danger small mb-2" id="reg-general-err"></div>

                    <button type="submit" id="register-submit-btn"
                            class="btn w-100 fw-600 text-white py-2 rounded-pill mb-1"
                            style="background:#17C3CE;border:none">
                        Create Account
                    </button>
                </form>

                <div class="d-flex align-items-center my-3 text-muted" style="font-size:.82rem">
                    <hr class="flex-grow-1 me-2"><span>or continue with</span><hr class="flex-grow-1 ms-2">
                </div>

                <div class="d-flex gap-3">
                    <a href="#" class="btn btn-outline-secondary flex-grow-1 d-flex align-items-center justify-content-center gap-2 rounded-pill fw-500" style="font-size:.88rem">
                        <svg width="18" height="18" viewBox="0 0 48 48"><path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.08 17.74 9.5 24 9.5z"/><path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/><path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/><path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-3.59-13.46-8.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/></svg>
                        Google
                    </a>
                    <a href="#" class="btn btn-outline-secondary flex-grow-1 d-flex align-items-center justify-content-center gap-2 rounded-pill fw-500" style="font-size:.88rem">
                        <i class="bi bi-facebook" style="color:#1877F2;font-size:1.1rem"></i>
                        Facebook
                    </a>
                </div>

                <p class="text-muted text-center mt-3" style="font-size:.75rem">
                    By continuing, you agree to our
                    <a href="#" class="text-decoration-none fw-500" style="color:#17C3CE">Terms of Service</a> and
                    <a href="#" class="text-decoration-none fw-500" style="color:#17C3CE">Privacy Policy</a>
                </p>
            </div>

        </div>
    </div>
</div>

<style>
.auth-tab-btn {
    color: #6c757d;
    border-bottom: 2px solid transparent !important;
    transition: color .2s, border-color .2s;
    margin-bottom: -1px;
}
.auth-tab-btn.auth-tab-active {
    color: #17C3CE;
    border-bottom: 2px solid #17C3CE !important;
}
</style>

<script>
(function () {
    // ── Tab switching ──
    function switchAuthTab(panelId) {
        document.querySelectorAll('.auth-panel').forEach(function(p){ p.style.display = 'none'; });
        document.getElementById(panelId).style.display = 'block';
        document.querySelectorAll('.auth-tab-btn').forEach(function(b){
            b.classList.toggle('auth-tab-active', b.dataset.panel === panelId);
        });
        clearAuthErrors();
    }

    document.querySelectorAll('.auth-tab-btn').forEach(function(btn){
        btn.addEventListener('click', function(){ switchAuthTab(btn.dataset.panel); });
    });

    // ── Clear errors ──
    function clearAuthErrors() {
        ['signin-email-err','signin-pwd-err','signin-general-err',
         'reg-name-err','reg-email-err','reg-pwd-err','reg-general-err'].forEach(function(id){
            var el = document.getElementById(id);
            if (el) el.textContent = '';
        });
    }

    // ── Password toggle ──
    window.authTogglePwd = function(inputId, btn) {
        var input = document.getElementById(inputId);
        var icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    };

    // ── CSRF helper ──
    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    }

    // ── Generic AJAX form submit ──
    function submitAuthForm(formId, url, fieldMap, generalErrId, submitBtnId) {
        var form    = document.getElementById(formId);
        var btn     = document.getElementById(submitBtnId);
        var origTxt = btn.textContent.trim();

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            clearAuthErrors();

            var data = new FormData(form);
            btn.disabled = true;
            btn.textContent = 'Please wait…';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: data
            })
            .then(function(res) {
                return res.json().then(function(json){ return { status: res.status, body: json }; });
            })
            .then(function(res) {
                if (res.status === 200 && res.body.success) {
                    window.location.reload();
                    return;
                }
                // Validation errors (422)
                var errors = res.body.errors || {};
                Object.keys(fieldMap).forEach(function(field){
                    if (errors[field] && errors[field][0]) {
                        var el = document.getElementById(fieldMap[field]);
                        if (el) el.textContent = errors[field][0];
                    }
                });
                // General / credentials error
                if (res.body.message && Object.keys(errors).length === 0) {
                    document.getElementById(generalErrId).textContent = res.body.message;
                }
                btn.disabled = false;
                btn.textContent = origTxt;
            })
            .catch(function() {
                document.getElementById(generalErrId).textContent = 'Something went wrong. Please try again.';
                btn.disabled = false;
                btn.textContent = origTxt;
            });
        });
    }

    // ── Wire up Sign In form ──
    submitAuthForm(
        'signin-form',
        '{{ route("login") }}',
        { email: 'signin-email-err', password: 'signin-pwd-err' },
        'signin-general-err',
        'signin-submit-btn'
    );

    // ── Wire up Register form ──
    submitAuthForm(
        'register-form',
        '{{ route("register") }}',
        { name: 'reg-name-err', email: 'reg-email-err', password: 'reg-pwd-err' },
        'reg-general-err',
        'register-submit-btn'
    );

    // ── Open modal on correct tab from outside ──
    window.openAuthModal = function(tab) {
        switchAuthTab(tab === 'register' ? 'register-panel' : 'signin-panel');
        var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('authModal'));
        modal.show();
    };
})();
</script>
