<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Docum24 — Crea il tuo account</title>
    <link rel="shortcut icon" type="image/png" href="{{ url('images/logo.png') }}">
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
            min-height: 100vh;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* NAV */
        nav.topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            height: 56px;
            background: rgba(253, 253, 252, .9);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--line);
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: .45rem;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            color: var(--ink);
        }

        .nav-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--red);
        }

        /* LAYOUT */
        .page-wrap {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
            padding-top: 56px;
        }

        @media(max-width: 860px) {
            .page-wrap {
                grid-template-columns: 1fr;
            }
        }

        /* LEFT PANEL — info */
        .left-panel {
            background: var(--ink);
            color: #fff;
            padding: 4rem 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: sticky;
            top: 56px;
            height: calc(100vh - 56px);
        }

        @media(max-width: 860px) {
            .left-panel {
                display: none;
            }
        }

        .left-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 99px;
            padding: .25rem .75rem;
            font-size: 11px;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .55);
            margin-bottom: 2rem;
        }

        .left-dot {
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
                opacity: .3
            }
        }

        .left-panel h2 {
            font-size: clamp(1.9rem, 2.5vw, 2.6rem);
            font-weight: 600;
            letter-spacing: -.03em;
            line-height: 1.1;
            margin-bottom: 1.2rem;
        }

        .left-panel h2 em {
            font-family: 'Instrument Serif', serif;
            font-style: italic;
            font-weight: 400;
            color: #7a7a72;
        }

        .left-panel p {
            font-size: 14px;
            color: #9a9990;
            line-height: 1.7;
            margin-bottom: 2.5rem;
        }

        .feature-list {
            display: flex;
            flex-direction: column;
            gap: .85rem;
        }

        .feat {
            display: flex;
            align-items: flex-start;
            gap: .75rem;
        }

        .feat-icon {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: rgba(255, 255, 255, .07);
            border: 1px solid rgba(255, 255, 255, .1);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .feat-icon svg {
            width: 13px;
            height: 13px;
            color: rgba(255, 255, 255, .7);
        }

        .feat-text {
            font-size: 13px;
            color: #9a9990;
            line-height: 1.5;
        }

        .feat-text strong {
            color: #fff;
            font-weight: 500;
        }

        .trial-pill {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: rgba(245, 48, 3, .15);
            border: 1px solid rgba(245, 48, 3, .3);
            border-radius: 8px;
            padding: .6rem 1rem;
            margin-top: 2.5rem;
            font-size: 12px;
            color: #ff8a6a;
        }

        .trial-pill svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        /* RIGHT PANEL — form */
        .right-panel {
            background: var(--white);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 3.5rem;
            min-height: calc(100vh - 56px);
        }

        @media(max-width: 500px) {
            .right-panel {
                padding: 2rem 1.5rem;
            }
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: -.025em;
            margin-bottom: .35rem;
        }

        .form-header p {
            font-size: 13px;
            color: var(--mid);
        }

        /* STEPS */
        .steps-indicator {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 2rem;
        }

        .step-pip {
            height: 3px;
            border-radius: 2px;
            transition: all .3s;
            background: var(--line);
        }

        .step-pip.active {
            background: var(--ink);
        }

        .step-pip.done {
            background: var(--red);
        }

        .step-label {
            font-size: 11px;
            color: var(--dim);
            margin-left: auto;
            letter-spacing: .04em;
        }

        /* FORM STEPS */
        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
            animation: fadeUp .3s ease;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(8px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* INPUTS */
        .field {
            margin-bottom: 1.1rem;
        }

        .field label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: var(--mid);
            margin-bottom: .4rem;
            letter-spacing: .02em;
        }

        .field input,
        .field select {
            width: 100%;
            padding: .65rem .9rem;
            font-family: 'Instrument Sans', sans-serif;
            font-size: 14px;
            color: var(--ink);
            background: var(--cream);
            border: 1px solid var(--line);
            border-radius: 6px;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            appearance: none;
        }

        .field input:focus,
        .field select:focus {
            border-color: var(--ink);
            box-shadow: 0 0 0 3px rgba(27, 27, 24, .06);
        }

        .field input.error {
            border-color: var(--red);
        }

        .field-error {
            font-size: 11px;
            color: var(--red);
            margin-top: .3rem;
            display: none;
        }

        .field-error.show {
            display: block;
        }

        .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem;
        }

        @media(max-width: 480px) {
            .field-row {
                grid-template-columns: 1fr;
            }
        }

        /* PASSWORD STRENGTH */
        .pw-strength {
            display: flex;
            gap: 3px;
            margin-top: .4rem;
        }

        .pw-bar {
            height: 3px;
            flex: 1;
            border-radius: 2px;
            background: var(--line);
            transition: background .3s;
        }

        /* BUSINESS TYPE GRID */
        .biz-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            /* 3 columnas en desktop */
            gap: .5rem;
            margin-bottom: .75rem;
        }

        @media(max-width: 480px) {
            .biz-grid {
                grid-template-columns: repeat(2, 1fr);
                /* 2 columnas en móvil para que no se amontone el texto */
            }
        }

        .biz-chip {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .3rem;
            padding: .7rem .5rem;
            border: 1px solid var(--line);
            border-radius: 8px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 500;
            color: var(--mid);
            background: var(--cream);
            transition: all .15s;
            text-align: center;
            line-height: 1.3;
        }

        .biz-chip:hover {
            border-color: var(--line2);
            color: var(--ink);
            background: #fff;
        }

        .biz-chip.selected {
            border-color: var(--ink);
            color: var(--ink);
            background: #fff;
        }

        .biz-chip .emoji {
            font-size: 1.3rem;
        }

        .biz-custom {
            position: relative;
            margin-top: .5rem;
        }

        .biz-custom input {
            border-radius: 8px !important;
            /* Bordes redondeados */
            padding: 0.8rem 1rem !important;
            /* Más aire interno */
            border: 1px solid var(--line) !important;
            background: var(--cream) !important;
            transition: all 0.2s ease;
        }

        .biz-custom input:focus {
            border-color: var(--ink) !important;
            box-shadow: 0 0 0 3px rgba(27, 27, 24, .06) !important;
        }

        .biz-custom-clear {
            position: absolute;
            right: .75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--dim);
            font-size: 16px;
            line-height: 1;
            display: none;
            padding: 0;
            transition: color .15s;
        }

        .biz-custom-clear:hover {
            color: var(--ink);
        }

        /* TERMS */
        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: .6rem;
            margin-bottom: 1.25rem;
        }

        .terms-check {
            width: 16px;
            height: 16px;
            border: 1.5px solid var(--line);
            border-radius: 3px;
            cursor: pointer;
            flex-shrink: 0;
            margin-top: 1px;
            appearance: none;
            background: var(--cream);
            transition: all .15s;
            position: relative;
        }

        .terms-check:checked {
            background: var(--ink);
            border-color: var(--ink);
        }

        .terms-check:checked::after {
            content: '';
            position: absolute;
            left: 4px;
            top: 1px;
            width: 5px;
            height: 9px;
            border: 2px solid #fff;
            border-top: none;
            border-left: none;
            transform: rotate(45deg);
        }

        .terms-label {
            font-size: 12px;
            color: var(--mid);
            line-height: 1.5;
        }

        .terms-label a {
            color: var(--ink);
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        /* BUTTONS */
        .btn-primary {
            width: 100%;
            padding: .7rem 1rem;
            background: var(--ink);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-family: 'Instrument Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s;
            position: relative;
        }

        .btn-primary:hover {
            background: #2e2e2a;
        }

        .btn-primary:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: transparent;
            color: var(--mid);
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: .65rem 1.25rem;
            font-family: 'Instrument Sans', sans-serif;
            font-size: 13px;
            cursor: pointer;
            transition: all .15s;
        }

        .btn-secondary:hover {
            border-color: var(--line2);
            color: var(--ink);
        }

        .btn-row {
            display: flex;
            gap: .6rem;
            margin-top: 1.4rem;
        }

        .btn-row .btn-primary {
            flex: 1;
        }

        /* AI LOADING */
        .ai-loading {
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            padding: 2rem;
            text-align: center;
        }

        .ai-loading.show {
            display: flex;
        }

        .ai-spinner {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--line);
            border-top-color: var(--ink);
            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .ai-loading p {
            font-size: 14px;
            font-weight: 500;
            color: var(--ink);
        }

        .ai-loading small {
            font-size: 12px;
            color: var(--mid);
        }

        /* VALIDATION ERRORS */
        .server-errors {
            background: #fff2f2;
            border: 1px solid #ffd0c8;
            border-radius: 6px;
            padding: .75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 12px;
            color: var(--red);
        }

        .server-errors ul {
            padding-left: 1.2rem;
        }

        .server-errors li {
            margin-bottom: .2rem;
        }

        /* LOGIN LINK */
        .login-link {
            margin-top: 1.5rem;
            text-align: center;
            font-size: 13px;
            color: var(--mid);
        }

        .login-link a {
            color: var(--ink);
            font-weight: 500;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        /* AI PROMPT PREVIEW */
        .prompt-preview {
            background: var(--cream);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: .9rem 1rem;
            margin-bottom: 1rem;
            font-size: 12px;
            color: var(--mid);
            line-height: 1.6;
            display: none;
            position: relative;
        }

        .prompt-preview.show {
            display: block;
        }

        .prompt-preview-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--dim);
            margin-bottom: .4rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        .prompt-preview-label::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--red);
            display: block;
        }
    </style>
