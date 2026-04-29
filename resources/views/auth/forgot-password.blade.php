<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Docum24 — Recupera Password</title>
    <link rel="shortcut icon" type="image/png" href="{{ url('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|instrument-serif:400i" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --cream: #FDFDFC; --ink: #1b1b18; --mid: #706f6c;
            --dim: #a8a7a3; --red: #F53003; --line: #e3e3e0;
            --white: #ffffff;
        }
        html { font-family: 'Instrument Sans', sans-serif; background: var(--cream); color: var(--ink); min-height: 100vh; }
        body { min-height: 100vh; display: flex; flex-direction: column; }

        nav.topbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            display: flex; align-items: center; padding: 0 2rem; height: 56px;
            background: rgba(253,253,252,.9); backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--line);
        }
        .nav-logo { display: flex; align-items: center; gap: .45rem; font-weight: 600; font-size: 15px; text-decoration: none; color: var(--ink); }
        .nav-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--red); }

        .page-wrap { flex: 1; display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; padding-top: 56px; }
        @media(max-width: 860px){ .page-wrap { grid-template-columns: 1fr; } }

        .left-panel {
            background: var(--ink); color: #fff; padding: 4rem 3.5rem;
            display: flex; flex-direction: column; justify-content: center;
            position: sticky; top: 56px; height: calc(100vh - 56px);
        }
        @media(max-width: 860px){ .left-panel { display: none; } }

        .left-panel h2 { font-size: 2.2rem; font-weight: 600; letter-spacing: -.03em; line-height: 1.1; margin-bottom: 1.2rem; }
        .left-panel h2 em { font-family: 'Instrument Serif', serif; font-style: italic; font-weight: 400; color: #7a7a72; }

        .right-panel {
            background: var(--white); display: flex; flex-direction: column;
            justify-content: center; padding: 3rem 4rem; min-height: calc(100vh - 56px);
        }
        @media(max-width: 500px){ .right-panel { padding: 2rem 1.5rem; } }

        .form-header { margin-bottom: 2rem; }
        .form-header h1 { font-size: 1.6rem; font-weight: 600; letter-spacing: -.025em; margin-bottom: .6rem; }
        .form-header p { font-size: 13px; color: var(--mid); line-height: 1.5; }

        .field { margin-bottom: 1.5rem; }
        .field label { display: block; font-size: 12px; font-weight: 500; color: var(--mid); margin-bottom: .5rem; }
        .field input {
            width: 100%; padding: .75rem .9rem; font-family: 'Instrument Sans', sans-serif; font-size: 14px;
            color: var(--ink); background: var(--cream); border: 1px solid var(--line); border-radius: 8px; outline: none; transition: 0.2s;
        }
        .field input:focus { border-color: var(--ink); box-shadow: 0 0 0 3px rgba(27,27,24,.06); }

        .btn-primary {
            width: 100%; padding: .8rem; background: var(--ink); color: #fff; border: none; border-radius: 8px;
            font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.2s;
        }
        .btn-primary:hover { background: #2e2e2a; transform: translateY(-1px); }

        .status-msg { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; font-size: 13px; color: #15803d; }
        .server-errors { background: #fff2f2; border: 1px solid #ffd0c8; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; font-size: 13px; color: var(--red); }

        .back-link { margin-top: 1.5rem; text-align: center; }
        .back-link a { font-size: 13px; color: var(--mid); text-decoration: none; }
        .back-link a:hover { color: var(--ink); text-decoration: underline; }
    </style>
</head>
<body>

<nav class="topbar">
    <a class="nav-logo" href="/"><div class="nav-dot"></div>Docum24</a>
</nav>

<div class="page-wrap">
    <div class="left-panel">
        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: rgba(255,255,255,.5); margin-bottom: 1rem;">Sicurezza</div>
        <h2>Nessun problema, capita a <em>tutti.</em></h2>
        <p style="color: #9a9990; font-size: 14px; line-height: 1.6; max-width: 380px;">
            Inserisci la tua email e ti invieremo un link per impostare una nuova password in tutta sicurezza.
        </p>
    </div>

    <div class="right-panel">
        <div class="form-header">
            <h1>Password dimenticata?</h1>
            <p>Ti invieremo le istruzioni via email.</p>
        </div>

        @if (session('status'))
            <div class="status-msg">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="server-errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="field">
                <label for="email">Email dell'account</label>
                <input type="email" id="email" name="email" :value="old('email')" required autofocus placeholder="tua@email.it">
            </div>

            <button type="submit" class="btn-primary">
                Invia link di ripristino
            </button>
        </form>

        <div class="back-link">
            <a href="{{ route('login') }}">← Torna al login</a>
        </div>
    </div>
</div>

</body>
</html>
