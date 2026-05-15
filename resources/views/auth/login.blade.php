<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Excel Power | Secure Access</title>

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
        /* Mesh Gradient Background matching Register page */
        background-image:
            radial-gradient(at 0% 0%, rgba(185, 28, 28, 0.15) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(212, 175, 55, 0.15) 0px, transparent 50%);
        font-family: 'Inter', sans-serif;
        padding: 20px;
    }

    .auth-container {
        width: 100%;
        max-width: 1000px;
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

    /* Abstract Background Decor */
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
        margin-top: 1rem;
    }

    .feature-item {
        display: flex;
        align-items: center;
        margin-bottom: 1.2rem;
        font-size: 1rem;
        opacity: 0.85;
    }

    .feature-item i {
        width: 28px;
        height: 28px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        color: var(--accent);
    }

    /* Form Side Styling */
    .form-panel {
        flex: 1.1;
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

    .input-wrapper i.left-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #d4af37;
        transition: color 0.3s ease;
    }

    .custom-control {
        width: 100%;
        padding: 12px 16px 12px 48px;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .custom-control:focus {
        outline: none;
        background: #fff;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(185, 28, 28, 0.1);
    }

    .custom-control:focus+i.left-icon {
        color: var(--primary);
    }

    .toggle-password {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #d4af37;
        z-index: 10;
    }

    .login-btn {
        background: var(--primary);
        color: white;
        border: none;
        padding: 14px;
        border-radius: 12px;
        font-weight: 700;
        width: 100%;
        margin-top: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(185, 28, 28, 0.2);
    }

    .login-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(185, 28, 28, 0.3);
    }

    .error-msg {
        color: #ef4444;
        font-size: 0.75rem;
        margin-top: 4px;
        font-weight: 500;
    }

    .auth-footer {
        text-align: center;
        margin-top: 2rem;
        font-size: 0.9rem;
        color: #7c2d12;
    }

    .auth-footer a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 700;
    }

    .form-check-label {
        font-size: 0.85rem;
        color: #7c2d12;
        font-weight: 500;
    }

    .form-check-input:checked {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .form-check-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.18);
    }

    .forgot-link {
        font-size: 0.85rem;
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
    }

    @media (max-width: 992px) {
        .auth-container {
            flex-direction: column;
            max-width: 500px;
        }

        .brand-panel {
            padding: 40px;
        }

        .brand-panel h1 {
            font-size: 2.5rem;
        }

        .form-panel {
            padding: 40px;
        }
    }
    </style>
</head>

<body>

    <div class="auth-container">
        <!-- Left Branding Panel -->
        <div class="brand-panel">
            <div class="brand-tagline">Excel Power</div>
            <h1>Welcome<br>Back to<br>Precision.</h1>

            <ul class="feature-list">
                <li class="feature-item">
                    <i class="bi bi-person-check"></i>
                    Secure session management
                </li>
                <li class="feature-item">
                    <i class="bi bi-hdd-network"></i>
                    Connected hardware cloud
                </li>
                <li class="feature-item">
                    <i class="bi bi-activity"></i>
                    Live system diagnostics
                </li>
            </ul>

            <div style="margin-top: auto; opacity: 0.5; font-size: 0.8rem;">
                © 2024 Excel Power. Managed Logistics.
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="form-panel">
            <div class="form-header">
                <h2>System Login</h2>
                <p>Please authenticate to access your dashboard.</p>
            </div>

            <!-- Laravel Session Status -->
            @if(session('status'))
            <div class="alert alert-success border-0 rounded-3 small mb-4" style="background: #f0fdf4; color: #166534;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('status') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger border-0 rounded-3 small mb-4" style="background: #fef2f2; color: #991b1b;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Username/Email -->
                <div class="input-group-custom">
                    <label>Username or Email</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person left-icon"></i>
                        <input type="text" name="login" value="{{ old('login') }}" class="custom-control"
                            placeholder="Enter your credentials" required autofocus>
                    </div>
                    @error('login') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <!-- Password -->
                <div class="input-group-custom">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock left-icon"></i>
                        <input type="password" id="password" name="password" class="custom-control"
                            placeholder="••••••••" required>
                        <i class="bi bi-eye toggle-password" onclick="togglePass('password', this)"></i>
                    </div>
                    @error('password') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">
                            Stay signed in
                        </label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="login-btn">Sign In to Dashboard</button>

                <div class="auth-footer">
                    New to the system? <a href="{{ route('register') }}">Create an account</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    function togglePass(id, icon) {
        const el = document.getElementById(id);
        if (el.type === 'password') {
            el.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            el.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }
    </script>

</body>

</html>