</head>

<body>

    <nav class="topbar">
        <a class="nav-logo" href="/">
            <div class="nav-dot"></div>Docum24
        </a>
        <div style="font-size:13px;color:var(--mid);">
            Hai già un account? <a href="{{ route('login') }}"
                style="color:var(--ink);font-weight:500;text-decoration:underline;text-underline-offset:3px;">Accedi</a>
        </div>
    </nav>

    <div class="page-wrap">

        <!-- LEFT -->
        <div class="left-panel">
            <div class="left-badge"><span class="left-dot"></span>
                @if (request('plan') && request('plan') !== 'Trial')
                    Piano {{ request('plan') }}
                @else
                    Prova gratuita 30 giorni
                @endif
            </div>

            <h2>Il tuo business,<br>gestito <em>dall'AI.</em></h2>
            <p>Docum24 impara il tuo settore e diventa un esperto della tua azienda. Documenti sempre in ordine, report
                automatici, zero confusione.</p>

            <div class="feature-list">
                <div class="feat">
                    <div class="feat-icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="7" cy="7" r="3" />
                            <path d="M7 1v2M7 11v2M1 7h2M11 7h2" />
                        </svg>
                    </div>
                    <div class="feat-text"><strong>AI specializzata per settore</strong> — Si configura automaticamente
                        per il tuo tipo di business.</div>
                </div>
                <div class="feat">
                    <div class="feat-icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M3 2h5l3 3v7H3V2z" />
                            <path d="M8 2v3h3" />
                        </svg>
                    </div>
                    <div class="feat-text"><strong>Gestione documentale intelligente</strong> — Carica, classifica e
                        ritrova qualsiasi file in secondi.</div>
                </div>
                <div class="feat">
                    <div class="feat-icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M2 11l3-3 2 2 5-5" />
                        </svg>
                    </div>
                    <div class="feat-text"><strong>Report automatici</strong> — Agenti AI generano analisi e sintesi nel
                        linguaggio del tuo settore.</div>
                </div>
                <div class="feat">
                    <div class="feat-icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="5" cy="7" r="3" />
                            <circle cx="9.5" cy="4" r="2.5" />
                            <path d="M7.5 6.5l2-1" />
                        </svg>
                    </div>
                    <div class="feat-text"><strong>Team fino a 5 utenti</strong> — Collabora con il tuo team nel piano
                        Business.</div>
                </div>
            </div>

            <div class="trial-pill">
                <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M7 1v6l3 2" />
                    <circle cx="7" cy="7" r="6" />
                </svg>
                @if (request('plan') && request('plan') !== 'Trial')
                    <span><strong>Piano {{ request('plan') }}</strong> —
                        {{ request('plan') === 'Basic' ? '€9/mese' : '€29/mese' }}</span>
                @else
                    <span><strong>30 giorni gratis</strong> — Nessuna carta di credito richiesta.</span>
                @endif
            </div>
        </div>

        <!-- RIGHT -->
        <div class="right-panel">

            <div class="form-header">
                <h1>Crea il tuo account</h1>
                <p>Inizia gratis. Configura Docum24 per la tua azienda in 2 minuti.</p>
            </div>

            <!-- Steps indicator -->
            <div class="steps-indicator">
                <div class="step-pip active" id="pip-1" style="width:40px;"></div>
                <div class="step-pip" id="pip-2" style="width:40px;"></div>
                <div class="step-pip" id="pip-3" style="width:40px;"></div>
                <span class="step-label" id="step-label">Passo 1 di 3</span>
            </div>

            @if ($errors->any())
                <div class="server-errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="register-form">
                @csrf
                <input type="hidden" name="selected_plan" id="selected-plan" value="{{ request('plan') }}">
                <!-- HIDDEN per inviare tutti i dati dal JS -->
                <input type="hidden" name="business_type" id="hidden-business-type">
                <input type="hidden" name="ai_prompt" id="hidden-ai-prompt">

                <!-- ══ STEP 1 — Dati personali ══════════════════════════ -->
                <div class="form-step active" id="step-1">

                    <div class="field">
                        <label for="name">Nome e cognome *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            placeholder="Es. Marco Rossi" autocomplete="name" autofocus>
                        <div class="field-error" id="err-name">Inserisci il tuo nome</div>
                    </div>

                    <div class="field">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="marco@studio.it" autocomplete="email">
                        <div class="field-error" id="err-email">Inserisci un'email valida</div>
                    </div>

                    <div class="field">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" placeholder="Minimo 8 caratteri"
                            autocomplete="new-password" oninput="updatePwStrength(this.value)">
                        <div class="pw-strength">
                            <div class="pw-bar" id="pw-bar-1"></div>
                            <div class="pw-bar" id="pw-bar-2"></div>
                            <div class="pw-bar" id="pw-bar-3"></div>
                            <div class="pw-bar" id="pw-bar-4"></div>
                        </div>
                        <div class="field-error" id="err-password">Minimo 8 caratteri</div>
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Conferma password *</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            placeholder="Ripeti la password" autocomplete="new-password">
                        <div class="field-error" id="err-confirm">Le password non coincidono</div>
                    </div>

                    <div class="btn-row">
                        <button type="button" class="btn-primary" onclick="goStep(2)">
                            Continua →
                        </button>
                    </div>
                </div>

                <!-- ══ STEP 2 — Tipo di business ═══════════════════════ -->
                <div class="form-step" id="step-2">

                    <div style="margin-bottom:1rem;">
                        <p style="font-size:13px;color:var(--mid);line-height:1.6;">
                            Seleziona il tipo di attività oppure descrivila liberamente.<br>
                            <strong style="color:var(--ink);">Docum24 si configurerà automaticamente.</strong>
                        </p>
                    </div>

                    <div class="biz-grid">
                        <div class="biz-chip" onclick="selectBiz(this, 'Studio Legale')">
                            <span class="emoji">⚖️</span>Studio Legale
                        </div>
                        <div class="biz-chip" onclick="selectBiz(this, 'Commercialista')">
                            <span class="emoji">📊</span>Commercialista
                        </div>
                        <div class="biz-chip" onclick="selectBiz(this, 'Studio Medico')">
                            <span class="emoji">🏥</span>Studio Medico
                        </div>
                        <div class="biz-chip" onclick="selectBiz(this, 'Edilizia & Appalti')">
                            <span class="emoji">🏗️</span>Edilizia
                        </div>
                        <div class="biz-chip" onclick="selectBiz(this, 'Agenzia & Consulenza')">
                            <span class="emoji">💼</span>Agenzia
                        </div>
                        <div class="biz-chip" onclick="selectBiz(this, 'PMI & Manifattura')">
                            <span class="emoji">🏭</span>PMI
                        </div>
                        <div class="biz-chip" onclick="selectBiz(this, 'Immobiliare')">
                            <span class="emoji">🏢</span>Immobiliare
                        </div>
                        <div class="biz-chip" onclick="selectBiz(this, 'E-commerce & Retail')">
                            <span class="emoji">🛍️</span>E-commerce
                        </div>
                        <div class="biz-chip" onclick="selectBiz(this, 'Uso Personale')">
                            <span class="emoji">🏠</span>Personale
                        </div>
                        <div class="biz-chip" onclick="selectBiz(this, 'Studio Dentistico')">
                            <span class="emoji">🦷</span>Studio Dentistico
                        </div>
                        <div class="biz-chip" onclick="selectBiz(this, 'Patronato & CAF')">
                            <span class="emoji">🏢</span>Patronato CAF
                        </div>
                        <div class="biz-chip" onclick="selectBiz(this, 'Scuola & Formazione')">
                            <span class="emoji">🏫</span>Escuela
                        </div>
                    </div>

                    <div class="biz-custom">
                        <input type="text" id="biz-custom-input" placeholder="Oppure descrivi la tua attività..."
                            oninput="onBizCustomInput(this.value)" onfocus="clearBizChips()">
                        <button type="button" class="biz-custom-clear" id="biz-clear"
                            onclick="clearBizCustom()">×</button>
                    </div>
                    <div class="field-error" id="err-biz" style="margin-top:.4rem;">Seleziona o descrivi il tipo di
                        attività</div>

                    <!-- AI prompt preview -->
                    <div class="prompt-preview" id="prompt-preview">
                        <div class="prompt-preview-label">AI — Profilo generato</div>
                        <div id="prompt-preview-text"></div>
                    </div>

                    <!-- AI loading -->
                    <div class="ai-loading" id="ai-loading">
                        <div class="ai-spinner"></div>
                        <p>Configurazione AI in corso...</p>
                        <small>Docum24 analizza il tuo settore e prepara il profilo</small>
                    </div>

                    <div class="btn-row" id="step2-buttons">
                        <button type="button" class="btn-secondary" onclick="goStep(1)">← Indietro</button>
                        <button type="button" class="btn-primary" onclick="goStep(3)" id="btn-step2-next">
                            Continua →
                        </button>
                    </div>
                </div>

                <!-- ══ STEP 3 — Conferma & termini ═════════════════════ -->
                <div class="form-step" id="step-3">

                    <!-- Riepilogo -->
                    <div
                        style="background:var(--cream);border:1px solid var(--line);border-radius:8px;padding:1rem 1.1rem;margin-bottom:1.25rem;">
                        <div
                            style="font-size:11px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;color:var(--dim);margin-bottom:.75rem;">
                            Riepilogo</div>
                        <div style="display:flex;flex-direction:column;gap:.5rem;">
                            <div style="display:flex;gap:.6rem;font-size:13px;">
                                <span style="color:var(--mid);min-width:80px;">Nome</span>
                                <span id="recap-name" style="font-weight:500;color:var(--ink);">—</span>
                            </div>
                            <div style="display:flex;gap:.6rem;font-size:13px;">
                                <span style="color:var(--mid);min-width:80px;">Email</span>
                                <span id="recap-email" style="font-weight:500;color:var(--ink);">—</span>
                            </div>
                            <div style="display:flex;gap:.6rem;font-size:13px;">
                                <span style="color:var(--mid);min-width:80px;">Attività</span>
                                <span id="recap-biz" style="font-weight:500;color:var(--ink);">—</span>
                            </div>
                        </div>
                    </div>
                    <!-- Después del recap de attività -->
                    @if (request('plan') && request('plan') !== 'Trial')
                        <div style="display:flex;gap:.6rem;font-size:13px;">
                            <span style="color:var(--mid);min-width:80px;">Piano</span>
                            <span style="font-weight:500;color:#6366f1;">{{ request('plan') }} —
                                {{ request('plan') === 'Basic' ? '€9/mese' : '€29/mese' }}</span>
                        </div>
                    @endif
                    <!-- Trial info -->
                    @if (request('plan') && request('plan') !== 'Trial')
                        <div
                            style="background:#f0f0ff;border:1px solid rgba(99,102,241,.2);border-radius:8px;padding:.85rem 1rem;margin-bottom:1.25rem;display:flex;align-items:flex-start;gap:.75rem;">
                            <svg viewBox="0 0 16 16" fill="none" stroke="#6366f1" stroke-width="1.5"
                                style="width:16px;height:16px;flex-shrink:0;margin-top:1px;">
                                <path d="M8 1l1.5 3 3.5.5-2.5 2.5.5 3.5L8 10l-3 1.5.5-3.5L3 5.5 6.5 5z" />
                            </svg>
                            <div style="font-size:12px;color:#706f6c;line-height:1.55;">
                                <strong style="color:var(--ink);">Piano {{ request('plan') }} selezionato</strong> —
                                Dopo la registrazione verrai reindirizzato al pagamento sicuro con Stripe.
                            </div>
                        </div>
                    @else
                        <div
                            style="background:#fff8f7;border:1px solid rgba(245,48,3,.15);border-radius:8px;padding:.85rem 1rem;margin-bottom:1.25rem;display:flex;align-items:flex-start;gap:.75rem;">
                            <svg viewBox="0 0 16 16" fill="none" stroke="#F53003" stroke-width="1.5"
                                style="width:16px;height:16px;flex-shrink:0;margin-top:1px;">
                                <circle cx="8" cy="8" r="6" />
                                <path d="M8 5v3" />
                                <circle cx="8" cy="11" r=".5" fill="#F53003" />
                            </svg>
                            <div style="font-size:12px;color:#706f6c;line-height:1.55;">
                                <strong style="color:var(--ink);">Prova gratuita di 30 giorni</strong> — Nessuna carta
                                di credito richiesta.
                            </div>
                        </div>
                    @endif

                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="terms-row">
                            <input type="checkbox" class="terms-check" name="terms" id="terms" required>
                            <label class="terms-label" for="terms">
                                Accetto i <a target="_blank" href="{{ route('terms.show') }}">Termini di Servizio</a>
                                e la <a target="_blank" href="{{ route('policy.show') }}">Privacy Policy</a> di
                                Docum24.
                            </label>
                        </div>
                    @else
                        <div class="terms-row">
                            <input type="checkbox" class="terms-check" id="terms-simple" required>
                            <label class="terms-label" for="terms-simple">
                                Accetto i <a href="#">Termini di Servizio</a> e la <a href="#">Privacy
                                    Policy</a> di Docum24.
                            </label>
                        </div>
                    @endif

                    <div class="field-error" id="err-terms">Devi accettare i termini per continuare</div>

                    <div class="btn-row">
                        <button type="button" class="btn-secondary" onclick="goStep(2)">← Indietro</button>
                        <button type="submit" class="btn-primary" id="btn-submit" onclick="return validateStep3()">
                            @if (request('plan') && request('plan') !== 'Trial')
                                Crea account e vai al pagamento →
                            @else
                                Crea account gratuito
                            @endif
                        </button>
                    </div>
                </div>

            </form>

            <div class="login-link">
                Hai già un account? <a href="{{ route('login') }}">Accedi</a>
            </div>
        </div>
    </div>

    <script>
        let currentStep = 1;
        let bizValue = '';
        let aiPrompt = '';

        // ── NAVIGAZIONE STEP ────────────────────────────────────
        function goStep(n) {
            if (n > currentStep && !validateCurrentStep()) return;

            document.getElementById(`step-${currentStep}`).classList.remove('active');
            document.getElementById(`pip-${currentStep}`).classList.remove('active');
            document.getElementById(`pip-${currentStep}`).classList.add('done');

            currentStep = n;
            document.getElementById(`step-${currentStep}`).classList.add('active');
            document.getElementById(`pip-${currentStep}`).classList.add('active');

            // Undo "done" per step futuri
            for (let i = n; i <= 3; i++) {
                const p = document.getElementById(`pip-${i}`);
                if (i === n) {
                    p.classList.add('active');
                    p.classList.remove('done');
                } else {
                    p.classList.remove('active');
                    p.classList.remove('done');
                }
            }

            document.getElementById('step-label').textContent = `Passo ${n} di 3`;

            if (n === 3) populateRecap();
        }

        // ── VALIDAZIONE PER STEP ─────────────────────────────────
        function validateCurrentStep() {
            if (currentStep === 1) return validateStep1();
            if (currentStep === 2) return validateStep2();
            return true;
        }

        function validateStep1() {
            let ok = true;
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const pw = document.getElementById('password').value;
            const pwc = document.getElementById('password_confirmation').value;

            showErr('err-name', !name);
            showErr('err-email', !email || !email.includes('@'));
            showErr('err-password', pw.length < 8);
            showErr('err-confirm', pw !== pwc);

            if (!name || !email || !email.includes('@') || pw.length < 8 || pw !== pwc) ok = false;
            return ok;
        }

        function validateStep2() {
            if (!bizValue) {
                showErr('err-biz', true);
                return false;
            }
            showErr('err-biz', false);

            // Se no hay prompt aún, generarlo
            if (!aiPrompt) {
                generateAiPrompt();
                return false; // bloqueamos hasta que termine
            }
            return true;
        }

        function validateStep3() {
            const termsEl = document.getElementById('terms') || document.getElementById('terms-simple');
            if (!termsEl.checked) {
                showErr('err-terms', true);
                return false;
            }
            showErr('err-terms', false);
            // Aseguramos que los hidden fields tengan valor
            document.getElementById('hidden-business-type').value = bizValue;
            document.getElementById('hidden-ai-prompt').value = aiPrompt;
            return true;
        }

        function showErr(id, show) {
            const el = document.getElementById(id);
            if (el) el.classList.toggle('show', show);
        }

        // ── PASSWORD STRENGTH ────────────────────────────────────
        function updatePwStrength(pw) {
            let score = 0;
            if (pw.length >= 8) score++;
            if (/[A-Z]/.test(pw)) score++;
            if (/[0-9]/.test(pw)) score++;
            if (/[^A-Za-z0-9]/.test(pw)) score++;

            const colors = ['', '#ef4444', '#f59e0b', '#6366f1', '#10b981'];
            for (let i = 1; i <= 4; i++) {
                document.getElementById(`pw-bar-${i}`).style.background =
                    i <= score ? colors[score] : 'var(--line)';
            }
        }

        // ── BUSINESS TYPE ────────────────────────────────────────
        function selectBiz(el, value) {
            document.querySelectorAll('.biz-chip').forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');
            bizValue = value;
            document.getElementById('biz-custom-input').value = '';
            document.getElementById('biz-clear').style.display = 'none';
            showErr('err-biz', false);
            aiPrompt = ''; // reset prompt cuando cambia
            hidePromptPreview();
            // Auto-genera el prompt
            generateAiPrompt();
        }

        function clearBizChips() {
            document.querySelectorAll('.biz-chip').forEach(c => c.classList.remove('selected'));
        }

        function onBizCustomInput(val) {
            bizValue = val.trim();
            document.getElementById('biz-clear').style.display = val ? 'block' : 'none';
            aiPrompt = '';
            hidePromptPreview();
            // Debounce: genera prompt dopo 1s di pausa
            clearTimeout(window._bizTimeout);
            if (val.trim().length >= 3) {
                window._bizTimeout = setTimeout(() => generateAiPrompt(), 1000);
            }
        }

        function clearBizCustom() {
            document.getElementById('biz-custom-input').value = '';
            document.getElementById('biz-clear').style.display = 'none';
            bizValue = '';
            aiPrompt = '';
            hidePromptPreview();
        }

        // ── AI PROMPT GENERATION ─────────────────────────────────
        async function generateAiPrompt() {
            if (!bizValue) return;

            // Show loading, hide buttons
            document.getElementById('ai-loading').classList.add('show');
            document.getElementById('step2-buttons').style.display = 'none';
            hidePromptPreview();

            try {
                const res = await fetch('/user/business-prompt-preview', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                            '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        business_type: bizValue
                    })
                });

                const data = await res.json();

                if (data.success && data.prompt) {
                    aiPrompt = data.prompt;
                    document.getElementById('hidden-business-type').value = bizValue;
                    document.getElementById('hidden-ai-prompt').value = aiPrompt;
                    showPromptPreview(aiPrompt);
                }
            } catch (e) {
                console.error('AI prompt error:', e);
                // Continua comunque senza preview
                aiPrompt = `Assistente specializzato in ${bizValue}.`;
            }

            document.getElementById('ai-loading').classList.remove('show');
            document.getElementById('step2-buttons').style.display = 'flex';
        }

        function showPromptPreview(text) {
            const box = document.getElementById('prompt-preview');
            const txt = document.getElementById('prompt-preview-text');
            // Mostrare solo i primi 150 chars + "..."
            txt.textContent = text.length > 150 ? text.slice(0, 150) + '…' : text;
            box.classList.add('show');
        }

        function hidePromptPreview() {
            document.getElementById('prompt-preview').classList.remove('show');
        }

        // ── RECAP STEP 3 ─────────────────────────────────────────
        function populateRecap() {
            document.getElementById('recap-name').textContent = document.getElementById('name').value;
            document.getElementById('recap-email').textContent = document.getElementById('email').value;
            document.getElementById('recap-biz').textContent = bizValue || '—';
        }

        // ── FORM SUBMIT ──────────────────────────────────────────
        document.getElementById('register-form').addEventListener('submit', function(e) {
            document.getElementById('hidden-business-type').value = bizValue;
            document.getElementById('hidden-ai-prompt').value = aiPrompt;

            const btn = document.getElementById('btn-submit');
            btn.disabled = true;
            btn.textContent = 'Creazione account...';
        });

        // ── ENTER KEY ────────────────────────────────────────────
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                if (currentStep === 1) {
                    e.preventDefault();
                    goStep(2);
                }
                if (currentStep === 2) {
                    e.preventDefault();
                    goStep(3);
                }
            }
        });
    </script>

</body>

</html>
