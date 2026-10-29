<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Docum24 — Crea il tuo account</title>
    <link rel="shortcut icon" type="image/png" href="{{ url('images/icon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|instrument-serif:400i" rel="stylesheet" />

    <style>
        :root {
            /* Colores de marca extraídos del logotipo */
            --brand-red: #FF3503;
            --brand-red-hover: #E02E02;
            --brand-red-light: rgba(255, 53, 3, 0.08);
            --brand-red-border: rgba(255, 53, 3, 0.25);
            
            --brand-dark: #1E2022;
            --brand-dark-hover: #2D3135;
            
            --text-main: #1E2022;
            --text-muted: #64748B;
            --text-dim: #94A3B8;
            
            --bg-page: #F8F6F2;
            --bg-card: #FFFFFF;
            --border-color: #E2E8F0;
            --border-focus: #1E2022;
            --error-bg: #FEF2F2;
            --error-border: #FCA5A5;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-main);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        body { min-height: 100vh; display: flex; flex-direction: column; }

        /* NAV */
        nav.topbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem; height: 60px;
            background: rgba(248, 246, 242, 0.88); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
        }
        
        .nav-logo { display: flex; align-items: center; gap: .5rem; text-decoration: none; }
        .nav-logo img { height: 28px; width: auto; display: block; }

        .nav-login-hint { font-size: 13px; color: var(--text-muted); }
        .nav-login-hint a { color: var(--brand-dark); font-weight: 600; text-decoration: underline; text-underline-offset: 3px; transition: color .15s; }
        .nav-login-hint a:hover { color: var(--brand-red); }

        /* LAYOUT */
        .page-wrap { flex: 1; display: grid; grid-template-columns: 1fr 1fr; min-height: 100vh; padding-top: 60px; }

        /* LEFT PANEL */
        .left-panel {
            background: var(--brand-dark); color: #fff; padding: 4rem 3.5rem;
            display: flex; flex-direction: column; justify-content: center;
            position: sticky; top: 60px; height: calc(100vh - 60px); overflow-y: auto;
        }

        .left-badge {
            display: inline-flex; align-items: center; gap: .5rem; align-self: flex-start;
            border: 1px solid rgba(255,255,255,.18); background: rgba(255,255,255,.05);
            border-radius: 99px; padding: .35rem .85rem; font-size: 11px; font-weight: 500;
            letter-spacing: .05em; text-transform: uppercase; color: rgba(255,255,255,.8); margin-bottom: 2rem;
        }

        .left-dot {
            width: 6px; height: 6px; border-radius: 50%; background: var(--brand-red);
            box-shadow: 0 0 8px var(--brand-red); animation: pulsedot 2s infinite ease-in-out;
        }
        @keyframes pulsedot { 0%,100% { opacity: 1; transform: scale(1); } 50% { opacity: .4; transform: scale(.85); } }

        .left-panel h2 { font-size: clamp(2rem, 2.5vw, 2.75rem); font-weight: 600; letter-spacing: -.03em; line-height: 1.15; margin-bottom: 1.25rem; color: #fff; }
        .left-panel h2 em { font-family: 'Instrument Serif', serif; font-style: italic; font-weight: 400; color: var(--brand-red); }
        .left-panel p { font-size: 14.5px; color: rgba(255,255,255,.7); line-height: 1.65; margin-bottom: 2.5rem; max-width: 460px; }

        .feature-list { display: flex; flex-direction: column; gap: 1rem; }
        .feat { display: flex; align-items: flex-start; gap: .85rem; }
        .feat-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px;
        }
        .feat-icon svg { width: 15px; height: 15px; color: #fff; }
        .feat-text { font-size: 13.5px; color: rgba(255,255,255,.7); line-height: 1.5; }
        .feat-text strong { color: #fff; font-weight: 600; }

        .trial-pill {
            display: inline-flex; align-items: center; gap: .6rem; align-self: flex-start;
            background: rgba(255,53,3,.12); border: 1px solid var(--brand-red-border);
            border-radius: 8px; padding: .65rem 1rem; margin-top: 2.5rem; font-size: 12.5px; color: #FF8A73;
        }
        .trial-pill svg { width: 15px; height: 15px; flex-shrink: 0; color: var(--brand-red); }

        /* RIGHT PANEL */
        .right-panel {
            background: var(--bg-card); display: flex; flex-direction: column;
            justify-content: center; padding: 3rem 4rem; min-height: calc(100vh - 60px);
        }

        .form-header { margin-bottom: 2rem; }
        .form-header h1 { font-size: 1.65rem; font-weight: 600; letter-spacing: -.025em; color: var(--brand-dark); margin-bottom: .35rem; }
        .form-header p { font-size: 13.5px; color: var(--text-muted); }

        /* INPUTS */
        .field { margin-bottom: 1.25rem; }
        .field label { display: block; font-size: 12px; font-weight: 600; color: var(--brand-dark); margin-bottom: .4rem; letter-spacing: .01em; }
        
        .input-wrapper { position: relative; display: flex; align-items: center; }
        .field input {
            width: 100%; padding: .7rem .9rem; font-family: 'Instrument Sans', sans-serif;
            font-size: 14px; color: var(--text-main); background: var(--bg-page);
            border: 1px solid var(--border-color); border-radius: 7px; outline: none;
            transition: border-color .15s ease, box-shadow .15s ease, background .15s ease;
        }
        .field input:focus { background: #fff; border-color: var(--brand-dark); box-shadow: 0 0 0 3px rgba(30, 32, 34, 0.08); }
        .field input.error { border-color: var(--brand-red); background: #FFFBFB; }
        .field input.error:focus { box-shadow: 0 0 0 3px rgba(255, 53, 3, 0.12); }

        .pw-toggle {
            position: absolute; right: .75rem; background: none; border: none;
            color: var(--text-muted); cursor: pointer; padding: .2rem; display: flex;
            align-items: center; justify-content: center; transition: color .15s;
        }
        .pw-toggle:hover { color: var(--brand-dark); }

        .field-error { font-size: 11.5px; color: var(--brand-red); margin-top: .35rem; display: none; font-weight: 500; }
        .field-error.show { display: block; }

        /* PASSWORD STRENGTH */
        .pw-strength-wrap { margin-top: .5rem; }
        .pw-strength { display: flex; gap: 4px; }
        .pw-bar { height: 4px; flex: 1; border-radius: 2px; background: var(--border-color); transition: background .25s ease; }

        /* TERMS */
        .terms-row { display: flex; align-items: flex-start; gap: .65rem; margin-bottom: 1.25rem; }
        .terms-check {
            width: 17px; height: 17px; border: 1.5px solid #CBD5E1; border-radius: 4px;
            cursor: pointer; flex-shrink: 0; margin-top: 2px; appearance: none; -webkit-appearance: none;
            background: var(--bg-page); transition: all .15s ease; position: relative;
        }
        .terms-check:checked { background: var(--brand-dark); border-color: var(--brand-dark); }
        .terms-check:checked::after {
            content: ''; position: absolute; left: 4px; top: 1px; width: 5px; height: 9px;
            border: 2px solid #fff; border-top: none; border-left: none; transform: rotate(45deg);
        }
        .terms-check:focus-visible { outline: 2px solid var(--brand-dark); outline-offset: 2px; }

        .terms-label { font-size: 12.5px; color: var(--text-muted); line-height: 1.5; }
        .terms-label a { color: var(--brand-dark); font-weight: 600; text-decoration: underline; text-underline-offset: 2px; transition: color .15s; }
        .terms-label a:hover { color: var(--brand-red); }

        /* BUTTONS */
        .btn-primary {
            width: 100%; padding: .8rem 1.2rem; background: var(--brand-red); color: #fff;
            border: none; border-radius: 7px; font-family: 'Instrument Sans', sans-serif;
            font-size: 14.5px; font-weight: 600; cursor: pointer;
            transition: background .15s ease, transform .1s ease, box-shadow .15s ease;
            box-shadow: 0 2px 4px rgba(255, 53, 3, 0.15);
        }
        .btn-primary:hover { background: var(--brand-red-hover); box-shadow: 0 4px 8px rgba(255, 53, 3, 0.25); }
        .btn-primary:active { transform: translateY(1px); }
        .btn-primary:disabled { opacity: .65; cursor: not-allowed; box-shadow: none; }

        /* TRIAL INFO */
        .trial-info {
            border-radius: 8px; padding: .85rem 1rem; margin-bottom: 1.25rem;
            display: flex; align-items: flex-start; gap: .75rem; font-size: 12.5px; line-height: 1.5;
        }
        .trial-info svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px; }
        .trial-info strong { color: var(--brand-dark); }
        .trial-info span { color: var(--text-muted); }

        /* SERVER ERRORS */
        .server-errors {
            background: var(--error-bg); border: 1px solid var(--error-border);
            border-radius: 8px; padding: .85rem 1rem; margin-bottom: 1.25rem; font-size: 12.5px; color: #B91C1C;
        }
        .server-errors ul { padding-left: 1.25rem; }
        .server-errors li { margin-bottom: .2rem; }

        .login-link { margin-top: 1.5rem; text-align: center; font-size: 13px; color: var(--text-muted); }
        .login-link a { color: var(--brand-dark); font-weight: 600; text-decoration: underline; text-underline-offset: 3px; transition: color .15s; }
        .login-link a:hover { color: var(--brand-red); }

        @media(max-width: 900px) {
            .page-wrap { grid-template-columns: 1fr; }
            .left-panel { display: none; }
            .right-panel { padding: 2.5rem 1.5rem; }
        }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .fade-up { animation: fadeUp .35s ease-out forwards; }
    </style>
</head>

<body>

    <nav class="topbar">
        <a class="nav-logo" href="/">
            <img src="{{ asset('images/logo.png') }}" alt="Docum24">
        </a>
        <div class="nav-login-hint">
            Hai già un account? <a href="{{ route('login') }}">Accedi</a>
        </div>
    </nav>

    <div class="page-wrap">

        <!-- LEFT PANEL -->
        <div class="left-panel">
            <div class="left-badge">
                <span class="left-dot"></span>
                @if (request('plan') && request('plan') !== 'Trial')
                    Piano {{ request('plan') }}
                @else
                    Prova gratuita 30 giorni
                @endif
            </div>

            <h2>Il tuo business,<br>gestito <em>dall'AI.</em></h2>
            <p>Docum24 impara il tuo settore e diventa un esperto della tua azienda. Documenti sempre in ordine, report automatici, zero confusione.</p>

            <div class="feature-list">
                <div class="feat">
                    <div class="feat-icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="7" cy="7" r="3"/><path d="M7 1v2M7 11v2M1 7h2M11 7h2"/></svg>
                    </div>
                    <div class="feat-text"><strong>AI specializzata per settore</strong> — Si configura automaticamente per il tuo tipo di business.</div>
                </div>
                <div class="feat">
                    <div class="feat-icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 2h5l3 3v7H3V2z"/><path d="M8 2v3h3"/></svg>
                    </div>
                    <div class="feat-text"><strong>Gestione documentale intelligente</strong> — Carica, classifica e ritrova qualsiasi file in secondi.</div>
                </div>
                <div class="feat">
                    <div class="feat-icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M2 11l3-3 2 2 5-5"/></svg>
                    </div>
                    <div class="feat-text"><strong>Report automatici</strong> — Agenti AI generano analisi e sintesi nel linguaggio del tuo settore.</div>
                </div>
                <div class="feat">
                    <div class="feat-icon">
                        <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="5" cy="7" r="3"/><circle cx="9.5" cy="4" r="2.5"/><path d="M7.5 6.5l2-1"/></svg>
                    </div>
                    <div class="feat-text"><strong>Team fino a 5 utenti</strong> — Collabora con il tuo team nel piano Business.</div>
                </div>
            </div>

            <div class="trial-pill">
                <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M7 1v6l3 2"/><circle cx="7" cy="7" r="6"/></svg>
                @if (request('plan') && request('plan') !== 'Trial')
                    <span><strong>Piano {{ request('plan') }}</strong> — {{ request('plan') === 'Basic' ? '€9/mese' : '€29/mese' }}</span>
                @else
                    <span><strong>30 giorni gratis</strong> — Nessuna carta di credito richiesta.</span>
                @endif
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel">

            <div class="form-header">
                <h1>Crea il tuo account</h1>
                <p>Inizia gratis. Configura Docum24 per la tua azienda in 2 minuti.</p>
            </div>

            @if ($errors->any())
                <div class="server-errors" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="register-form" class="fade-up" novalidate>
                @csrf
                <input type="hidden" name="selected_plan" value="{{ request('plan') }}">
                <input type="text" name="website" style="display:none !important" tabindex="-1" autocomplete="off" value="">

                <div class="field">
                    <label for="name">Nome e cognome *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Es. Marco Rossi" autocomplete="name" autofocus required aria-describedby="err-name">
                    <div class="field-error" id="err-name">Inserisci il tuo nome</div>
                </div>

                <div class="field">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="marco@studio.it" autocomplete="email" required aria-describedby="err-email">
                    <div class="field-error" id="err-email">Inserisci un'email valida</div>
                </div>

                <div class="field">
                    <label for="password">Password *</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Minimo 8 caratteri" autocomplete="new-password" required aria-describedby="err-password" oninput="updatePwStrength(this.value)">
                        <button type="button" class="pw-toggle" onclick="togglePasswordVisibility('password', this)" aria-label="Mostra password">
                            <svg class="eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="pw-strength-wrap">
                        <div class="pw-strength">
                            <div class="pw-bar" id="pw-bar-1"></div>
                            <div class="pw-bar" id="pw-bar-2"></div>
                            <div class="pw-bar" id="pw-bar-3"></div>
                            <div class="pw-bar" id="pw-bar-4"></div>
                        </div>
                    </div>
                    <div class="field-error" id="err-password">Minimo 8 caratteri</div>
                </div>

                <div class="field">
                    <label for="password_confirmation">Conferma password *</label>
                    <div class="input-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ripeti la password" autocomplete="new-password" required aria-describedby="err-confirm">
                        <button type="button" class="pw-toggle" onclick="togglePasswordVisibility('password_confirmation', this)" aria-label="Mostra password">
                            <svg class="eye-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="field-error" id="err-confirm">Le password non coincidono</div>
                </div>

                @if (request('plan') && request('plan') !== 'Trial')
                    <div class="trial-info" style="background: rgba(30, 32, 34, 0.03); border: 1px solid var(--border-color);">
                        <svg viewBox="0 0 16 16" fill="none" stroke="var(--brand-dark)" stroke-width="1.5"><path d="M8 1l1.5 3 3.5.5-2.5 2.5.5 3.5L8 10l-3 1.5.5-3.5L3 5.5 6.5 5z"/></svg>
                        <span><strong>Piano {{ request('plan') }} selezionato</strong> — Dopo la registrazione verrai reindirizzato al pagamento sicuro con Stripe.</span>
                    </div>
                @else
                    <div class="trial-info" style="background: var(--brand-red-light); border: 1px solid var(--brand-red-border);">
                        <svg viewBox="0 0 16 16" fill="none" stroke="var(--brand-red)" stroke-width="1.5"><circle cx="8" cy="8" r="6"/><path d="M8 5v3"/><circle cx="8" cy="11" r=".5" fill="var(--brand-red)"/></svg>
                        <span><strong>Prova gratuita di 30 giorni</strong> — Nessuna carta di credito richiesta.</span>
                    </div>
                @endif

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="terms-row">
                        <input type="checkbox" class="terms-check" name="terms" id="terms" required aria-describedby="err-terms">
                        <label class="terms-label" for="terms">
                            Accetto i <a target="_blank" href="{{ route('terms.show') }}">Termini di Servizio</a>
                            e la <a target="_blank" href="{{ route('policy.show') }}">Privacy Policy</a> di Docum24.
                        </label>
                    </div>
                @else
                    <div class="terms-row">
                        <input type="checkbox" class="terms-check" id="terms-simple" required aria-describedby="err-terms">
                        <label class="terms-label" for="terms-simple">
                            Accetto i <a href="#" onclick="openModal('modal-termini');return false;">Termini di Servizio</a> e la <a href="#" onclick="openModal('modal-privacy');return false;">Privacy Policy</a> di Docum24.
                        </label>
                    </div>
                @endif

                <div class="field-error" id="err-terms" style="margin-bottom: 1rem;">Devi accettare i termini per continuare</div>

                <button type="submit" class="btn-primary" id="btn-submit" onclick="return validateForm()">
                    @if (request('plan') && request('plan') !== 'Trial')
                        Crea account e vai al pagamento →
                    @else
                        Crea account gratuito
                    @endif
                </button>
            </form>

            <div class="login-link">
                Hai già un account? <a href="{{ route('login') }}">Accedi</a>
            </div>
        </div>
    </div>

    <script>
        function validateForm() {
            let ok = true;
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const pw = document.getElementById('password').value;
            const pwc = document.getElementById('password_confirmation').value;
            const termsEl = document.getElementById('terms') || document.getElementById('terms-simple');

            const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

            showErr('err-name', 'name', !name); if (!name) ok = false;
            showErr('err-email', 'email', !isEmailValid); if (!isEmailValid) ok = false;
            showErr('err-password', 'password', pw.length < 8); if (pw.length < 8) ok = false;
            showErr('err-confirm', 'password_confirmation', pw !== pwc || !pwc); if (pw !== pwc || !pwc) ok = false;
            showErr('err-terms', null, !termsEl.checked); if (!termsEl.checked) ok = false;

            if (ok) {
                const btn = document.getElementById('btn-submit');
                btn.disabled = true;
                btn.textContent = 'Creazione account...';
            }
            return ok;
        }

        function showErr(errId, inputId, show) {
            const errEl = document.getElementById(errId);
            if (errEl) errEl.classList.toggle('show', show);
            if (inputId) {
                const inputEl = document.getElementById(inputId);
                if (inputEl) inputEl.classList.toggle('error', show);
            }
        }

        function updatePwStrength(pw) {
            let score = 0;
            if (pw.length >= 8) score++;
            if (/[A-Z]/.test(pw)) score++;
            if (/[0-9]/.test(pw)) score++;
            if (/[^A-Za-z0-9]/.test(pw)) score++;

            const colors = ['var(--border-color)', '#EF4444', '#F59E0B', '#3B82F6', '#10B981'];
            for (let i = 1; i <= 4; i++) {
                document.getElementById('pw-bar-' + i).style.background = i <= score ? colors[score] : 'var(--border-color)';
            }
        }

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.setAttribute('aria-label', isPassword ? 'Nascondi password' : 'Mostra password');
            btn.innerHTML = isPassword 
                ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0112 19c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
                : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        }
    </script>

    @include('partials.modal-styles')
    @include('partials.privacy-modal')
    @include('partials.termini-modal')
    @include('partials.dati-modal')
    @include('partials.contatti-modal')
</body>

</html>