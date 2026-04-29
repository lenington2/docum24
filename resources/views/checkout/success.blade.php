<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pagamento completato — Docum24</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Instrument Sans', sans-serif; background: #FDFDFC; color: #1b1b18; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { text-align: center; max-width: 420px; padding: 3rem 2rem; border: 1px solid #e3e3e0; border-radius: 16px; background: #fff; }
        .icon { width: 64px; height: 64px; border-radius: 50%; background: #f0fdf4; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; }
        .icon svg { width: 32px; height: 32px; color: #15803d; }
        h1 { font-size: 1.6rem; font-weight: 600; margin-bottom: .75rem; }
        p  { font-size: 14px; color: #706f6c; line-height: 1.7; margin-bottom: 2rem; }
        a  { display: inline-block; padding: .6rem 1.5rem; background: #1b1b18; color: #fff; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500; }
        a:hover { background: #2e2e2a; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
        </div>
        <h1>Pagamento completato!</h1>
        <p>Il tuo piano è stato attivato con successo.<br>Benvenuto nella nuova versione di Docum24.</p>
        <a href="{{ url('/dashboard') }}">Vai alla Dashboard →</a>
    </div>
</body>
</html>
