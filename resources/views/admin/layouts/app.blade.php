<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Admin Panel' }} - Excel Power POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts, Bootstrap & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
    :root {
        --primary: #b91c1c;
        --primary-dark: #7f1d1d;
        --accent: #d4af37;
        --bg-dark: #450a0a;
        --sidebar-bg: #7f1d1d;
        --sidebar-card: #991b1b;
        --text-light: #fff7ed;
        --text-muted: #fde68a;
        --body-bg: #fffaf0;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        background: var(--body-bg);
        font-family: 'Inter', sans-serif;
        color: #450a0a;
    }

    .admin-wrapper {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 290px;
        background:
            radial-gradient(circle at top left, rgba(185, 28, 28, 0.25), transparent 35%),
            linear-gradient(180deg, var(--sidebar-bg), #450a0a);
        color: var(--text-light);
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        padding: 24px 18px;
        overflow-y: auto;
        box-shadow: 10px 0 30px rgba(15, 23, 42, 0.15);
        z-index: 1000;
    }

    .brand-box {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 22px;
        padding: 20px;
        margin-bottom: 26px;
        position: relative;
        overflow: hidden;
    }

    .brand-box::after {
        content: "";
        position: absolute;
        width: 130px;
        height: 130px;
        background: var(--accent);
        right: -55px;
        top: -55px;
        filter: blur(45px);
        opacity: 0.35;
        border-radius: 50%;
    }

    .brand-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 1;
    }

    .brand-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--primary), var(--accent));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.4rem;
        box-shadow: 0 10px 25px rgba(185, 28, 28, 0.35);
    }

    .brand-title {
        font-size: 1.15rem;
        font-weight: 800;
        margin: 0;
        line-height: 1.1;
    }

    .brand-subtitle {
        margin: 4px 0 0;
        font-size: 0.76rem;
        color: var(--text-muted);
        font-weight: 500;
    }

    .menu-title {
        font-size: 0.72rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 800;
        margin: 22px 12px 10px;
    }

    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-menu li {
        margin-bottom: 7px;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 13px;
        text-decoration: none;
        color: #fff7ed;
        padding: 13px 14px;
        border-radius: 15px;
        font-weight: 650;
        font-size: 0.93rem;
        transition: all 0.25s ease;
        border: 1px solid transparent;
    }

    .sidebar-link i {
        width: 34px;
        height: 34px;
        border-radius: 11px;
        background: rgba(255, 255, 255, 0.06);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent);
        font-size: 1rem;
        transition: all 0.25s ease;
    }

    .sidebar-link:hover {
        background: rgba(255, 255, 255, 0.08);
        color: white;
        transform: translateX(4px);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .sidebar-link:hover i {
        background: linear-gradient(135deg, var(--primary), var(--accent));
        color: white;
    }

    .sidebar-link.active {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        box-shadow: 0 12px 25px rgba(185, 28, 28, 0.28);
    }

    .sidebar-link.active i {
        background: rgba(255, 255, 255, 0.18);
        color: white;
    }

    .sidebar-footer {
        margin-top: 26px;
        background: var(--sidebar-card);
        border-radius: 18px;
        padding: 16px;
        border: 1px solid rgba(255, 255, 255, 0.07);
    }

    .user-name {
        font-size: 0.9rem;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .user-role {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-bottom: 14px;
    }

    .logout-btn {
        width: 100%;
        border: none;
        border-radius: 13px;
        background: rgba(239, 68, 68, 0.13);
        color: #fecaca;
        padding: 10px 12px;
        font-weight: 700;
        transition: 0.25s ease;
    }

    .logout-btn:hover {
        background: #ef4444;
        color: white;
    }

    .main-content {
        margin-left: 290px;
        width: calc(100% - 290px);
        min-height: 100vh;
    }

    .topbar {
        height: 76px;
        background: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid #fde68a;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 34px;
        position: sticky;
        top: 0;
        z-index: 500;
    }

    .page-title {
        font-size: 1.35rem;
        font-weight: 850;
        margin: 0;
        color: #450a0a;
    }

    .page-subtitle {
        color: #7c2d12;
        font-size: 0.86rem;
        margin: 3px 0 0;
    }

    .content-area {
        padding: 34px;
    }

    .content-card {
        background: white;
        border-radius: 22px;
        border: 1px solid #fde68a;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
        padding: 26px;
    }

    .text-bg-primary {
        background: linear-gradient(135deg, var(--primary), var(--accent)) !important;
        color: white !important;
    }

    .page-link {
        color: var(--primary);
    }

    .active>.page-link,
    .page-link.active {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .stat-card {
        background: white;
        border: 1px solid #fde68a;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 12px 26px rgba(15, 23, 42, 0.06);
        transition: 0.25s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--primary), var(--accent));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.45rem;
        margin-bottom: 16px;
    }

    .stat-title {
        color: #7c2d12;
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .stat-value {
        color: #450a0a;
        font-size: 1.8rem;
        font-weight: 850;
        margin: 0;
    }

    @media (max-width: 992px) {
        .sidebar {
            width: 250px;
        }

        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            position: relative;
            width: 100%;
            min-height: auto;
        }

        .admin-wrapper {
            flex-direction: column;
        }

        .main-content {
            margin-left: 0;
            width: 100%;
        }

        .topbar {
            padding: 0 20px;
        }

        .content-area {
            padding: 20px;
        }
    }
    </style>
</head>

<body>
    <div class="admin-wrapper">

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="brand-box">
                <div class="brand-logo">
                    <div class="brand-icon">
                        <i class="bi bi-shop-window"></i>
                    </div>
                    <div>
                        <h1 class="brand-title">Excel Power</h1>
                        <p class="brand-subtitle">POS Admin Control</p>
                    </div>
                </div>
            </div>

            @foreach(config('admin_permissions', []) as $group)
            @php
            $visibleItems = collect($group['items'])
            ->filter(fn ($item) => auth()->user()->can($item['permission']));
            @endphp

            @if($visibleItems->isNotEmpty())
            <div class="menu-title">{{ $group['group'] }}</div>

            <ul class="sidebar-menu">
                @foreach($visibleItems as $item)
                <li>
                    <a href="{{ route($item['route']) }}"
                        class="sidebar-link {{ request()->routeIs($item['route_is']) ? 'active' : '' }}">
                        <i class="{{ $item['icon'] }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                </li>

                @endforeach

            </ul>
            @endif
            @endforeach
            <div class="sidebar-footer">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">
                    Logged in as {{ auth()->user()->roles->pluck('name')->first() ?? 'User' }}
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="bi bi-box-arrow-left me-1"></i>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <div class="topbar">
                <div>
                    <h2 class="page-title">@yield('page-title', 'Admin Panel')</h2>
                    <p class="page-subtitle">@yield('page-subtitle', 'Manage your POS system')</p>
                </div>

                <div class="d-none d-md-flex align-items-center gap-2">
                    <span class="badge rounded-pill text-bg-primary px-3 py-2">
                        <i class="bi bi-shield-check me-1"></i> Admin Access
                    </span>
                </div>
            </div>

            <div class="content-area">
                @yield('content')
            </div>
        </main>

    </div>
</body>

</html>
