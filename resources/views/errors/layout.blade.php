<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Errore')</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            font-family: 'Nunito', sans-serif;
            background-color: #f5f5f3;
            color: #1a1a18;
            height: 100vh;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card {
            background: #ffffff;
            border: 0.5px solid rgba(0,0,0,0.12);
            border-radius: 12px;
            padding: 3rem 3.5rem;
            max-width: 520px;
            width: 100%;
            text-align: center;
        }

        .icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #FCEBEB;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .label {
            font-size: 12px;
            font-weight: 600;
            color: #A32D2D;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }

        h1 {
            font-size: 22px;
            font-weight: 600;
            color: #1a1a18;
            margin-bottom: 0.75rem;
        }

        .description {
            font-size: 15px;
            color: #5a5a58;
            line-height: 1.65;
            margin-bottom: 2rem;
        }

        .divider {
            border: none;
            border-top: 0.5px solid rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 8px;
            border: 0.5px solid rgba(0,0,0,0.22);
            background: #ffffff;
            color: #1a1a18;
            font-family: 'Nunito', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s;
        }

        .btn-back:hover {
            background: #f5f5f3;
            text-decoration: none;
            color: #1a1a18;
        }

        .message-block {
            margin-top: 1.5rem;
            font-size: 13px;
            color: #888780;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">

            <div class="icon-wrap">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                          stroke="#A32D2D" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <p class="label">Errore di sistema</p>
            <h1>Qualcosa è andato storto</h1>
            <p class="description">
                Si è verificato un errore imprevisto.<br>
                Riprova tra qualche istante oppure torna alla pagina precedente.
            </p>

            <hr class="divider">

            <a href="#" class="btn-back" onclick="history.back(-1); return false;">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 12L6 8l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Torna indietro
            </a>

            @if(isset($message) || View::hasSection('message'))
                <div class="message-block">
                    @yield('message')
                </div>
            @endif

        </div>
    </div>
</body>
</html>