<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Docum24 — Accedi</title>
    <link rel="shortcut icon" type="image/png" href="{{ url('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|instrument-serif:400i" rel="stylesheet" />
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1b1b18">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Docum24">
    <link rel="apple-touch-icon" href="/icons/icon-512x512.png">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --cream: #FDFDFC; --ink: #1b1b18; --mid: #706f6c;
            --dim: #a8a7a3; --red: #F53003; --line: #e3e3e0;
            --line2: #19140035; --white: #ffffff;
        }
        html { font-family: 'Instrument Sans', sans-serif; background: var(--cream); color: var(--ink); min-height: 100vh; }
        body { min-height: 100vh; display: flex; flex-direction: column; }

        /* NAV */
        nav.topbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem; height: 56px;
            background: rgba(253,253,252,.9); backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--line);
        }
        .nav-logo { display: flex; align-items: center; gap: .45rem; font-weight: 600; font-size: 15px; text-decoration: none; color: var(--ink); }
        .nav-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--red); }

        /* LAYOUT */
        .page-wrap { flex: 1; display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; padding-top: 56px; }
        @media(max-width: 860px){ .page-wrap { grid-template-columns: 1fr; } }

        /* LEFT PANEL (Oscuro como Register) */
        .left-panel {
            background: var(--ink); color: #fff; padding: 4rem 3.5rem;
            display: flex; flex-direction: column; justify-content: center;
            position: sticky; top: 56px; height: calc(100vh - 56px);
        }
        @media(max-width: 860px){ .left-panel { display: none; } }

        .left-badge {
            display: inline-flex; align-items: center; gap: .4rem;
            border: 1px solid rgba(255,255,255,.15); border-radius: 99px;
            padding: .25rem .75rem; font-size: 11px; letter-spacing: .06em;
            text-transform: uppercase; color: rgba(255,255,255,.55); margin-bottom: 2rem;
        }

        .left-panel h2 { font-size: 2.2rem; font-weight: 600; letter-spacing: -.03em; line-height: 1.1; margin-bottom: 1.2rem; }
        .left-panel h2 em { font-family: 'Instrument Serif', serif; font-style: italic; font-weight: 400; color: #7a7a72; }

        /* RIGHT PANEL (Formulario) */
        .right-panel {
            background: var(--white); display: flex; flex-direction: column;
            justify-content: center; padding: 3rem 4rem; min-height: calc(100vh - 56px);
        }
        @media(max-width: 500px){ .right-panel { padding: 2rem 1.5rem; } }

        .form-header { margin-bottom: 2.5rem; }
        .form-header h1 { font-size: 1.6rem; font-weight: 600; letter-spacing: -.025em; margin-bottom: .4rem; }
        .form-header p { font-size: 14px; color: var(--mid); }

        /* INPUTS */
        .field { margin-bottom: 1.2rem; }
        .field label { display: block; font-size: 12px; font-weight: 500; color: var(--mid); margin-bottom: .5rem; }
        .field input {
            width: 100%; padding: .75rem .9rem; font-family: 'Instrument Sans', sans-serif; font-size: 14px;
            color: var(--ink); background: var(--cream); border: 1px solid var(--line); border-radius: 8px; outline: none; transition: 0.2s;
        }
        .field input:focus { border-color: var(--ink); box-shadow: 0 0 0 3px rgba(27,27,24,.06); }

        /* BUTTONS */
        .btn-primary {
            width: 100%; padding: .8rem; background: var(--ink); color: #fff; border: none; border-radius: 8px;
            font-size: 14px; font-weight: 600; cursor: pointer; transition: 0.2s;
        }
        .btn-primary:hover { background: #2e2e2a; transform: translateY(-1px); }

        .server-errors { background: #fff2f2; border: 1px solid #ffd0c8; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; font-size: 13px; color: var(--red); }
        .extra-links { margin-top: 1.5rem; display: flex; justify-content: space-between; font-size: 13px; }
        .extra-links a { color: var(--mid); text-decoration: none; transition: 0.2s; }
        .extra-links a:hover { color: var(--ink); text-decoration: underline; }
    </style>
</head>
<body>

<nav class="topbar">
    <a class="nav-logo" href="/"><div class="nav-dot"></div>Docum24</a>
    <div style="font-size:13px;">
        Non hai un account? <a href="{{ route('register') }}" style="color:var(--ink); font-weight:600;">Registrati gratis</a>
    </div>
</nav>

<div class="page-wrap">
    <div class="left-panel">
        <div class="left-badge">Bentornato</div>
        <h2>Gestisci i tuoi documenti con <em>intelligenza.</em></h2>
        <p style="color: #9a9990; font-size: 14px; line-height: 1.6; max-width: 380px;">
            Accedi per continuare a organizzare i tuoi progetti e consultare i tuoi assistenti AI specializzati.
        </p>
    </div>

    <div class="right-panel">
        <div class="form-header">
            <h1>Accedi a Docum24</h1>
            <p>Inserisci le tue credenziali per entrare.</p>
        </div>

        @if ($errors->any())
            <div class="server-errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="field">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="tua@email.it">
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <div style="display: flex; align-items: center; gap: .5rem; margin-bottom: 1.5rem;">
                <input type="checkbox" name="remember" id="remember_me" style="accent-color: var(--ink);">
                <label for="remember_me" style="font-size: 13px; color: var(--mid);">Ricordami</label>
            </div>

            <button type="submit" class="btn-primary">Entra</button>

            <div class="extra-links">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Password dimenticata?</a>
                @endif
                <a href="{{ route('register') }}">Crea account gratis</a>
            </div>
        </form>
    </div>
</div>
<script>
let _pwaPrompt = null;

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    _pwaPrompt = e;

    // Mostrar banner después de 2 segundos
    setTimeout(() => {
        const banner = document.createElement('div');
        banner.id = 'pwa-banner';
        banner.style.cssText = `
            position:fixed;bottom:24px;left:50%;transform:translateX(-50%);
            background:#1b1b18;color:#fff;border-radius:14px;
            padding:14px 20px;display:flex;align-items:center;gap:14px;
            box-shadow:0 8px 32px rgba(0,0,0,0.3);z-index:9999;
            font-family:'Instrument Sans',sans-serif;
            max-width:420px;width:calc(100% - 48px);
            animation:slideUp 0.4s ease;`;
        banner.innerHTML = `
            <img src="/icons/icon-512x512.png" style="width:40px;height:40px;border-radius:10px;flex-shrink:0;">
            <div style="flex:1;">
                <p style="margin:0;font-size:14px;font-weight:600;">Installa Docum24</p>
                <p style="margin:2px 0 0;font-size:12px;color:#9a9990;">Accesso rapido dal desktop</p>
            </div>
            <button onclick="installPWA()"
                style="background:#F53003;color:#fff;border:none;border-radius:8px;
                       padding:8px 14px;font-size:13px;font-weight:600;cursor:pointer;
                       white-space:nowrap;flex-shrink:0;">
                Installa
            </button>
            <button onclick="document.getElementById('pwa-banner').remove()"
                style="background:transparent;border:none;cursor:pointer;
                       color:#9a9990;padding:4px;flex-shrink:0;">
                ✕
            </button>`;
        document.body.appendChild(banner);
    }, 2000);
});

async function installPWA() {
    if (!_pwaPrompt) return;
    _pwaPrompt.prompt();
    const result = await _pwaPrompt.userChoice;
    document.getElementById('pwa-banner')?.remove();
    _pwaPrompt = null;
}
</script>

<style>
@keyframes slideUp {
    from { transform:translateX(-50%) translateY(20px); opacity:0; }
    to   { transform:translateX(-50%) translateY(0); opacity:1; }
}
</style>
</body>
</html>
