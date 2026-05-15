<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Excel Power</title>

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

    .toggle-password {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #d4af37;
        z-index: 10;
    }

    .submit-btn {
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

    .submit-btn:hover {
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

    .status-msg {
        background: #dcfce7;
        color: #166534;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 20px;
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
            <h1>Secure<br>Identity<br>Update.</h1>

            <ul class="feature-list">
                <li class="feature-item">
                    <i class="bi bi-shield-lock"></i>
                    Double-layer encryption
                </li>
                <li class="feature-item">
                    <i class="bi bi-key"></i>
                    One-time reset token
                </li>
                <li class="feature-item">
                    <i class="bi bi-check2-circle"></i>
                    Instant synchronization
                </li>
            </ul>

            <div style="margin-top: auto; opacity: 0.5; font-size: 0.8rem;">
                © 2024 Excel Power. Security Protocol.
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="form-panel">
            <div class="form-header">
                <h2>Reset Password</h2>
                <p>Please enter your email and choose a new secure password.</p>
            </div>

            @if (session('status'))
            <div class="status-msg">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div class="input-group-custom">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <i class="bi bi-envelope left-icon"></i>
                        <input type="email" name="email" value="{{ old('email', $request->email) }}"
                            class="custom-control" placeholder="name@company.com" required autofocus
                            autocomplete="username">
                    </div>
                    @error('email')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="input-group-custom">
                    <label>New Password</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock left-icon"></i>
                        <input type="password" id="password" name="password" class="custom-control"
                            placeholder="••••••••" required autocomplete="new-password">
                        <i class="bi bi-eye toggle-password" onclick="togglePass('password', this)"></i>
                    </div>
                    @error('password')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="input-group-custom">
                    <label>Confirm New Password</label>
                    <div class="input-wrapper">
                        <i class="bi bi-shield-lock left-icon"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="custom-control" placeholder="••••••••" required autocomplete="new-password">
                        <i class="bi bi-eye toggle-password" onclick="togglePass('password_confirmation', this)"></i>
                    </div>
                    @error('password_confirmation')
                    <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="submit-btn">Update Password & Login</button>
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
