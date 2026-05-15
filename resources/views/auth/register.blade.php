<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join Excel Power | Premium Solutions</title>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    :root {
        --primary: #b91c1c;
        --primary-dark: #7f1d1d;
        --accent: #d4af37;
        --bg-dark: #450a0a;
        --text-main: #450a0a;
    }

    body {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: var(--bg-dark);
        background-image:
            radial-gradient(at 0% 0%, rgba(185, 28, 28, 0.15) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(212, 175, 55, 0.15) 0px, transparent 50%);
        font-family: 'Inter', sans-serif;
        padding: 20px;
    }

    .auth-container {
        width: 100%;
        max-width: 1100px;
        background: rgba(255, 255, 255, 0.98);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: row;
    }

    /* Brand Side Styling */
    .brand-panel {
        background: linear-gradient(135deg, var(--primary-dark) 0%, #450a0a 100%);
        color: #ffffff;
        padding: 60px;
        flex: 1;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow: hidden;
    }

    .brand-panel::before {
        content: "";
        position: absolute;
        top: -10%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: var(--accent);
        filter: blur(120px);
        opacity: 0.2;
        border-radius: 50%;
    }

    .brand-tagline {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: var(--accent);
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .brand-panel h1 {
        font-weight: 800;
        font-size: 3.5rem;
        line-height: 1.1;
        margin-bottom: 1.5rem;
        background: linear-gradient(to bottom right, #fff, #d4af37);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin-top: 2rem;
    }

    .feature-item {
        display: flex;
        align-items: center;
        margin-bottom: 1.2rem;
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .feature-item i {
        width: 32px;
        height: 32px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: var(--accent);
    }

    /* Form Side Styling */
    .form-panel {
        flex: 1.2;
        padding: 60px;
        background: white;
    }

    .form-header h2 {
        font-weight: 800;
        color: var(--bg-dark);
        margin-bottom: 0.5rem;
    }

    .form-header p {
        color: #7c2d12;
        font-size: 0.95rem;
        margin-bottom: 2.5rem;
    }

    .input-group-custom {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .input-group-custom label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: #7f1d1d;
        margin-bottom: 8px;
    }

    .input-wrapper {
        position: relative;
    }

    .input-wrapper i.bi:not(.toggle-password) {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #d4af37;
        transition: color 0.3s ease;
        z-index: 2;
    }

    .custom-control {
        width: 100%;
        /* Increased right padding so text doesn't overlap the eye icon */
        padding: 12px 50px 12px 48px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
    }

    .custom-control:focus {
        outline: none;
        background: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(185, 28, 28, 0.1);
    }

    /* Highlight icon when input is focused */
    .custom-control:focus+i {
        color: var(--primary) !important;
    }

    .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #d4af37;
        z-index: 10;
        padding: 8px;
        /* Larger click area */
    }

    .toggle-password:hover {
        color: var(--primary);
    }

    .register-btn {
        background: var(--primary);
        color: white;
        border: none;
        padding: 14px;
        border-radius: 12px;
        font-weight: 700;
        width: 100%;
        margin-top: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(185, 28, 28, 0.2);
    }

    .register-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(185, 28, 28, 0.3);
    }

    .strength-meter {
        height: 4px;
        width: 100%;
        background: #fde68a;
        border-radius: 2px;
        margin-top: 10px;
        overflow: hidden;
    }

    .strength-bar {
        height: 100%;
        width: 0;
        transition: all 0.4s ease;
    }

    .strength-text {
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 5px;
        display: block;
    }

    .error-msg {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 4px;
        font-weight: 500;
    }

    .login-link {
        text-align: center;
        margin-top: 2rem;
        font-size: 0.9rem;
        color: #7c2d12;
    }

    .login-link a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 700;
    }

    @media (max-width: 992px) {
        .auth-container {
            flex-direction: column;
            max-width: 500px;
        }

        .brand-panel,
        .form-panel {
            padding: 40px;
        }

        .brand-panel h1 {
            font-size: 2.5rem;
        }
    }
    </style>
</head>

<body>

    <div class="auth-container">
        <!-- Left Branding Panel -->
        <div class="brand-panel">
            <div class="brand-tagline">Excel Power</div>
            <h1>Build your<br>future with<br>precision.</h1>

            <ul class="feature-list">
                <li class="feature-item"><i class="bi bi-shield-check"></i> Enterprise-grade security</li>
                <li class="feature-item"><i class="bi bi-lightning-charge"></i> Real-time inventory tracking</li>
                <li class="feature-item"><i class="bi bi-cpu"></i> Smart logistics integration</li>
            </ul>

            <div style="margin-top: auto; opacity: 0.5; font-size: 0.8rem;">
                © 2026 Excel Power. All rights reserved.
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="form-panel">
            <div class="form-header">
                <h2>Create Account</h2>
                <p>Welcome! Enter your details to get started.</p>
            </div>

            @if ($errors->any())
            <div class="alert alert-danger border-0 rounded-3 small mb-4" style="background: #fef2f2; color: #991b1b;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Please fix the highlighted fields and try again.
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="input-group-custom">
                    <label>Full Name</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person"></i>
                        <input type="text" name="name" value="{{ old('name') }}" class="custom-control"
                            placeholder="John Doe" required autofocus>
                    </div>
                    @error('name') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group-custom">
                            <label>Username</label>
                            <div class="input-wrapper">
                                <i class="bi bi-at"></i>
                                <input type="text" name="username" value="{{ old('username') }}" class="custom-control"
                                    placeholder="johndoe88" required>
                            </div>
                            @error('username') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group-custom">
                            <label>Email Address</label>
                            <div class="input-wrapper">
                                <i class="bi bi-envelope"></i>
                                <input type="email" name="email" value="{{ old('email') }}" class="custom-control"
                                    placeholder="name@company.com" required>
                            </div>
                            @error('email') <div class="error-msg">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="input-group-custom">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="bi bi-key"></i>
                        <input type="password" id="password" name="password" class="custom-control"
                            placeholder="••••••••" onkeyup="updateStrength()" required>
                        <i class="bi bi-eye toggle-password" onclick="togglePass('password', this)"></i>
                    </div>
                    <div class="strength-meter">
                        <div id="strength-bar" class="strength-bar"></div>
                    </div>
                    <span id="strength-label" class="strength-text"></span>
                    @error('password') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <!-- Confirm Password -->
                <div class="input-group-custom">
                    <label>Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="bi bi-shield-lock"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="custom-control" placeholder="••••••••" required>
                        <i class="bi bi-eye toggle-password" onclick="togglePass('password_confirmation', this)"></i>
                    </div>
                    @error('password_confirmation') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="register-btn">Create Account</button>

                <div class="login-link">
                    Already have an account? <a href="{{ route('login') }}">Login here</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    function togglePass(id, icon) {
        const el = document.getElementById(id);
        const pos = el.selectionStart; // Save cursor position

        if (el.type === 'password') {
            el.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            el.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }

        el.focus(); // Keep input focused
        el.setSelectionRange(pos, pos); // Restore cursor position
    }

    function updateStrength() {
        const pass = document.getElementById('password').value;
        const bar = document.getElementById('strength-bar');
        const label = document.getElementById('strength-label');

        let score = 0;
        if (pass.length > 0) score++;
        if (pass.length >= 8) score++;
        if (/[A-Z]/.test(pass)) score++;
        if (/[0-9]/.test(pass)) score++;
        if (/[^A-Za-z0-9]/.test(pass)) score++;

        const states = [{
                color: '#fde68a',
                width: '0%',
                text: ''
            },
            {
                color: '#ef4444',
                width: '20%',
                text: 'Very Weak'
            },
            {
                color: '#f97316',
                width: '40%',
                text: 'Weak'
            },
            {
                color: '#eab308',
                width: '60%',
                text: 'Medium'
            },
            {
                color: '#22c55e',
                width: '80%',
                text: 'Strong'
            },
            {
                color: '#10b981',
                width: '100%',
                text: 'Excellent'
            }
        ];

        const state = states[score] || states[0];
        bar.style.width = state.width;
        bar.style.backgroundColor = state.color;
        label.innerText = state.text;
        label.style.color = state.color;
    }
    </script>

</body>

</html>
