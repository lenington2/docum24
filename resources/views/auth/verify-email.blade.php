<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Docum24 — Verifica email</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|instrument-serif:400i" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --cream: #FDFDFC; --ink: #1b1b18; --mid: #706f6c;
            --red: #F53003; --line: #e3e3e0; --white: #ffffff;
        }
        html { font-family: 'Instrument Sans', sans-serif; background: var(--cream); color: var(--ink); min-height: 100vh; }
        body { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2rem; }
        nav.topbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            display: flex; align-items: center; padding: 0 2rem; height: 56px;
            background: rgba(253,253,252,.9); backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--line);
        }
        .nav-logo { display: flex; align-items: center; gap: .45rem; font-weight: 600; font-size: 15px; text-decoration: none; color: var(--ink); }
        .nav-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--red); }
        .card {
            background: var(--white); border: 1px solid var(--line); border-radius: 16px;
            padding: 2.5rem; width: 100%; max-width: 440px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        }
        .icon-wrap {
            width: 52px; height: 52px; border-radius: 14px;
            background: #fff8f7; border: 1px solid rgba(245,48,3,.15);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem;
        }
        h1 { font-size: 1.3rem; font-weight: 600; letter-spacing: -.025em; margin-bottom: .5rem; }
        p { font-size: 13px; color: var(--mid); line-height: 1.7; margin-bottom: 1.5rem; }
        .alert-success {
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;
            padding: .75rem 1rem; font-size: 12px; color: #16a34a; margin-bottom: 1.25rem;
        }
        .btn-primary {
            width: 100%; padding: .7rem 1rem; background: var(--ink); color: #fff;
            border: none; border-radius: 6px; font-family: 'Instrument Sans', sans-serif;
            font-size: 14px; font-weight: 500; cursor: pointer; transition: background .15s;
        }
        .btn-primary:hover { background: #2e2e2a; }
        .links { display: flex; align-items: center; justify-content: center; gap: 1rem; margin-top: 1.25rem; }
        .links a, .links button {
            font-size: 12px; color: var(--mid); background: none; border: none;
            cursor: pointer; font-family: 'Instrument Sans', sans-serif;
            text-decoration: underline; text-underline-offset: 3px; transition: color .15s;
        }
        .links a:hover, .links button:hover { color: var(--ink); }
    </style>
</head>
<body>
    <nav class="topbar">
        <a class="nav-logo" href="/">
            <div class="nav-dot"></div>Docum24
        </a>
    </nav>

    <div class="card">
        <div class="icon-wrap">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#F53003" stroke-width="1.8">
                <rect x="2" y="4" width="20" height="16" rx="3"/>
                <path d="M2 7l10 7 10-7"/>
            </svg>
        </div>

        <h1>Verifica la tua email</h1>
        <p>Abbiamo inviato un link di verifica a <strong>{{ auth()->user()->email }}</strong>. Clicca sul link per accedere a Docum24.</p>

        @if (session('status') == 'verification-link-sent')
            <div class="alert-success">
                ✓ Nuovo link inviato con successo.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary">Invia di nuovo il link</button>
        </form>

        <div class="links">
            <a href="{{ route('profile.show') }}">Modifica profilo</a>
            <span style="color:var(--line);">|</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Esci</button>
            </form>
        </div>
    </div>
</body>
</html>
