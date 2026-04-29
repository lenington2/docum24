<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Docum24 — Gestione Documentale Intelligente per la tua Azienda</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600|instrument-serif:400i" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --cream: #FDFDFC;
            --ink: #1b1b18;
            --mid: #706f6c;
            --dim: #a8a7a3;
            --red: #F53003;
            --line: #e3e3e0;
            --line2: #19140035;
            --white: #ffffff;
        }

        html {
            font-family: 'Instrument Sans', sans-serif;
            background: var(--cream);
            color: var(--ink);
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* NAV */
        nav.topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2.5rem;
            height: 56px;
            background: rgba(253, 253, 252, .9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--line);
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: .45rem;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: -.015em;
            text-decoration: none;
            color: var(--ink);
        }

        .nav-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--red);
            flex-shrink: 0;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: .2rem;
        }

        .nav-links a {
            display: inline-block;
            padding: .35rem .9rem;
            font-size: 13px;
            color: var(--ink);
            text-decoration: none;
            border: 1px solid transparent;
            border-radius: 4px;
            transition: border-color .15s;
        }

        .nav-links a:hover {
            border-color: var(--line2);
        }

        .nav-links .pill {
            background: var(--ink);
            color: #fff;
            border-color: var(--ink);
        }

        .nav-links .pill:hover {
            background: #2e2e2a;
            border-color: #2e2e2a;
        }

        /* HERO */
        .hero-wrap {
            background: var(--cream);
            border-bottom: 1px solid var(--line);
            padding-bottom: 3rem;
        }

        .hero {
            padding: 7.5rem 2.5rem 2rem;
            max-width: 1120px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5rem;
            align-items: center;
        }

        @media(max-width:800px) {
            .hero {
                grid-template-columns: 1fr;
                padding: 6rem 1.5rem 2rem;
                gap: 3rem;
            }
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            border: 1px solid var(--line2);
            border-radius: 99px;
            padding: .25rem .8rem;
            font-size: 11px;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--mid);
            margin-bottom: 1.2rem;
        }

        .badge-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--red);
            animation: pulsedot 2s infinite;
        }

        @keyframes pulsedot {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .35
            }
        }

        .hero h1 {
            font-size: clamp(2.6rem, 4.5vw, 3.7rem);
            font-weight: 600;
            line-height: 1.07;
            letter-spacing: -.03em;
            margin-bottom: 1.2rem;
        }

        .hero h1 em {
            font-family: 'Instrument Serif', serif;
            font-style: italic;
            font-weight: 400;
            color: var(--red);
        }

        .hero-sub {
            font-size: 15px;
            line-height: 1.75;
            color: var(--mid);
            max-width: 420px;
            margin-bottom: 1.1rem;
        }

        .ai-callout {
            display: flex;
            align-items: flex-start;
            gap: .7rem;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: .9rem 1rem;
            margin-bottom: 2rem;
            max-width: 420px;
        }

        .ai-callout-ico {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .ai-callout-ico svg {
            width: 14px;
            height: 14px;
            color: #fff;
        }

        .ai-callout-text {
            font-size: 12px;
            line-height: 1.6;
            color: var(--mid);
        }

        .ai-callout-text strong {
            color: var(--ink);
            font-weight: 600;
        }

        .hero-actions {
            display: flex;
            gap: .7rem;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: .5rem 1.3rem;
            font-size: 13px;
            font-weight: 500;
            border-radius: 4px;
            text-decoration: none;
            border: 1px solid;
            transition: all .15s;
        }

        .btn-dark {
            background: var(--ink);
            color: #fff;
            border-color: var(--ink);
        }

        .btn-dark:hover {
            background: #2e2e2a;
            border-color: #2e2e2a;
        }

        .btn-ghost {
            background: transparent;
            color: var(--ink);
            border-color: var(--line2);
        }

        .btn-ghost:hover {
            border-color: #19140060;
        }

        /* HERO VISUAL — NO overflow:hidden, card flottanti visibili */
        .hero-visual-wrap {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
        }

        @media(max-width:800px) {
            .hero-visual-wrap {
                display: none;
            }
        }

        .doc-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, .08), 0 0 0 1px rgba(26, 26, 0, .07);
            padding: 1.2rem 1.2rem .9rem;
            font-size: 11px;
            width: 100%;
            max-width: 300px;
            position: relative;
            z-index: 2;
        }

        .doc-card-header {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: .9rem;
        }

        .doc-card-title {
            font-weight: 600;
            font-size: 12px;
            flex: 1;
        }

        .tag {
            padding: .12rem .45rem;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .tag-red {
            background: #fff2f2;
            color: var(--red);
        }

        .tag-grn {
            background: #f0fdf4;
            color: #15803d;
        }

        .tag-blu {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .tag-act {
            background: #1b1b18;
            color: #fff;
        }

        .doc-row {
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .4rem 0;
            border-bottom: 1px solid var(--line);
        }

        .doc-row:last-of-type {
            border-bottom: none;
        }

        .doc-ico {
            width: 22px;
            height: 22px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .doc-ico svg {
            width: 11px;
            height: 11px;
        }

        .doc-name {
            flex: 1;
            font-weight: 500;
            color: var(--ink);
            font-size: 11px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 120px;
        }

        .doc-date {
            color: var(--dim);
            font-size: 10px;
            white-space: nowrap;
        }

        /* Floating cards — OUTSIDE overflow:hidden wrapper */
        .fc {
            position: absolute;
            background: #fff;
            border-radius: 8px;
            padding: .65rem .9rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .1), 0 0 0 1px rgba(26, 26, 0, .06);
            font-size: 11px;
            z-index: 3;
        }

        .fc-stat {
            font-size: 20px;
            font-weight: 600;
            color: var(--ink);
            line-height: 1;
        }

        .fc-label {
            font-size: 10px;
            color: var(--mid);
            margin-top: 2px;
        }

        .fc-top {
            top: 4px;
            right: -8px;
        }

        .fc-bot {
            bottom: 24px;
            left: -8px;
        }

        .ai-chip {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            background: var(--ink);
            color: #fff;
            border-radius: 5px;
            padding: .3rem .6rem;
            font-size: 10px;
            font-weight: 500;
            margin-top: .85rem;
        }

        .ai-chip svg {
            width: 10px;
            height: 10px;
        }

        /* DIVIDER */
        .divider {
            border: none;
            border-top: 1px solid var(--line);
        }

        /* SECTION */
        .section {
            padding: 5rem 2.5rem;
            max-width: 1120px;
            margin: 0 auto;
        }

        .sec-label {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            font-size: 11px;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--mid);
            margin-bottom: .9rem;
        }

        .sec-label::before {
            content: '';
            display: block;
            width: 16px;
            height: 1px;
            background: var(--red);
        }

        .sec-title {
            font-size: clamp(1.7rem, 3vw, 2.3rem);
            font-weight: 600;
            letter-spacing: -.025em;
            margin-bottom: .7rem;
        }

        .sec-sub {
            font-size: 14px;
            color: var(--mid);
            line-height: 1.7;
            max-width: 500px;
        }

        /* AI GRID */
        .ai-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1px;
            background: var(--line);
            border: 1px solid var(--line);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 3rem;
        }

        @media(max-width:700px) {
            .ai-grid {
                grid-template-columns: 1fr;
            }
        }

        .ai-item {
            background: var(--cream);
            padding: 2rem 1.75rem;
            transition: background .15s;
        }

        .ai-item:hover {
            background: #fff;
        }

        .ai-item-head {
            display: flex;
            align-items: center;
            gap: .7rem;
            margin-bottom: .75rem;
        }

        .ai-ico {
            width: 34px;
            height: 34px;
            border-radius: 7px;
            border: 1px solid var(--line);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .ai-ico svg {
            width: 16px;
            height: 16px;
            color: var(--red);
        }

        .ai-item-title {
            font-size: 14px;
            font-weight: 600;
        }

        .ai-item-desc {
            font-size: 13px;
            color: var(--mid);
            line-height: 1.65;
        }

        /* AGENT STRIP */
        .agent-strip {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 0;
            margin-top: 1.5rem;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            overflow: hidden;
            position: relative;
        }

        @media(max-width:700px) {
            .agent-strip {
                grid-template-columns: 1fr 1fr;
            }
        }

        .agent-strip::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #fff8f7 0%, transparent 55%);
            pointer-events: none;
            z-index: 0;
        }

        .agent {
            padding: 1.25rem 1.2rem;
            border-right: 1px solid var(--line);
            position: relative;
            z-index: 1;
        }

        .agent:last-child {
            border-right: none;
        }

        @media(max-width:700px) {
            .agent:nth-child(2n) {
                border-right: none;
            }

            .agent:nth-child(n+3) {
                border-top: 1px solid var(--line);
            }
        }

        .agent-tag {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: .07em;
            text-transform: uppercase;
            color: var(--red);
            margin-bottom: .35rem;
        }

        .agent-name {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: .25rem;
        }

        .agent-desc {
            font-size: 11px;
            color: var(--mid);
            line-height: 1.55;
        }

        /* STEPS */
        .steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            border: 1px solid var(--line);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 3rem;
        }

        @media(max-width:700px) {
            .steps {
                grid-template-columns: 1fr 1fr;
            }
        }

        .step {
            padding: 1.6rem 1.4rem;
            border-right: 1px solid var(--line);
        }

        .step:last-child {
            border-right: none;
        }

        @media(max-width:700px) {
            .step:nth-child(2n) {
                border-right: none;
            }

            .step:nth-child(n+3) {
                border-top: 1px solid var(--line);
            }
        }

        .step-num {
            font-size: 11px;
            font-weight: 600;
            color: var(--red);
            letter-spacing: .06em;
            margin-bottom: .65rem;
        }

        .step-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: .35rem;
        }

        .step-desc {
            font-size: 12px;
            color: var(--mid);
            line-height: 1.6;
        }

        /* PRICING */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 3rem;
            align-items: start;
        }

        @media(max-width:860px) {
            .pricing-grid {
                grid-template-columns: 1fr;
                max-width: 360px;
                margin-inline: auto;
            }
        }

        .plan {
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
            transition: box-shadow .2s, transform .2s;
        }

        .plan:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, .08);
            transform: translateY(-2px);
        }

        .plan.featured {
            border-color: var(--ink);
            background: var(--ink);
            color: #fff;
        }

        .plan-head {
            padding: 1.75rem 1.75rem 1.2rem;
        }

        .plan-name {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .07em;
            text-transform: uppercase;
            margin-bottom: .55rem;
            color: var(--mid);
        }

        .plan.featured .plan-name {
            color: #7a7a72;
        }

        .plan-price {
            font-size: 2.3rem;
            font-weight: 600;
            letter-spacing: -.04em;
            line-height: 1;
        }

        .plan-price sub {
            font-size: 13px;
            font-weight: 400;
            color: var(--mid);
            vertical-align: baseline;
        }

        .plan.featured .plan-price sub {
            color: #7a7a72;
        }

        .plan-tagline {
            font-size: 12px;
            color: var(--mid);
            margin-top: .45rem;
            line-height: 1.5;
        }

        .plan.featured .plan-tagline {
            color: #9a9990;
        }

        .plan-body {
            padding: 0 1.75rem 1.75rem;
        }

        .plan-hr {
            border: none;
            border-top: 1px solid var(--line);
            margin: 0 -1.75rem 1.2rem;
        }

        .plan.featured .plan-hr {
            border-color: #2e2e2a;
        }

        .feat-row {
            display: flex;
            align-items: flex-start;
            gap: .55rem;
            font-size: 13px;
            color: var(--mid);
            margin-bottom: .65rem;
            line-height: 1.4;
        }

        .plan.featured .feat-row {
            color: #9a9990;
        }

        .feat-row strong {
            color: var(--ink);
            font-weight: 600;
        }

        .plan.featured .feat-row strong {
            color: #fff;
        }

        .check {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--line);
        }

        .plan.featured .check {
            background: #2e2e2a;
        }

        .check svg {
            width: 8px;
            height: 8px;
            color: var(--mid);
        }

        .plan.featured .check svg {
            color: #9a9990;
        }

        .plan-cta {
            display: block;
            text-align: center;
            padding: .6rem 1rem;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid;
            transition: all .15s;
            margin-top: 1.2rem;
        }

        .cta-border {
            border-color: var(--line2);
            color: var(--ink);
        }

        .cta-border:hover {
            border-color: var(--ink);
        }

        .cta-white {
            background: #fff;
            color: var(--ink);
            border-color: #fff;
        }

        .cta-white:hover {
            background: #f0f0ee;
        }

        /* CTA BANNER */
        .cta-wrap {
            padding: 0 2.5rem 5rem;
            max-width: 1160px;
            margin: 0 auto;
        }

        .cta-banner {
            background: var(--ink);
            border-radius: 14px;
            padding: 3.5rem 3rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .cta-banner h2 {
            font-size: clamp(1.6rem, 3vw, 2.2rem);
            font-weight: 600;
            letter-spacing: -.025em;
            color: #fff;
        }

        .cta-banner h2 em {
            font-family: 'Instrument Serif', serif;
            font-style: italic;
            font-weight: 400;
            color: #7a7a72;
        }

        .cta-banner p {
            font-size: 13px;
            color: #7a7a72;
            margin-top: .4rem;
        }

        .btn-white {
            background: #fff;
            color: var(--ink);
            border-color: #fff;
            padding: .65rem 1.75rem;
            font-size: 14px;
            font-weight: 500;
            border-radius: 4px;
            text-decoration: none;
            white-space: nowrap;
            border: 1px solid #fff;
            transition: background .15s;
            display: inline-block;
        }

        .btn-white:hover {
            background: #f0f0ee;
        }

        /* FOOTER */
        footer {
            border-top: 1px solid var(--line);
            padding: 1.75rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 12px;
            color: var(--dim);
            max-width: 1120px;
            margin: 0 auto;
        }

        footer a {
            color: var(--dim);
            text-decoration: none;
        }

        footer a:hover {
            color: var(--ink);
        }

        /* ANIM */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(14px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .fu {
            animation: fadeUp .55s ease both;
        }

        .fu2 {
            animation: fadeUp .55s .12s ease both;
        }

        .fu3 {
            animation: fadeUp .55s .22s ease both;
        }

        .fu4 {
            animation: fadeUp .55s .34s ease both;
        }

        .fu5 {
            animation: fadeUp .55s .44s ease both;
        }
    </style>
</head>

<body>

    <!-- NAV -->
    <nav class="topbar">
        <a class="nav-logo" href="/">
            <div class="nav-dot"></div>Docum24
        </a>
        <div class="nav-links">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Accedi</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="pill">Inizia gratis</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>


    <!-- HERO -->
    <div class="hero-wrap">
        <div class="hero">

            <div>
                <div class="badge fu"><span class="badge-dot"></span>Gestione documentale con AI</div>
                <h1 class="fu2">I tuoi documenti,<br><em>gestiti dall'AI.</em></h1>
                <p class="hero-sub fu3">Docum24 organizza tutta la documentazione della tua azienda, impara il tuo
                    settore e diventa un esperto del tuo business. Accesso istantaneo, report automatici, collaborazione
                    di team.</p>

                <div class="ai-callout fu4">
                    <div class="ai-callout-ico">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="8" cy="8" r="3" />
                            <path d="M8 1v2M8 13v2M1 8h2M13 8h2M3.05 3.05l1.42 1.42M11.53 11.53l1.42 1.42" />
                        </svg>
                    </div>
                    <div class="ai-callout-text">
                        <strong>Agenti AI specializzati per settore</strong> — Docum24 analizza il tipo di azienda e si
                        configura automaticamente. Categorie, tipologie e flussi su misura. Si adatta a studi legali,
                        agenzie, manifattura e molto altro.
                    </div>
                </div>

                <div class="hero-actions fu5">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-dark">Inizia gratis</a>
                    @endif
                    <a href="#piani" class="btn btn-ghost">Vedi i piani</a>
                </div>
            </div>

            <!-- VISUAL — wrapper senza overflow:hidden -->
            <div class="hero-visual-wrap fu3">
                <!-- Floating card top-right — VISIBILE perché fuori dall'overflow -->
                <div class="fc fc-top">
                    <div class="fc-stat">142</div>
                    <div class="fc-label">Documenti questo mese</div>
                </div>
                <!-- Floating card bottom-left -->
                <div class="fc fc-bot">
                    <div class="fc-stat">−78%</div>
                    <div class="fc-label">Tempo di ricerca</div>
                </div>

                <div class="doc-card">
                    <div class="doc-card-header">
                        <span class="doc-card-title">Progetto: Accordi 2025</span>
                        <span class="tag tag-act">Attivo</span>
                    </div>
                    <div class="doc-row">
                        <div class="doc-ico" style="background:#fff2f2;"><svg viewBox="0 0 16 16" fill="none"
                                stroke="#f53003" stroke-width="1.5">
                                <path d="M4 2h6l3 3v9H4V2z" />
                                <path d="M10 2v3h3" />
                            </svg></div>
                        <span class="doc-name">Contratto_NDA_cliente.pdf</span>
                        <span class="tag tag-red">Contratto</span>
                        <span class="doc-date">oggi</span>
                    </div>
                    <div class="doc-row">
                        <div class="doc-ico" style="background:#f0fdf4;"><svg viewBox="0 0 16 16" fill="none"
                                stroke="#15803d" stroke-width="1.5">
                                <path d="M4 2h6l3 3v9H4V2z" />
                                <path d="M10 2v3h3" />
                            </svg></div>
                        <span class="doc-name">Fattura_marzo_2025.pdf</span>
                        <span class="tag tag-grn">Fattura</span>
                        <span class="doc-date">ieri</span>
                    </div>
                    <div class="doc-row">
                        <div class="doc-ico" style="background:#eff6ff;"><svg viewBox="0 0 16 16" fill="none"
                                stroke="#1d4ed8" stroke-width="1.5">
                                <path d="M4 2h6l3 3v9H4V2z" />
                                <path d="M10 2v3h3" />
                            </svg></div>
                        <span class="doc-name">Proposta_commerciale.docx</span>
                        <span class="tag tag-blu">Proposta</span>
                        <span class="doc-date">3 mar</span>
                    </div>
                    <div class="doc-row">
                        <div class="doc-ico" style="background:#fff2f2;"><svg viewBox="0 0 16 16" fill="none"
                                stroke="#f53003" stroke-width="1.5">
                                <path d="M4 2h6l3 3v9H4V2z" />
                                <path d="M10 2v3h3" />
                            </svg></div>
                        <span class="doc-name">Accordo_servizio_v2.pdf</span>
                        <span class="tag tag-red">Contratto</span>
                        <span class="doc-date">28 feb</span>
                    </div>
                    <div style="padding-top:.85rem; border-top:1px solid var(--line); margin-top:.1rem;">
                        <div class="ai-chip">
                            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="8" cy="8" r="3" />
                                <path d="M8 2v1M8 13v1M2 8h1M13 8h1" />
                            </svg>
                            AI: 2 contratti in scadenza questa settimana
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Floating card — usuarios registrados -->
    <div class="fc" style="top: 50%; right: -12px; transform: translateY(-50%);">
        <div class="fc-stat" id="user-counter">—</div>
        <div class="fc-label">Utenti registrati</div>
    </div>


    <!-- AI CORE -->
    <section class="section">
        <div class="sec-label">Intelligenza Artificiale</div>
        <h2 class="sec-title">Un sistema che impara il tuo business</h2>
        <p class="sec-sub">Docum24 non è un semplice archivio. Si adatta al tuo settore, capisce i tuoi documenti e ti
            assiste come un esperto interno sempre disponibile.</p>

        <div class="ai-grid">
            <div class="ai-item">
                <div class="ai-item-head">
                    <div class="ai-ico"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <circle cx="8" cy="8" r="3" />
                            <path d="M8 1v2M8 13v2M1 8h2M13 8h2" />
                        </svg></div>
                    <div class="ai-item-title">Configurazione automatica per settore</div>
                </div>
                <div class="ai-item-desc">Al primo accesso Docum24 analizza il tipo di azienda e configura categorie,
                    tipologie documentali e flussi di lavoro su misura — senza nessuna configurazione manuale.</div>
            </div>
            <div class="ai-item">
                <div class="ai-item-head">
                    <div class="ai-ico"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <rect x="2" y="3" width="12" height="10" rx="1.5" />
                            <path d="M5 7h6M5 10h4" />
                        </svg></div>
                    <div class="ai-item-title">Assistente documentale AI</div>
                </div>
                <div class="ai-item-desc">Chiedi all'AI di trovare un documento, riassumerne il contenuto, confrontare
                    versioni o verificare scadenze. Come avere un assistente esperto sempre al tuo fianco.</div>
            </div>
            <div class="ai-item">
                <div class="ai-item-head">
                    <div class="ai-ico"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path d="M2 13l4-4 3 3 5-6" />
                            <circle cx="13" cy="4" r="2" />
                        </svg></div>
                    <div class="ai-item-title">Agenti specializzati per report</div>
                </div>
                <div class="ai-item-desc">Agenti AI dedicati generano report personalizzati per il tuo settore: analisi
                    contratti, sintesi finanziarie, stato avanzamento commesse — nel linguaggio della tua azienda.</div>
            </div>
            <div class="ai-item">
                <div class="ai-item-head">
                    <div class="ai-ico"><svg viewBox="0 0 16 16" fill="none" stroke="currentColor"
                            stroke-width="1.5">
                            <path d="M8 2l1.5 3 3.5.5-2.5 2.5.5 3.5L8 10l-3 1.5.5-3.5L3 5.5 6.5 5z" />
                        </svg></div>
                    <div class="ai-item-title">Classificazione intelligente</div>
                </div>
                <div class="ai-item-desc">Carica qualsiasi file: l'AI legge il contenuto e suggerisce automaticamente
                    progetto, categoria e tipologia corretti. Zero errori di archiviazione, zero tempo perso.</div>
            </div>
        </div>

        <!-- Agenti per settore -->
        <div class="agent-strip">
            <div class="agent">
                <div class="agent-tag">Agente</div>
                <div class="agent-name">⚖️ Studio Legale</div>
                <div class="agent-desc">Contratti, scadenze, fascicoli clienti e atti giudiziari.</div>
            </div>
            <div class="agent">
                <div class="agent-tag">Agente</div>
                <div class="agent-name">🏗️ Edilizia & Appalti</div>
                <div class="agent-desc">Capitolati, permessi, SAL, collaudi e documentazione cantiere.</div>
            </div>
            <div class="agent">
                <div class="agent-tag">Agente</div>
                <div class="agent-name">💼 Agenzia & Consulenza</div>
                <div class="agent-desc">Proposte, report clienti, brief e rendicontazione ore.</div>
            </div>
            <div class="agent">
                <div class="agent-tag">Agente</div>
                <div class="agent-name">🏭 PMI & Manifattura</div>
                <div class="agent-desc">Ordini, DDT, certificazioni qualità e compliance normativa.</div>
            </div>
        </div>
    </section>

    <hr class="divider">


    <!-- STRUTTURA -->
    <section class="section">
        <div class="sec-label">Struttura</div>
        <h2 class="sec-title">Tutto al suo posto</h2>
        <p class="sec-sub">Tre livelli di organizzazione configurabili per qualsiasi tipo di azienda. L'AI suggerisce
            la struttura migliore per il tuo settore.</p>

        <div class="steps">
            <div class="step">
                <div class="step-num">01</div>
                <div class="step-title">Progetti</div>
                <div class="step-desc">Raggruppa il lavoro per cliente, commessa o area aziendale in uno spazio
                    dedicato.</div>
            </div>
            <div class="step">
                <div class="step-num">02</div>
                <div class="step-title">Categorie</div>
                <div class="step-desc">Dentro ogni progetto, crea le sezioni che servono: Legale, Finanziario, Tecnico,
                    Marketing…</div>
            </div>
            <div class="step">
                <div class="step-num">03</div>
                <div class="step-title">Tipologie</div>
                <div class="step-desc">Definisci i tipi documento per categoria: Contratti, Fatture, Relazioni —
                    adattati al tuo settore dall'AI.</div>
            </div>
            <div class="step">
                <div class="step-num">04</div>
                <div class="step-title">Documenti</div>
                <div class="step-desc">Carica, classifica e ritrova qualsiasi file in pochi secondi. L'AI guida ogni
                    passo.</div>
            </div>
        </div>
    </section>

    <hr class="divider">


    <!-- PIANI -->
    <section class="section" id="piani">
        <div class="sec-label">Prezzi</div>
        <h2 class="sec-title">Il piano che cresce con te</h2>
        <p class="sec-sub">Inizia gratis e scala quando la tua azienda ne ha bisogno. Nessuna sorpresa.</p>

        <div class="pricing-grid">

            <div class="plan">
                <div class="plan-head">
                    <div class="plan-name">Trial</div>
                    <div class="plan-price">Gratis <sub>/ 30 giorni</sub></div>
                    <div class="plan-tagline">Prova Docum24 senza impegno. Nessuna carta richiesta.</div>
                </div>
                <div class="plan-body">
                    <hr class="plan-hr">
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span><strong>1 progetto</strong> attivo</span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span><strong>5 categorie</strong> per progetto</span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span><strong>5 tipologie</strong> per categoria</span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span>Archiviazione <strong>100 MB</strong></span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span><strong>500k token</strong> AI inclusi</span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span>Utente <strong>singolo</strong></span>
                    </div>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="plan-cta cta-border">Inizia gratis</a>
                    @endif
                </div>
            </div>

            <div class="plan featured">
                <div class="plan-head">
                    <div class="plan-name">Basic</div>
                    <div class="plan-price">€9 <sub>/ mese</sub></div>
                    <div class="plan-tagline">Per freelance e studi professionali che crescono.</div>
                </div>
                <div class="plan-body">
                    <hr class="plan-hr">
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span><strong>5 progetti</strong> attivi</span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span><strong>20 categorie</strong> per progetto</span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span><strong>20 tipologie</strong> per categoria</span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span>Archiviazione <strong>1 GB</strong></span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span><strong>2M token</strong> AI inclusi</span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span>Utente <strong>singolo</strong></span>
                    </div>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}?plan=Basic" class="plan-cta cta-white">Inizia con Basic</a>
                    @endif
                </div>
            </div>

            <div class="plan">
                <div class="plan-head">
                    <div class="plan-name">Pro</div>
                    <div class="plan-price">€29 <sub>/ mese</sub></div>
                    <div class="plan-tagline">Per PMI e team che collaborano senza limiti.</div>
                </div>
                <div class="plan-body">
                    <hr class="plan-hr">
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span><strong>Progetti illimitati</strong></span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span><strong>Categorie illimitate</strong> per progetto</span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span><strong>Tipologie illimitate</strong></span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span>Archiviazione <strong>10 GB</strong></span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span><strong>8M token</strong> AI inclusi</span>
                    </div>
                    <div class="feat-row">
                        <div class="check"><svg viewBox="0 0 10 10" fill="none" stroke="currentColor"
                                stroke-width="1.5">
                                <path d="M2 5l2 2 4-4" />
                            </svg></div><span>Fino a <strong>5 utenti</strong> nel team</span>
                    </div>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}?plan=Pro" class="plan-cta cta-border">Inizia con Pro</a>
                    @endif
                </div>
            </div>

        </div>
    </section>


    <!-- CTA BANNER -->
    <div class="cta-wrap">
        <div class="cta-banner">
            <div>
                <h2>Pronto a trasformare<br><em>la tua gestione documentale?</em></h2>
                <p>Crea il tuo account in meno di 2 minuti. Nessuna carta di credito richiesta.</p>
            </div>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn-white">Inizia gratis →</a>
            @endif
        </div>
    </div>


    <!-- FOOTER -->
    <footer>
        <div style="display:flex;align-items:center;gap:.5rem;">
            <div class="nav-dot" style="width:6px;height:6px;"></div>
            <span style="font-weight:600;color:var(--ink);">Docum24</span>
            <span>— Gestione documentale intelligente per le aziende italiane</span>
        </div>
        <div style="display:flex;gap:1.5rem;">
            <a href="#">Privacy</a>
            <a href="#">Termini</a>
            <a href="#">Contatti</a>
        </div>
    </footer>

    <script>
    async function animateCounter(el, target, duration = 1200) {
        const start = performance.now();
        const update = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            el.textContent = Math.floor(eased * target);
            if (progress < 1) requestAnimationFrame(update);
            else el.textContent = target;
        };
        requestAnimationFrame(update);
    }

    fetch('/api/user-count')
        .then(r => r.json())
        .then(data => {
            const el = document.getElementById('user-counter');
            animateCounter(el, data.count);
        })
        .catch(() => {
            document.getElementById('user-counter').textContent = '—';
        });
</script>
</body>

</html>
