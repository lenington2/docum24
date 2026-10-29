<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Docum24') }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, -apple-system, sans-serif; background: #f3f4f6; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .guest-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.08); padding: 2rem; width: 100%; max-width: 440px; margin: 1rem; }
        .guest-card input[type="email"], .guest-card input[type="password"], .guest-card input[type="text"] {
            width: 100%; padding: .6rem .8rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: .95rem; margin-top: .25rem;
        }
        .guest-card label { font-size: .875rem; font-weight: 500; color: #374151; }
        .guest-card button[type="submit"] {
            background: #2563eb; color: #fff; border: none; padding: .6rem 1.5rem; border-radius: 8px; font-size: .95rem; cursor: pointer; margin-top: 1rem;
        }
        .guest-card button[type="submit"]:hover { background: #1d4ed8; }
        .guest-card .error { color: #dc2626; font-size: .85rem; margin-bottom: .75rem; }
    </style>
</head>
<body>
    <div class="guest-card">
        {{ $slot }}
    </div>
</body>
</html>
