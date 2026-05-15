<!DOCTYPE html>
<html>

<head>
    <title>Developer Dashboard</title>
</head>

<body>
    <h1>Developer Dashboard</h1>
    <p>Welcome, {{ auth()->user()->name }}</p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">⬅ Logout</button>
    </form>
</body>

</html>