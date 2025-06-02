<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        .error { color: red; }
        .form-group { margin-bottom: 1rem; }
    </style>
</head>
<body>
    <h1>Login</h1>

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <form action="/login" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="/register">Register</a></p>
</body>
</html>
