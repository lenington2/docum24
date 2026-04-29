<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestione Documenti IA</title>
    <link rel="shortcut icon" type="image/png" href="{{ url('images/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        :root {
            /* Colores Landing */
            --cream: #FDFDFC;
            --ink: #1b1b18;
            --red: #F53003;
            --line: #e3e3e0;
            --white: #ffffff;

            /* Mapeo para el Chat - MODO OSCURO (Default) */
            --outgoing-chat-bg: #1b1b18;
            /* Fondo principal tinto */
            --incoming-chat-bg: #242421;
            /* Un tono sutilmente más claro para burbujas */
            --incoming-chat-border: #2e2e2a;
            /* Bordes suaves para evitar "rayas" */
            --text-color: #FDFDFC;
            --icon-color: #a8a7a3;
            --accent: #F53003;
            /* Rojo de la landing */
        }

        .light-mode {
            /* Fondo principal y burbujas: usamos el mismo para unificar */
            --outgoing-chat-bg: #FDFDFC;
            --incoming-chat-bg: #FDFDFC;

            /* Bordes sutiles de la landing */
            --incoming-chat-border: #e3e3e0;

            /* Textos e iconos */
            --text-color: #1b1b18;
            /* El color 'Ink' de tu landing */
            --icon-color: #706f6c;
            /* El color 'Mid' de tu landing */

            /* Acento */
            --accent: #F53003;
            /* El rojo de tu landing */
        }

        /* Ajuste para modo claro */
        .light-mode .chat.incoming .chat-content {
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        }

        /* Eliminamos los fondos que ocupaban todo el ancho */
        .chat.outgoing,
        .chat.incoming {
            background: transparent !important;
        }

        body {
            background: var(--outgoing-chat-bg);
            color: var(--text-color);
            font-family: 'Instrument Sans', sans-serif;
            /* Tipografía de la landing */
            transition: background 0.3s ease;
        }

        header {
            background: var(--incoming-chat-bg);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--incoming-chat-border);
            transition: all 0.3s ease, padding-right 0.3s ease;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-text {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-color);
        }

        .logo-text span {
            color: #3b82f6;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .theme-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
            background: var(--icon-hover-bg);
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .toggle-switch::before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: white;
            top: 3px;
            left: 3px;
            transition: transform 0.3s;
        }

        .toggle-switch.active::before {
            transform: translateX(24px);
        }

        .theme-icon {
            color: var(--icon-color);
            font-size: 1.3rem;
        }

        nav {
            display: flex;
            gap: 1rem;
        }

        nav a {
            padding: 0.5rem 1.25rem;
            text-decoration: none;
            color: var(--icon-color);
            border-radius: 4px;
            font-size: 0.875rem;
            transition: all 0.2s;
            border: 1px solid var(--icon-color);
        }

        nav a:hover {
            background: var(--icon-hover-bg);
            color: var(--text-color);
        }

        .chat-container {
            height: calc(100vh - 160px);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 20px 10px 120px !important;
            scroll-behavior: smooth;
            transition: margin-left 0.3s ease, margin-right 0.3s ease;
            /* AÑADIR */
        }

        /* Para que los mensajes no se peguen al input */
        .chat:last-child {
            margin-bottom: 20px;
        }

        .chat-container::-webkit-scrollbar {
            width: 6px;
        }

        .chat-container::-webkit-scrollbar-track {
            background: var(--incoming-chat-bg);
            border-radius: 25px;
        }

        .chat-container::-webkit-scrollbar-thumb {
            background: var(--icon-color);
            border-radius: 25px;
        }

        .default-text {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 60vh;
            padding: 20px;
            text-align: center;
            color: var(--text-color);
        }

        .default-text h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .default-text p {
            margin-top: 10px;
            font-size: 1.1rem;
            color: var(--icon-color);
        }

        /* Contenedor base del mensaje */
        .chat {
            padding: 15px 20px;
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .chat.outgoing {
            background: var(--outgoing-chat-bg);
        }

        .chat.incoming {
            background: var(--incoming-chat-bg);
        }

        /* La "Card" o burbuja que encierra el texto */
        .chat-content {
            display: flex;
            max-width: 850px;
            /* Un poco más estrecho para que parezca burbuja */
            width: 100%;
            align-items: flex-start;
            gap: 15px;
            padding: 18px;
            border-radius: 16px;
            transition: all 0.3s ease;
        }

        /* MENSAJE DEL BOT (Entrante) */
        .chat.incoming .chat-content {
            background: var(--incoming-chat-bg);
            border: 1px solid var(--incoming-chat-border);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            /* Sombra muy sutil */
        }

        /* MENSAJE DEL USUARIO (Saliente) */
        .chat.outgoing .chat-content {
            /* Si prefieres que el usuario también tenga card, quita el fondo del body */
            background: rgba(168, 167, 163, 0.05);
            border: 1px solid transparent;
        }

        .chat-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--outgoing-chat-bg);
            border: 1px solid var(--incoming-chat-border);
            flex-shrink: 0;
        }

        .chat-text {
            flex: 1;
            font-size: 1.05rem;
            line-height: 1.6;
            word-wrap: break-word;
        }

        .chat-text p {
            margin: 8px 0;
        }

        .chat-text h1,
        .chat-text h2,
        .chat-text h3 {
            font-weight: 600;
            color: var(--text-color);
        }

        .chat-text h1 {
            font-size: 1.3rem;
            margin: 20px 0 12px 0;
        }

        .chat-text h2 {
            font-size: 1.2rem;
            margin: 18px 0 10px 0;
        }

        .chat-text h3 {
            font-size: 1.1rem;
            margin: 15px 0 8px 0;
        }

        .chat-text hr {
            margin: 15px 0;
            border: none;
            border-top: 1px solid var(--icon-color);
            opacity: 0.3;
        }

        .chat-text strong {
            font-weight: 600;
            color: var(--text-color);
        }

        .chat-text em {
            font-style: italic;
        }

        .chat-text ol,
        .chat-text ul {
            margin: 10px 0;
            padding-left: 25px;
        }

        .chat-text li {
            margin: 5px 0;
            line-height: 1.5;
        }

        .chat-text li.writing {
            list-style-type: none;
        }

        .chat-text ol {
            list-style-type: decimal;
        }

        .chat-text ul {
            list-style-type: disc;
        }

        .typing-animation {
            display: inline-flex;
            gap: 5px;
            padding: 10px 0;
        }

        .typing-dot {
            height: 8px;
            width: 8px;
            border-radius: 50%;
            background: var(--text-color);
            opacity: 0.7;
            animation: animateDots 1.5s ease-in-out infinite;
        }

        .typing-dot:nth-child(1) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.3s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes animateDots {

            0%,
            44% {
                transform: translateY(0px);
            }

            28% {
                opacity: 0.4;
                transform: translateY(-6px);
            }

            44% {
                opacity: 0.2;
            }
        }

        .ai-cursor {
            display: inline-block;
            width: 8px;
            height: 20px;
            background: var(--text-color);
            margin-left: 2px;
            animation: blink 0.7s infinite;
        }

        @keyframes blink {

            0%,
            49% {
                opacity: 1;
            }

            50%,
            100% {
                opacity: 0;
            }
        }

        .typing-container {
            position: fixed;
            bottom: 0;
            width: 100%;
            display: flex;
            flex-direction: column;
            /* Forzamos que los hijos se apilen verticalmente */
            align-items: center;
            /* Centramos el contenido horizontalmente */
            padding: 12px 10px 15px;
            background: var(--outgoing-chat-bg);
            border-top: 1px solid var(--incoming-chat-border);
            transition: padding-left 0.3s ease, margin-right 0.3s ease;
            z-index: 200;
        }

        .typing-content {
            display: flex;
            max-width: 1000px;
            width: 100%;
            align-items: flex-end;
            gap: 10px;
        }

        .typing-textarea {
            flex: 1;
            position: relative;
        }

        #chat-input {
            resize: none;
            width: 100%;
            min-height: 55px;
            max-height: 200px;
            border: none;
            padding: 15px 50px 15px 20px;
            color: var(--text-color);
            font-size: 1rem;
            border-radius: 8px;
            background: var(--incoming-chat-bg);
            outline: 1px solid var(--incoming-chat-border);
            font-family: "Poppins", sans-serif;
        }

        #chat-input::placeholder {
            color: var(--placeholder-color);
        }

        #chat-input:focus {
            outline: 2px solid var(--icon-color);
        }

        #send-btn {
            position: absolute;
            right: 10px;
            bottom: 10px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--icon-color);
            cursor: pointer;
            border-radius: 4px;
            transition: all 0.2s;
        }

        #send-btn:hover {
            background: var(--icon-hover-bg);
            color: var(--text-color);
        }

        #send-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @media screen and (max-width: 768px) {
            header {
                padding: 1rem;
            }

            .logo-text {
                font-size: 1rem;
            }

            .header-right {
                gap: 0.5rem;
            }

            nav a {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
            }
        }

        @media screen and (max-width: 600px) {
            .default-text h1 {
                font-size: 2rem;
            }

            .default-text p {
                font-size: 0.95rem;
            }

            .chat {
                padding: 15px 10px;
            }

            .chat-text {
                font-size: 0.95rem;
            }


            #chat-input {
                min-height: 45px;
                padding: 12px 45px 12px 15px;
            }

            .logo-text {
                display: none;
            }
        }

        .deferred-reveal {
            visibility: hidden;
        }

        .chat-text img {
            max-width: 160px;
            height: auto;
            object-fit: contain;
        }

        .swal-cancel-custom {
            border: 1px solid var(--incoming-chat-border) !important;
            color: var(--text-color) !important;
        }

        .swal2-popup {
            background: var(--incoming-chat-bg) !important;
            color: var(--text-color) !important;
            border: 1px solid var(--incoming-chat-border) !important;
            border-radius: 14px !important;
        }

        .swal2-title {
            color: var(--text-color) !important;
        }

        .swal2-html-container {
            color: var(--text-color) !important;
        }

        .quick-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .quick-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            background: var(--incoming-chat-bg);
            border: 1px solid var(--incoming-chat-border);
            border-radius: 16px;
            padding: 24px 20px;
            width: 130px;
            cursor: pointer;
            opacity: 0;
            transform: translateY(16px);
            animation: fadeUpCard 0.5s ease forwards;
            animation-delay: var(--delay);
            transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
        }

        .quick-card:hover {
            transform: translateY(-4px);
            border-color: var(--accent);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.15);
        }

        .quick-card .material-symbols-rounded {
            font-size: 42px;
        }

        .quick-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-color);
            text-align: center;
        }

        @keyframes fadeUpCard {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chat-form-center {
            display: flex;
            justify-content: center;
            padding: 16px 10px;
        }

        .chat-form-card {
            background: var(--incoming-chat-bg);
            border: 1px solid var(--incoming-chat-border);
            border-radius: 16px;
            padding: 20px;
            width: 100%;
            max-width: 420px;
        }

        .chat-action-icons {
            display: flex;
            justify-content: center;
            gap: 16px;
            padding: 16px 10px;
        }

        .chat-action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 18px 24px;
            border-radius: 14px;
            border: 1px solid var(--incoming-chat-border);
            background: var(--incoming-chat-bg);
            cursor: pointer;
            transition: all 0.2s;
            min-width: 90px;
            font-family: 'Poppins', sans-serif;
        }

        .chat-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .chat-action-btn .material-symbols-rounded {
            font-size: 32px;
        }

        .chat-action-btn span.label {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-color);
        }

        .quick-actions,
        #cards-progetto {
            transition: opacity 0.5s ease-in;
        }
    </style>
</head>

<body>
    <div id="themeToggle" style="display:none;"></div>
    <!-- Header -->
    <header>
        <div class="logo-container">
            <span class="logo-text"><span>Docu</span>Flow</span>
        </div>

        <div class="header-right">

            <div style="position:relative;">
                <button id="btn-piano-widget" onclick="togglePianoWidget()"
                    style="width:40px;height:40px;border-radius:50%;background:var(--incoming-chat-bg);
               color:var(--icon-color);border:1px solid var(--incoming-chat-border);
               cursor:pointer;display:flex;align-items:center;justify-content:center;
               transition:all 0.2s;"
                    onmouseover="this.style.background='var(--icon-hover-bg)'"
                    onmouseout="this.style.background='var(--incoming-chat-bg)'">
                    <span class="material-symbols-rounded" style="font-size:20px;">notifications</span>
                </button>

                <div id="piano-badge"
                    style="display:none;position:absolute;top:-2px;right:-2px;
                    width:10px;height:10px;border-radius:50%;background:#ef4444;
                    border:2px solid var(--outgoing-chat-bg);
                    animation:pulse 1.5s infinite;"
                    @if(!$tieneEmpresa) data-empresa="1" @endif>
                </div>

                <!-- Dropdown -->
                <div id="piano-dropdown"
                    style="display:none;position:absolute;right:0;top:calc(100% + 8px);
               background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);
               border-radius:14px;min-width:260px;
               box-shadow:0 8px 24px rgba(0,0,0,0.3);z-index:999;padding:16px;">

                    <!-- Plan + fecha -->
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span class="material-symbols-rounded"
                                style="font-size:16px;color:#6366f1;">workspace_premium</span>
                            <span id="pw-plan-nome"
                                style="font-size:13px;font-weight:700;color:var(--text-color);"></span>
                        </div>
                        <span id="pw-fecha" style="font-size:10px;color:var(--icon-color);"></span>
                    </div>

                    <!-- Tokens -->
                    <div style="margin-bottom:12px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                            <div style="display:flex;align-items:center;gap:5px;">
                                <span class="material-symbols-rounded"
                                    style="font-size:13px;color:var(--icon-color);">psychology</span>
                                <span style="font-size:11px;color:var(--icon-color);">Token AI</span>
                            </div>
                            <span id="pw-token-txt" style="font-size:11px;font-weight:600;"></span>
                        </div>
                        <div
                            style="height:6px;background:var(--incoming-chat-border);border-radius:10px;overflow:hidden;">
                            <div id="pw-token-bar"
                                style="height:100%;border-radius:10px;transition:width 0.5s ease;width:0%;"></div>
                        </div>
                    </div>

                    <!-- Storage -->
                    <div style="margin-bottom:12px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                            <div style="display:flex;align-items:center;gap:5px;">
                                <span class="material-symbols-rounded"
                                    style="font-size:13px;color:var(--icon-color);">storage</span>
                                <span style="font-size:11px;color:var(--icon-color);">Storage</span>
                            </div>
                            <span id="pw-storage-txt" style="font-size:11px;font-weight:600;"></span>
                        </div>
                        <div
                            style="height:6px;background:var(--incoming-chat-border);border-radius:10px;overflow:hidden;">
                            <div id="pw-storage-bar"
                                style="height:100%;border-radius:10px;transition:width 0.5s ease;width:0%;"></div>
                        </div>
                    </div>

                    <!-- Notifica profilo azienda — solo se no compilato -->
                    @if(!$tieneEmpresa)
                    <div onclick="togglePianoWidget(); abrirModalEmpresa();"
                        style="border-top:1px solid var(--incoming-chat-border);padding:10px 0 10px;display:flex;align-items:center;gap:10px;cursor:pointer;border-radius:8px;padding:8px 6px;margin-bottom:4px;"
                        onmouseover="this.style.background='var(--icon-hover-bg)'"
                        onmouseout="this.style.background='none'">
                        <span class="material-symbols-rounded" style="color:#f59e0b;font-size:20px;flex-shrink:0;">business</span>
                        <div style="flex:1;">
                            <p style="margin:0;font-size:12px;font-weight:600;color:var(--text-color);">Completa il profilo azienda</p>
                            <p style="margin:2px 0 0;font-size:11px;color:var(--icon-color);">Aggiungi logo e dati per i documenti</p>
                        </div>
                        <span class="material-symbols-rounded" style="font-size:16px;color:var(--icon-color);">chevron_right</span>
                    </div>
                    @endif

                    <!-- Upgrade -->
                    <div style="border-top:1px solid var(--incoming-chat-border);padding-top:10px;">
                        <p style="font-size:11px;color:var(--icon-color);margin-bottom:8px;text-align:center;">
                            Hai bisogno di più risorse?
                        </p>
                        <div style="display:flex;flex-direction:column;gap:6px;" id="pw-upgrade-btns">
                            <!-- Se rellena dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>

            <button onclick="startNewConversation()" title="Nuovo Chat"
                style="width:40px;height:40px;border-radius:50%;background:var(--incoming-chat-bg);color:var(--text-color);border:1px solid var(--incoming-chat-border);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
                onmouseover="this.style.background='var(--icon-hover-bg)';this.style.transform='rotate(180deg)'"
                onmouseout="this.style.background='var(--incoming-chat-bg)';this.style.transform='rotate(0deg)'">
                <span class="material-symbols-rounded"
                    style="font-size:20px;transition:transform 0.3s;">chat_add_on</span>
            </button>

            @auth
                <div style="position:relative;">
                    <button onclick="toggleUserMenu()"
                        style="display:flex;align-items:center;gap:8px;background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);color:var(--text-color);padding:6px 14px;border-radius:8px;cursor:pointer;font-family:'Poppins',sans-serif;font-size:0.875rem;transition:all 0.2s;"
                        onmouseover="this.style.background='var(--icon-hover-bg)'"
                        onmouseout="this.style.background='var(--incoming-chat-bg)'">
                        <div
                            style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#60a5fa,#3b82f6);display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;flex-shrink:0;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                    </button>

                    <!-- Dropdown -->
                    <div id="user-menu"
                        style="display:none;position:absolute;right:0;top:calc(100% + 8px);background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:10px;min-width:180px;box-shadow:0 8px 24px rgba(0,0,0,0.3);z-index:999;overflow:hidden;">
                        <div style="padding:12px 14px;border-bottom:1px solid var(--incoming-chat-border);">
                            <p style="font-size:12px;font-weight:600;color:var(--text-color);margin:0;">
                                {{ auth()->user()->name }}</p>
                            <p style="font-size:11px;color:var(--icon-color);margin:0;">{{ auth()->user()->email }}</p>
                        </div>
                        <button onclick="toggleUserMenu(); abrirPerfil();"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 14px;color:var(--text-color);background:none;border:none;font-family:'Poppins',sans-serif;font-size:13px;cursor:pointer;transition:background 0.2s;"
                            onmouseover="this.style.background='var(--icon-hover-bg)'"
                            onmouseout="this.style.background='transparent'">
                            <span class="material-symbols-rounded"
                                style="font-size:18px;color:var(--icon-color);">person</span>
                            Profilo
                        </button>
                        <hr style="height:1px;background-color:var(--incoming-chat-border);border:none;margin:0;">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 14px;color:var(--text-color);background:none;border:none;font-family:'Poppins',sans-serif;font-size:13px;cursor:pointer;transition:background 0.2s;"
                                onmouseover="this.style.background='var(--icon-hover-bg)'"
                                onmouseout="this.style.background='transparent'">
                                <span class="material-symbols-rounded"
                                    style="font-size:18px;color:var(--icon-color);">logout</span>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}"
                    style="padding:0.5rem 1.25rem;text-decoration:none;color:var(--icon-color);border-radius:4px;font-size:0.875rem;border:1px solid var(--icon-color);">Accedi</a>
            @endauth
        </div>
    </header>

    <!-- Chat Container -->
    <div class="chat-container" id="chatContainer">
        <div class="default-text" id="pantalla-inicio">
            <h1>Ciao, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
            <p>Sono il tuo assistente per la gestione documentale.</p>
            <p style="margin-bottom: 40px;">Come posso aiutarti oggi?</p>

            <div id="loading-actions" style="display:none; margin-top: 20px;">
                <div class="typing-animation">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
                <p style="font-size: 11px; color: var(--icon-color); margin-top: 5px; font-style: italic;">Caricamento
                    azioni...</p>
            </div>

            <!-- Quick actions -->
            <div class="quick-actions" id="quick-actions" style="display:none; width:100%; justify-content:center;">
                <div class="quick-card" style="--delay: 0.1s" onclick="window.abrirNuevoProyectoChat()">
                    <span class="material-symbols-rounded" style="color:#f59e0b;">folder</span>
                    <span class="quick-label">Progetto</span>
                </div>
                <div class="quick-card" style="--delay: 0.25s" onclick="window.abrirGestionCategoriasChat()">
                    <span class="material-symbols-rounded" style="color:#6366f1;">category</span>
                    <span class="quick-label">Categoria</span>
                </div>
                <div class="quick-card" style="--delay: 0.4s" onclick="window.abrirGestionTipologieChat()">
                    <span class="material-symbols-rounded" style="color:#10b981;">style</span>
                    <span class="quick-label">Tipologia</span>
                </div>
                <div class="quick-card" style="--delay: 0.55s" onclick="sendQuickMessage('Come funziona Docum24?')">
                    <span class="material-symbols-rounded" style="color:#ec4899;">help</span>
                    <span class="quick-label">Aiuto</span>
                </div>
            </div>

            <!-- Cards progetto -->
            <div id="cards-progetto"
                style="display:none; gap:16px; justify-content:center; flex-wrap:wrap; margin-top:24px; width:100%; max-width:600px;">

                <!-- Card 1: Ultimo progetto -->
                <div id="card-ultimo-progetto"
                    style="flex:1;min-width:220px;max-width:280px;opacity:0;transform:translateY(16px);transition:opacity 0.3s ease, transform 0.3s ease;">
                    <button onclick="aprireUltimoProgetto()"
                        style="width:100%;display:flex;align-items:center;gap:14px;padding:16px 20px;background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:16px;cursor:pointer;transition:all 0.2s;font-family:'Poppins',sans-serif;text-align:left;"
                        onmouseover="this.style.borderColor='#f59e0b';this.style.background='#f59e0b08';this.style.transform='translateY(-2px)'"
                        onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='var(--incoming-chat-bg)';this.style.transform='translateY(0)'">
                        <div
                            style="width:40px;height:40px;border-radius:12px;background:#f59e0b20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span class="material-symbols-rounded"
                                style="font-size:22px;color:#f59e0b;">folder_open</span>
                        </div>
                        <div style="min-width:0;">
                            <p style="margin:0;font-size:11px;color:var(--icon-color);font-weight:500;">Continua con
                            </p>
                            <p id="ultimo-progetto-nombre"
                                style="margin:0;font-size:13px;font-weight:700;color:var(--text-color);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;">
                            </p>
                        </div>
                        <span class="material-symbols-rounded"
                            style="font-size:18px;color:var(--icon-color);margin-left:auto;flex-shrink:0;">arrow_forward</span>
                    </button>
                </div>

                <!-- Card 2: Apri progetto -->
                <div style="flex:1;min-width:220px;max-width:280px;opacity:0;transform:translateY(16px);transition:opacity 0.3s ease 0.15s, transform 0.3s ease 0.15s;"
                    id="card-apri-progetto">
                    <button onclick="toggleNav()"
                        style="width:100%;display:flex;align-items:center;gap:14px;padding:16px 20px;background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:16px;cursor:pointer;transition:all 0.2s;font-family:'Poppins',sans-serif;text-align:left;"
                        onmouseover="this.style.borderColor='#6366f1';this.style.background='#6366f108';this.style.transform='translateY(-2px)'"
                        onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='var(--incoming-chat-bg)';this.style.transform='translateY(0)'">
                        <div
                            style="width:40px;height:40px;border-radius:12px;background:#6366f120;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span class="material-symbols-rounded" style="font-size:22px;color:#6366f1;">folder</span>
                        </div>
                        <div style="min-width:0;">
                            <p style="margin:0;font-size:11px;color:var(--icon-color);font-weight:500;">Scegli un
                                progetto</p>
                            <p style="margin:0;font-size:13px;font-weight:700;color:var(--text-color);">Apri Progetto
                            </p>
                        </div>
                        <span class="material-symbols-rounded"
                            style="font-size:18px;color:var(--icon-color);margin-left:auto;flex-shrink:0;">arrow_forward</span>
                    </button>
                </div>

            </div>
            <br>
            @if(!$tieneEmpresa)
            <div id="card-notifica-empresa"
                style="width:100%;max-width:580px;opacity:0;transform:translateY(16px);transition:opacity 0.3s ease 0.3s,transform 0.3s ease 0.3s;margin-top:0;">
                <button onclick="abrirModalEmpresa()"
                    style="width:100%;display:flex;align-items:center;gap:14px;padding:16px 20px;
                           background:var(--incoming-chat-bg);border:1px solid #f59e0b40;
                           border-radius:16px;cursor:pointer;transition:all 0.2s;font-family:'Poppins',sans-serif;text-align:left;"
                    onmouseover="this.style.borderColor='#f59e0b';this.style.background='#f59e0b08';this.style.transform='translateY(-2px)'"
                    onmouseout="this.style.borderColor='#f59e0b40';this.style.background='var(--incoming-chat-bg)';this.style.transform='translateY(0)'">
                    <div style="width:40px;height:40px;border-radius:12px;background:#f59e0b20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <span class="material-symbols-rounded" style="font-size:22px;color:#f59e0b;">business</span>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p style="margin:0;font-size:11px;color:#f59e0b;font-weight:600;">⚠ Azione richiesta</p>
                        <p id="notifica-empresa-texto" style="margin:2px 0 0;font-size:13px;font-weight:600;color:var(--text-color);"></p>
                    </div>
                    <span class="material-symbols-rounded" style="font-size:18px;color:var(--icon-color);flex-shrink:0;">arrow_forward</span>
                </button>
            </div>
            @endif

        </div>
    </div>


    <!-- Typing Container -->
    <div class="typing-container">
        <div class="typing-content">

            <!-- Panel upload (encima del input) -->
            <div id="upload-panel"
                style="display:none;position:fixed;bottom:90px;left:0;right:0;padding:0 10px;z-index:100;transition:padding-left 0.3s ease, padding-right 0.3s ease;">
                <div style="max-width:1000px;margin:0 auto;">
                    <div
                        style="background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:16px;padding:16px;box-shadow:0 -4px 20px rgba(0,0,0,0.3);">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span class="material-symbols-rounded"
                                    style="font-size:18px;color:#6366f1;">upload_file</span>
                                <span id="upload-panel-titulo"
                                    style="font-size:13px;font-weight:600;color:var(--text-color);">Caricamento
                                    file...</span>
                            </div>
                            <button onclick="chiudiUploadPanel()"
                                style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;transition:all 0.2s;"
                                onmouseover="this.style.color='#ef4444'"
                                onmouseout="this.style.color='var(--icon-color)'">
                                <span class="material-symbols-rounded" style="font-size:18px;">close</span>
                            </button>
                        </div>
                        <div id="upload-files-lista"
                            style="display:flex;flex-direction:column;gap:6px;max-height:220px;overflow-y:auto;"></div>
                    </div>
                </div>
            </div>

            <div class="typing-textarea">

                <!-- Botón + izquierda dentro del input -->
                <button id="btn-upload-plus" onclick="abrirSelectorFiles()" title="Carica documenti"
                    style="position:absolute;left:10px;bottom:60%;transform:translateY(60%);width:34px;height:34px;border-radius:8px;background:transparent;color:var(--icon-color);border:none;cursor:not-allowed;display:flex;align-items:center;justify-content:center;transition:all 0.2s;opacity:0.35;"
                    disabled>
                    <span class="material-symbols-rounded" style="font-size:20px;">add_2</span>
                </button>

                <!-- Btn DeepSearch -->
                <button id="btn-deep-search" onclick="toggleDeepSearch()"
                    title="DeepSearch — cerca nel web e nelle tue fonti"
                    style="position:absolute;right:55px;bottom:50%;transform:translateY(50%);
           width:34px;height:34px;border-radius:8px;background:transparent;
           color:var(--icon-color);border:none;cursor:pointer;
           display:flex;align-items:center;justify-content:center;
           transition:all 0.2s;opacity:0.5;">
                    <span class="material-symbols-rounded" style="font-size:20px;">travel_explore</span>
                </button>

                <input type="file" id="file-input-chat" multiple
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.txt" style="display:none;"
                    onchange="onFilesSelected(this)">

                <textarea id="chat-input" placeholder="Scrivi il tuo messaggio qui..." rows="1" style="padding-left:50px;"></textarea>
                <div id="send-btn"
                    style="position:absolute;right:10px;bottom:10px;width:40px;height:40px;display:flex;align-items:center;justify-content:center;color:var(--icon-color);cursor:pointer;border-radius:4px;transition:all 0.2s;">
                    <span class="material-symbols-rounded">send</span>
                </div>
            </div>
        </div>
        <div id="quick-actions-bar"
            style="display:flex; justify-content:center; width:100%; padding: 4px 10px 0; transition: all 0.3s ease;">
            <div
                style="display:flex; max-width:1000px; width:100%; justify-content: flex-start; gap:8px; flex-wrap: wrap;">

                <div style="position:relative;">
                    <button onclick="toggleDropdownCrea()"
                        style="display:flex; align-items:center; gap:6px; padding:6px 16px; border-radius:12px; border:1px solid var(--incoming-chat-border); background:var(--incoming-chat-bg); color:var(--icon-color); font-family:'Poppins',sans-serif; font-size:12px; cursor:pointer; transition:all 0.2s;"
                        onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1';this.style.background='rgba(99,102,241,0.05)'"
                        onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)';this.style.background='var(--incoming-chat-bg)'">
                        <span class="material-symbols-rounded" style="font-size:18px;">add</span>
                        Crea
                    </button>

                    <div id="dropdown-crea"
                        style="display:none;position:absolute;bottom:calc(100% + 8px);left:0;background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:12px;min-width:180px;box-shadow:0 8px 24px rgba(0,0,0,0.2);z-index:999;overflow:hidden;">
                        <button onclick="toggleDropdownCrea(); accionProyecto('crea')"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 14px;color:var(--text-color);background:none;border:none;font-family:'Poppins',sans-serif;font-size:13px;cursor:pointer;transition:background 0.2s;"
                            onmouseover="this.style.background='var(--icon-hover-bg)'"
                            onmouseout="this.style.background='transparent'">
                            <span class="material-symbols-rounded" style="font-size:18px;color:#f59e0b;">folder</span>
                            Progetto
                        </button>
                        <button onclick="toggleDropdownCrea(); accionCategoria('crea')"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 14px;color:var(--text-color);background:none;border:none;font-family:'Poppins',sans-serif;font-size:13px;cursor:pointer;transition:background 0.2s;"
                            onmouseover="this.style.background='var(--icon-hover-bg)'"
                            onmouseout="this.style.background='transparent'">
                            <span class="material-symbols-rounded"
                                style="font-size:18px;color:#6366f1;">category</span>
                            Categoria
                        </button>
                        <button onclick="toggleDropdownCrea(); accionTipologia('crea')"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 14px;color:var(--text-color);background:none;border:none;font-family:'Poppins',sans-serif;font-size:13px;cursor:pointer;transition:background 0.2s;"
                            onmouseover="this.style.background='var(--icon-hover-bg)'"
                            onmouseout="this.style.background='transparent'">
                            <span class="material-symbols-rounded" style="font-size:18px;color:#10b981;">style</span>
                            Tipologia
                        </button>
                    </div>
                </div>

                <button onclick="const t=body.classList.contains('light-mode')?'dark':'light';setTheme(t);"
                    style="display:flex; align-items:center; gap:6px; padding:6px 16px; border-radius:12px; border:1px solid var(--incoming-chat-border); background:var(--incoming-chat-bg); color:var(--icon-color); font-family:'Poppins',sans-serif; font-size:12px; cursor:pointer; transition:all 0.2s;"
                    onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1';this.style.background='rgba(99,102,241,0.05)'"
                    onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)';this.style.background='var(--incoming-chat-bg)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">contrast</span>
                    Tema
                </button>

                <button onclick="apriCronologiaChat()"
                    style="display:flex; align-items:center; gap:6px; padding:6px 16px; border-radius:12px; border:1px solid var(--incoming-chat-border); background:var(--incoming-chat-bg); color:var(--icon-color); font-family:'Poppins',sans-serif; font-size:12px; cursor:pointer; transition:all 0.2s;"
                    onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1';this.style.background='rgba(99,102,241,0.05)'"
                    onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)';this.style.background='var(--incoming-chat-bg)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">forum</span>
                    Conversazioni
                </button>

                <button onclick="avviaWizardReport()"
                    style="display:flex; align-items:center; gap:6px; padding:6px 16px; border-radius:12px; border:1px solid var(--incoming-chat-border); background:var(--incoming-chat-bg); color:var(--icon-color); font-family:'Poppins',sans-serif; font-size:12px; cursor:pointer; transition:all 0.2s;"
                    onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1';this.style.background='rgba(99,102,241,0.05)'"
                    onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)';this.style.background='var(--incoming-chat-bg)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">description</span>
                    Report
                </button>

                <button onclick="abrirCronologia();"
                    style="display:flex; align-items:center; gap:6px; padding:6px 16px; border-radius:12px; border:1px solid var(--incoming-chat-border); background:var(--incoming-chat-bg); color:var(--icon-color); font-family:'Poppins',sans-serif; font-size:12px; cursor:pointer; transition:all 0.2s;"
                    onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1';this.style.background='rgba(99,102,241,0.05)'"
                    onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)';this.style.background='var(--incoming-chat-bg)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">history</span>
                    Cronologia
                </button>

                @php
                    $esPro = auth()->user()->suscripcionActiva?->plan?->nombre === 'Pro';
                @endphp

                @if($esPro)
                <!-- Team Selector -->
                <div style="position:relative;" id="team-selector-wrap">
                    <button onclick="toggleTeamSelector()"
                        style="display:flex;align-items:center;gap:6px;background:var(--incoming-chat-bg);
                        border:1px solid var(--incoming-chat-border);color:var(--text-color);
                        padding:6px 12px;border-radius:8px;cursor:pointer;font-family:'Poppins',sans-serif;
                        font-size:12px;transition:all 0.2s;max-width:160px;"
                        onmouseover="this.style.background='var(--icon-hover-bg)'"
                        onmouseout="this.style.background='var(--incoming-chat-bg)'">
                        <span class="material-symbols-rounded"
                            style="font-size:16px;color:#6366f1;flex-shrink:0;">group</span>
                        <span id="team-selector-label"
                            style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            Personale
                        </span>
                        <span class="material-symbols-rounded"
                            style="font-size:14px;color:var(--icon-color);flex-shrink:0;">expand_more</span>
                    </button>

                    <div id="team-selector-dropdown"
                        style="display:none;position:absolute;bottom:calc(100% + 8px);left:0;
                        background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);
                        border-radius:12px;min-width:200px;box-shadow:0 8px 24px rgba(0,0,0,0.3);
                        z-index:999;overflow:hidden;">
                    </div>
                </div>
                @endif

                <button onclick="abrirImpostazioni();"
                    style="display:flex; align-items:center; gap:6px; padding:6px 16px; border-radius:12px; border:1px solid var(--incoming-chat-border); background:var(--incoming-chat-bg); color:var(--icon-color); font-family:'Poppins',sans-serif; font-size:12px; cursor:pointer; transition:all 0.2s;"
                    onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1';this.style.background='rgba(99,102,241,0.05)'"
                    onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)';this.style.background='var(--incoming-chat-bg)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">settings</span>
                    Impostazioni
                </button>

                <button onclick="abrirNotifica();"
                    style="display:flex;align-items:center;gap:6px;padding:6px 16px;border-radius:12px;border:1px solid var(--incoming-chat-border);background:var(--incoming-chat-bg);color:var(--icon-color);font-family:'Poppins',sans-serif;font-size:12px;cursor:pointer;transition:all 0.2s;position:relative;"
                    onmouseover="this.style.borderColor='#f59e0b';this.style.color='#f59e0b';this.style.background='rgba(245,158,11,0.05)'"
                    onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)';this.style.background='var(--incoming-chat-bg)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">event</span>
                    Scadenze
                    <span id="notifica-badge"
                        style="display:none;position:absolute;top:-6px;right:-6px;
                            background:#f59e0b;color:#fff;border-radius:50%;
                            width:18px;height:18px;font-size:10px;font-weight:700;
                            display:flex;align-items:center;justify-content:center;
                            border:2px solid var(--outgoing-chat-bg);">0</span>
                </button>

                <button onclick="sendQuickMessage('Come funziona Docum24?')"
                    style="display:flex; align-items:center; gap:6px; padding:6px 16px; border-radius:12px; border:1px solid var(--incoming-chat-border); background:var(--incoming-chat-bg); color:var(--icon-color); font-family:'Poppins',sans-serif; font-size:12px; cursor:pointer; transition:all 0.2s;"
                    onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1';this.style.background='rgba(99,102,241,0.05)'"
                    onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)';this.style.background='var(--incoming-chat-bg)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">help</span>
                    Aiuto
                </button>

            </div>
        </div>
    </div>


    <script>
        // Verificar si hay plan pendiente de pago
        @if (session('pending_plan'))
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(() => iniziareCheckout('{{ session('pending_plan') }}'), 1000);
                @php session()->forget('pending_plan'); @endphp
            });
        @endif

        const chatInput = document.getElementById('chat-input');
        const sendBtn = document.getElementById('send-btn');
        const chatContainer = document.getElementById('chatContainer');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;

        let esNuevoUsuario = {{ auth()->user()->business_type ? 'false' : 'true' }};
        let currentAbortController = null;

        // Y sincronizar localStorage con la BD
        if (!esNuevoUsuario) {
            localStorage.setItem('onboarding_completado', '1');
        }

        let sessionId = localStorage.getItem('chat_session_id') || null;
        if (!sessionId) {
            sessionId = generateUUID();
            localStorage.setItem('chat_session_id', sessionId);
        }

        function generateUUID() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                const r = Math.random() * 16 | 0;
                const v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        function startNewConversation() {
            Swal.fire({
                title: 'Nuova conversazione',
                text: 'Tutti i messaggi correnti verranno cancellati.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6366f1',
                cancelButtonColor: 'transparent',
                confirmButtonText: 'Sì, ricomincia',
                cancelButtonText: 'Annulla',
                background: 'var(--incoming-chat-bg)',
                color: 'var(--text-color)',
                customClass: {
                    cancelButton: 'swal-cancel-custom'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Reestablecemos el HTML inicial
                    chatContainer.innerHTML = `
    <div class="default-text" id="pantalla-inicio">
        <h1>Ciao, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
        <p>Sono il tuo assistente per la gestione documentale.</p>
        <p style="margin-bottom: 40px;">Come posso aiutarti oggi?</p>

        <div class="quick-actions" id="quick-actions" style="display:none; width:100%; justify-content:center;">
            <div class="quick-card" style="--delay: 0.1s" onclick="window.abrirNuevoProyectoChat()">
                <span class="material-symbols-rounded" style="color:#f59e0b;">folder</span>
                <span class="quick-label">Progetto</span>
            </div>
            <div class="quick-card" style="--delay: 0.25s" onclick="window.abrirGestionCategoriasChat()">
                <span class="material-symbols-rounded" style="color:#6366f1;">category</span>
                <span class="quick-label">Categoria</span>
            </div>
            <div class="quick-card" style="--delay: 0.4s" onclick="window.abrirGestionTipologieChat()">
                <span class="material-symbols-rounded" style="color:#10b981;">style</span>
                <span class="quick-label">Tipologia</span>
            </div>
            <div class="quick-card" style="--delay: 0.55s" onclick="sendQuickMessage('Come funziona Docum24?')">
                <span class="material-symbols-rounded" style="color:#ec4899;">help</span>
                <span class="quick-label">Aiuto</span>
            </div>
        </div>

        <div id="cards-progetto" style="display:none; gap:16px; justify-content:center; flex-wrap:wrap; margin-top:24px; width:100%; max-width:600px;">

            <div id="card-ultimo-progetto" style="flex:1; min-width:220px; max-width:280px; opacity:0; transform:translateY(16px);">
                <button onclick="aprireUltimoProgetto()" style="width:100%;display:flex;align-items:center;gap:14px;padding:16px 20px;background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:16px;cursor:pointer;text-align:left;">
                    <div style="width:40px;height:40px;border-radius:12px;background:#f59e0b20;display:flex;align-items:center;justify-content:center;">
                        <span class="material-symbols-rounded" style="font-size:22px;color:#f59e0b;">folder_open</span>
                    </div>
                    <div style="min-width:0;">
                        <p style="margin:0;font-size:11px;color:var(--icon-color);font-weight:500;">Continua con</p>
                        <p id="ultimo-progetto-nombre" style="margin:0;font-size:13px;font-weight:700;color:var(--text-color);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;"></p>
                    </div>
                    <span class="material-symbols-rounded" style="font-size:18px;color:var(--icon-color);margin-left:auto;">arrow_forward</span>
                </button>
            </div>

            <div id="card-apri-progetto" style="flex:1; min-width:220px; max-width:280px; opacity:0; transform:translateY(16px);">
                <button onclick="toggleNav()" style="width:100%;display:flex;align-items:center;gap:14px;padding:16px 20px;background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:16px;cursor:pointer;text-align:left;">
                    <div style="width:40px;height:40px;border-radius:12px;background:#6366f120;display:flex;align-items:center;justify-content:center;">
                        <span class="material-symbols-rounded" style="font-size:22px;color:#6366f1;">folder</span>
                    </div>
                    <div>
                        <p style="margin:0;font-size:11px;color:var(--icon-color);font-weight:500;">Scegli un progetto</p>
                        <p style="margin:0;font-size:13px;font-weight:700;color:var(--text-color);">Apri Progetto</p>
                    </div>
                    <span class="material-symbols-rounded" style="font-size:18px;color:var(--icon-color);margin-left:auto;">arrow_forward</span>
                </button>
            </div>
        </div>
    </div>`;
                    // Resetear ID de sesión
                    sessionId = generateUUID();
                    localStorage.setItem('chat_session_id', sessionId);

                    // IMPORTANTE: Reiniciar la variable de control de datos
                    proyectosCargados = null;

                    // RE-EJECUTAR LA LÓGICA DE BIENVENIDA
                    iniciarBienvenida();
                }
            });
        }

        function setTheme(theme) {
            const toggle = document.getElementById('themeToggle');
            const dot = toggle?.querySelector('span');
            if (theme === 'light') {
                body.classList.add('light-mode');
                if (dot) dot.style.transform = 'translateX(16px)'; // Mueve el círculo
                if (toggle) toggle.style.background = '#F53003'; // Rojo landing cuando está activo
            } else {
                body.classList.remove('light-mode');
                if (dot) dot.style.transform = 'translateX(0)'; // Vuelve al inicio
                if (toggle) toggle.style.background = '#1b1b18'; // Negro landing en modo oscuro
            }
            localStorage.setItem('theme', theme);
        }

        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme);

        themeToggle.addEventListener('click', () => {
            const newTheme = body.classList.contains('light-mode') ? 'dark' : 'light';
            setTheme(newTheme);
        });

        chatInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        chatInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        sendBtn.addEventListener('click', sendMessage);

        function createChatElement(content, type) {
            const chatDiv = document.createElement('div');
            chatDiv.className = `chat ${type}`;
            chatDiv.innerHTML = content;
            return chatDiv;
        }

        function addUserMessage(text) {
            const html = `
                <div class="chat-content">
                    <div class="chat-icon"><span class="material-symbols-rounded">person</span></div>
                    <div class="chat-text">${text}</div>
                </div>`;
            chatContainer.appendChild(createChatElement(html, 'outgoing'));
            scrollToBottom();
        }

        function addTypingAnimation() {
            const html = `
                <div class="chat-content">
                    <div class="chat-icon"><span class="material-symbols-rounded">robot_2</span></div>
                    <div class="chat-text">
                        <div class="typing-animation">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                        <div id="loading-message" style="font-size:0.85rem;color:var(--icon-color);margin-top:8px;font-style:italic;opacity:0.7;transition:opacity 0.3s ease;"></div>
                    </div>
                </div>`;
            const chatDiv = createChatElement(html, 'incoming');
            chatDiv.id = 'typing-animation';
            chatContainer.appendChild(chatDiv);
            scrollToBottom();
            startLoadingMessages();
        }

        let loadingMessageInterval = null;

        function startLoadingMessages() {
            const messages = [
                "Un momento, sto analizzando...",
                "Elaboro la tua richiesta...",
                "Analizzo i documenti...",
                "Quasi pronto...",
                "Sto preparando la risposta...",
                "Elaborazione in corso..."
            ];

            let currentIndex = 0;
            const messageElement = document.getElementById('loading-message');
            if (!messageElement) return;

            messageElement.textContent = messages[0];
            messageElement.style.opacity = '0.7';

            loadingMessageInterval = setInterval(() => {
                currentIndex = (currentIndex + 1) % messages.length;
                messageElement.style.opacity = '0';
                setTimeout(() => {
                    messageElement.textContent = messages[currentIndex];
                    messageElement.style.opacity = '0.7';
                }, 300);
            }, 5000);
        }

        function stopLoadingMessages() {
            if (loadingMessageInterval) {
                clearInterval(loadingMessageInterval);
                loadingMessageInterval = null;
            }
        }

        function removeTypingAnimation() {
            stopLoadingMessages();
            const typing = document.getElementById('typing-animation');
            if (typing) typing.remove();
        }

        function formatMarkdown(text) {
            if (/<div|<img|<button|<span/.test(text)) return text;

            marked.setOptions({
                breaks: true,
                gfm: true,
            });

            return marked.parse(text);
        }

        async function typeWriter(element, text, speed = 8) {
            const formattedHTML = formatMarkdown(text);
            element.innerHTML = formattedHTML;

            const allListItems = element.querySelectorAll('li');
            allListItems.forEach(li => li.classList.add('writing'));

            const walker = document.createTreeWalker(element, NodeFilter.SHOW_TEXT, null, false);
            const textNodes = [];
            let node;
            while (node = walker.nextNode()) {
                if (node.textContent.trim()) textNodes.push(node);
            }

            const cursor = document.createElement('span');
            cursor.className = 'ai-cursor';
            cursor.style.cssText =
                'display:inline-block;width:2px;height:1em;background:var(--text-color);margin-left:2px;animation:blink 0.7s infinite;';

            textNodes.forEach(node => {
                node.originalText = node.textContent;
                node.textContent = '';
            });

            for (let nodeIndex = 0; nodeIndex < textNodes.length; nodeIndex++) {
                const currentNode = textNodes[nodeIndex];
                const text = currentNode.originalText;

                let parentLi = currentNode.parentElement;
                while (parentLi && parentLi.tagName !== 'LI') parentLi = parentLi.parentElement;

                for (let i = 0; i < text.length; i++) {
                    currentNode.textContent += text[i];
                    if (i === 0 && parentLi) parentLi.classList.remove('writing');
                    if (cursor.parentNode) cursor.remove();
                    element.appendChild(cursor);
                    await new Promise(resolve => setTimeout(resolve, Math.random() * speed + 2));
                    if ('.!?:'.includes(text[i])) await new Promise(resolve => setTimeout(resolve, 100));
                    scrollToBottom();
                }
            }
            if (cursor.parentNode) cursor.parentNode.removeChild(cursor);
            document.querySelectorAll('.ai-cursor').forEach(c => c.remove());
            allListItems.forEach(li => li.classList.remove('writing'));
        }

        async function addBotMessage(text) {
            const html = `
                <div class="chat-content">
                    <div class="chat-icon"><span class="material-symbols-rounded">robot_2</span></div>
                    <div class="chat-text" id="bot-text"></div>
                    <button onclick="copiarRisposta(this)" title="Copia risposta"
                        style="align-self:flex-end;flex-shrink:0;border:none;background:transparent;
                            cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;
                            display:flex;opacity:0.4;transition:all 0.2s;"
                        onmouseover="this.style.opacity='1';this.style.background='var(--icon-hover-bg)'"
                        onmouseout="this.style.opacity='0.4';this.style.background='transparent'">
                        <span class="material-symbols-rounded" style="font-size:16px;">content_copy</span>
                    </button>
                </div>`;
            const chatDiv = createChatElement(html, 'incoming');
            chatContainer.appendChild(chatDiv);

            const container = document.getElementById('bot-text');
            const tmp = document.createElement('div');
            tmp.innerHTML = text;

            let cards = Array.from(tmp.children);
            if (cards.length === 1) {
                const root = cards[0];
                const innerCandidates = root.querySelectorAll('.card, article, section');
                if (innerCandidates.length > 1) cards = Array.from(innerCandidates);
            }

            if (cards.length <= 1) {
                // Si contiene tabla o botones, mostrar directo sin typewriter
                if (/\|.+\|/.test(text) || /<button|<div/.test(text)) {
                    container.style.opacity = '0';
                    container.style.transition = 'opacity 0.4s ease';
                    container.innerHTML = formatMarkdown(text);
                    container.removeAttribute('id');
                    setTimeout(() => {
                        container.style.opacity = '1';
                        scrollToBottom();
                    }, 30);
                    setTimeout(() => scrollToBottom(), 200);
                    return;
                }
                await typeWriter(container, text);
                container.removeAttribute('id');
                return;
            }

            for (const originalCard of cards) {
                const card = originalCard.cloneNode(true);
                card.querySelectorAll('img, button, .btn, a.btn, a.button').forEach(el => el.classList.add(
                    'deferred-reveal'));

                let typingZone = card.querySelector('[data-typing], .details, .info, .text, .card-body, .content, p') ||
                    card;
                const originalHTML = typingZone.innerHTML;
                typingZone.innerHTML = '';

                container.appendChild(card);
                scrollToBottom();

                await typeWriter(typingZone, originalHTML);
                card.querySelectorAll('.deferred-reveal').forEach(el => el.classList.remove('deferred-reveal'));
                await new Promise(r => setTimeout(r, 120));
            }
            container.removeAttribute('id');
        }

        function scrollToBottom() {
            // Usamos requestAnimationFrame para esperar a que el navegador renderice la nueva burbuja
            requestAnimationFrame(() => {
                chatContainer.scrollTo({
                    top: chatContainer.scrollHeight,
                    behavior: 'smooth'
                });
            });
        }

        async function sendMessage() {
            const message = chatInput.value.trim();

            // Si está en modo chat documentos, usar endpoint diferente
            if (window._modoChatDoc && window._chatDocIds?.length > 0) {
                await sendMessageDocMode(message);
                return;
            }
            if (!message) return;

            const defaultText = chatContainer.querySelector('.default-text');
            if (defaultText) defaultText.remove();

            chatInput.disabled = true;
            sendBtn.classList.add('disabled');
            addUserMessage(message);
            chatInput.value = '';
            chatInput.style.height = 'auto';
            addTypingAnimation();

            // Crear controller ANTES de usarlo
            currentAbortController = new AbortController();
            const signal = currentAbortController.signal;

            // Cambiar btn a cancel
            sendBtn.querySelector('span').textContent = 'cancel';
            sendBtn.querySelector('span').style.color = '#ef4444';
            sendBtn.removeEventListener('click', sendMessage);
            sendBtn.addEventListener('click', cancelarEnvio);

            try {
                const response = await fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        message: message,
                        session_id: sessionId,
                        es_nuevo: esNuevoUsuario,
                        deep_search: _deepSearchActivo
                    }),
                    signal: signal
                });

                const data = await response.json();
                removeTypingAnimation();
                console.log('ACTION:', data.action, 'PARAMS:', JSON.stringify(data.params));

                if (data.success) {
                    if (data.session_id) {
                        sessionId = data.session_id;
                        localStorage.setItem('chat_session_id', sessionId);
                    }
                    await addBotMessage(data.response);

                    console.log('DeepSearch data:', data.referencias, data.response);

                    // DeepSearch referencias
                    if (_deepSearchActivo && data.referencias && data.referencias.length > 0) {
                        // Badge en el último mensaje
                        const lastMsg = chatContainer.lastElementChild;
                        const chatText = lastMsg?.querySelector('.chat-text');
                        if (chatText) {
                            const badge = document.createElement('div');
                            badge.style.cssText =
                                'display:flex;align-items:center;gap:6px;margin-top:8px;padding:6px 10px;background:#6366f115;border:1px solid #6366f130;border-radius:8px;cursor:pointer;width:fit-content;';
                            badge.innerHTML =
                                `<span class="material-symbols-rounded" style="font-size:14px;color:#6366f1;">travel_explore</span><span style="font-size:11px;color:#6366f1;font-weight:600;">DeepSearch · ${data.referencias.length} font${data.referencias.length > 1 ? 'i' : 'e'}</span>`;
                            badge.onclick = () => abrirPanelReferencias(data.referencias);
                            chatText.appendChild(badge);
                        }
                        // Abrir panel automáticamente
                        abrirPanelReferencias(data.referencias);
                    }

                    // Ejecutar acción si viene
                    if (data.action && data.action !== 'nessuna') {
                        setTimeout(() => eseguireAzione(data.action, data.params), 600);
                    }
                    // Actualizar widget tokens
                    window.actualizarTokenWidget && window.actualizarTokenWidget();
                } else {
                    await addBotMessage('Errore: ' + (data.error || 'Non è stato possibile ottenere una risposta'));
                }
            } catch (error) {
                removeTypingAnimation();
                if (error.name === 'AbortError') {
                    agregarRespuestaBot('⚠️ Richiesta annullata.');
                } else {
                    await addBotMessage('Errore di connessione. Riprova.');
                    console.error('Error:', error);
                }
            }

            chatInput.disabled = false;
            sendBtn.classList.remove('disabled');
            chatInput.focus();
            resetSendBtn();
        }

        let proyectosCargados = null;

        function iniciarBienvenida() {
            const box = document.getElementById('pantalla-inicio');
            if (!box) return;

            // Reiniciamos el estado de carga de proyectos cada vez que se llama
            proyectosCargados = null;

            fetch('/proyectos')
                .then(r => r.json())
                .then(data => {
                    proyectosCargados = data;
                })
                .catch(() => {
                    proyectosCargados = [];
                });

            // 2. SELECTOR RESTRINGIDO: Solo los hijos directos de pantalla-inicio
            // Esto evita que entre a buscar etiquetas <p> dentro de las cards
            const elementosMensaje = Array.from(box.querySelectorAll(':scope > h1, :scope > p'));

            const textos = elementosMensaje.map(el => ({
                el: el,
                txt: el.textContent.trim()
            }));

            // Limpiar solo los textos de bienvenida
            textos.forEach(item => {
                item.el.textContent = "";
            });

            // 3. Typewriter ultra rápido solo para el mensaje
            let i = 0;

            function escribirLinea() {
                if (i >= textos.length) {
                    finalizarYMostrarTodo();
                    return;
                }

                const current = textos[i];
                let charIdx = 0;

                // Si la línea está vacía, saltar
                if (!current.txt) {
                    i++;
                    escribirLinea();
                    return;
                }

                const interval = setInterval(() => {
                    current.el.textContent += current.txt[charIdx];
                    charIdx++;
                    if (charIdx >= current.txt.length) {
                        clearInterval(interval);
                        i++;
                        setTimeout(escribirLinea, 40);
                    }
                }, 8); // Velocidad agresiva
            }

            escribirLinea();
        }

        function finalizarYMostrarTodo() {
            const quickActions = document.getElementById('quick-actions');
            const cardsProgetto = document.getElementById('cards-progetto');

            // Esperar a que los datos estén listos
            const checkData = setInterval(() => {
                if (proyectosCargados !== null) {
                    clearInterval(checkData);

                    if (proyectosCargados.length === 0) {
                        iniciarOnboardingDiretto();
                    } else {
                        // Mostrar contenedores
                        if (quickActions) {
                            quickActions.style.display = 'flex';
                            quickActions.style.opacity = '1';
                        }
                        if (cardsProgetto) {
                            cardsProgetto.style.display = 'flex';
                            cardsProgetto.style.opacity = '1';
                            initCardsProgetto();

                            const cardNotifica = document.getElementById('card-notifica-empresa');
                            if (cardNotifica) {
                                cardNotifica.style.opacity = '1';
                                cardNotifica.style.transform = 'translateY(0)';
                            }
                        }
                    }
                }
            }, 50);
        }

        document.addEventListener('DOMContentLoaded', iniciarBienvenida);

        async function eseguireAzione(action, params) {
            const defaultText = chatContainer.querySelector('.default-text');
            if (defaultText) defaultText.remove();

            switch (action) {
                case 'crea_progetto':
                    window.abrirNuevoProyectoChat && accionProyecto('crea');
                    break;
                case 'gestisci_progetti':
                    window.abrirNuevoProyectoChat && window.abrirNuevoProyectoChat();
                    break;
                case 'crea_categoria':
                    window.abrirGestionCategoriasChat && accionCategoria('crea');
                    break;
                case 'gestisci_categorie':
                    window.abrirGestionCategoriasChat && window.abrirGestionCategoriasChat();
                    break;
                case 'crea_tipologia':
                    window.abrirGestionTipologieChat && accionTipologia('crea');
                    break;
                case 'gestisci_tipologie':
                    window.abrirGestionTipologieChat && window.abrirGestionTipologieChat();
                    break;
                case 'apri_progetto':
                    // Busca el proyecto por nombre y lo abre
                    if (params?.nome) aprireProgettoPorNome(params.nome);
                    break;
                case 'apri_supporto': {
                    toggleSupportChat();
                    setTimeout(() => {
                        const domanda = params?.domanda || '';
                        if (domanda) {
                            document.getElementById('support-input').value = domanda;
                            sendSupportMessage();
                        }
                    }, 600);
                    break;
                }
                case 'suggerisci_categoria':
                    setTimeout(() => {
                        agregarRespuestaBot(`
                        <button onclick="accionCategoria('crea')"
                            style="background:#6366f120;color:#6366f1;border:1px solid #6366f140;border-radius:8px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;"
                            onmouseover="this.style.background='#6366f1';this.style.color='#fff'"
                            onmouseout="this.style.background='#6366f120';this.style.color='#6366f1'">
                            + Crea Categoria
                        </button>`);
                    }, 600);
                    break;

                case 'suggerisci_tipologia':
                    setTimeout(() => {
                        agregarRespuestaBot(`
                        <button onclick="accionTipologia('crea')"
                            style="background:#10b98120;color:#10b981;border:1px solid #10b98140;border-radius:8px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;"
                            onmouseover="this.style.background='#10b981';this.style.color='#fff'"
                            onmouseout="this.style.background='#10b98120';this.style.color='#10b981'">
                            + Crea Tipologia
                        </button>`);
                    }, 600);
                    break;

                case 'suggerisci_progetto':
                    setTimeout(() => {
                        agregarRespuestaBot(`
                        <button onclick="accionProyecto('crea')"
                            style="background:#f59e0b20;color:#f59e0b;border:1px solid #f59e0b40;border-radius:8px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;"
                            onmouseover="this.style.background='#f59e0b';this.style.color='#fff'"
                            onmouseout="this.style.background='#f59e0b20';this.style.color='#f59e0b'">
                            + Crea Progetto
                        </button>`);
                    }, 600);
                    break;

                case 'onboarding_start':
                    // Solo el mensaje ya fue mostrado, nada más que hacer
                    break;

                case 'onboarding_nome_progetto': {
                    setTimeout(() => {
                        const nomi = params.nomi || [];
                        let btns = nomi.map(nome => `
            <button onclick="seleccionarNombreOnboarding('${nome.replace(/'/g, "\\'")}')"
                style="background:var(--incoming-chat-bg);color:var(--text-color);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:8px 14px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:all 0.2s;display:flex;align-items:center;gap:8px;width:100%;text-align:left;"
                onmouseover="this.style.borderColor='#f59e0b';this.style.background='#f59e0b08'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='var(--incoming-chat-bg)'">
                <span class="material-symbols-rounded" style="font-size:16px;color:#f59e0b;">folder</span>
                ${nome}
            </button>`).join('');

                        agregarRespuestaBot(`
            <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px;">
                ${btns}
                <div style="margin-top:4px;border-top:1px solid var(--incoming-chat-border);padding-top:10px;">
                    <p style="margin:0 0 6px;font-size:11px;color:var(--icon-color);">Oppure scrivi il tuo nome:</p>
                    <div style="display:flex;gap:8px;">
                        <input id="onb-custom-nombre" type="text" placeholder="Es. Studio Dentistico Rossi..."
                            style="flex:1;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:8px 12px;font-size:12px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='#f59e0b'"
                            onblur="this.style.borderColor='var(--incoming-chat-border)'"
                            onkeydown="if(event.key==='Enter'){const v=this.value.trim();if(v)seleccionarNombreOnboarding(v);}">
                        <button onclick="const v=document.getElementById('onb-custom-nombre').value.trim();if(v)seleccionarNombreOnboarding(v);"
                            style="background:#f59e0b;color:#fff;border:none;border-radius:8px;padding:8px 14px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;white-space:nowrap;transition:background 0.2s;"
                            onmouseover="this.style.background='#d97706'"
                            onmouseout="this.style.background='#f59e0b'">
                            Usa questo →
                        </button>
                    </div>
                </div>
            </div>`);
                    }, 400);
                    break;
                }

                case 'onboarding_crea_progetto':
                    setTimeout(async () => {
                        const res = await fetch('/proyectos', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                nombre: params.nome,
                                descripcion: ''
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            window._onboardingProyectoId = data.proyecto.id;
                            window._onboardingProyectoNombre = data.proyecto.nombre;
                            localStorage.setItem('onboarding_proyecto', JSON.stringify({
                                id: data.proyecto.id,
                                nombre: data.proyecto.nombre
                            }));
                            if (typeof window.agregarProyectoNavLateral === 'function') {
                                window.agregarProyectoNavLateral(data.proyecto);
                            }

                            // ── Avanzar automáticamente al PASSO 3 ──────────────
                            setTimeout(() => {
                                sendQuickMessage(
                                    `Progetto "${data.proyecto.nombre}" creato. Ora suggerisci le categorie.`
                                );
                            }, 600);
                            // ────────────────────────────────────────────────────
                        }
                    }, 300);
                    break;

                case 'onboarding_suggerisci_categorie':
                    // Mostrar categorías como checkboxes
                    setTimeout(() => {
                        const cats = params.categorie || [];
                        let checkboxes = cats.map((cat, i) => `
                            <label style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:8px;border:1px solid var(--incoming-chat-border);cursor:pointer;transition:all 0.2s;font-size:13px;color:var(--text-color);"
                                onmouseover="this.style.borderColor='#6366f1';this.style.background='#6366f108'"
                                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='transparent'">
                                <input type="checkbox" id="onb-cat-${i}" value="${cat}" checked
                                    style="width:16px;height:16px;accent-color:#6366f1;cursor:pointer;">
                                ${cat}
                            </label>`).join('');

                        agregarRespuestaBot(`
                            <div style="display:flex;flex-direction:column;gap:6px;margin-top:8px;">
                                ${checkboxes}
                                <button onclick="confirmarCategoriasOnboarding()"
                                    style="margin-top:8px;background:#6366f1;color:#fff;border:none;border-radius:8px;padding:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:background 0.2s;"
                                    onmouseover="this.style.background='#4f46e5'"
                                    onmouseout="this.style.background='#6366f1'">
                                    Crea queste categorie →
                                </button>
                            </div>`);
                    }, 400);
                    break;

                case 'onboarding_crea_tutto': {
                    const categorie = params.categorie || [];
                    const tipologie = params.tipologie_per_categoria || {};

                    // Recuperar proyecto de localStorage si no está en memoria
                    if (!window._onboardingProyectoId) {
                        const saved = localStorage.getItem('onboarding_proyecto');
                        if (saved) {
                            try {
                                const d = JSON.parse(saved);
                                window._onboardingProyectoId = d.id;
                                window._onboardingProyectoNombre = d.nombre;
                            } catch (e) {}
                        }
                    }

                    let pid = window._onboardingProyectoId;

                    // Si todavía no hay proyecto, crearlo ahora con el nombre de params
                    const crearYProceder = async () => {
                        if (!pid && params.nome_progetto) {
                            agregarRespuestaBot(`⏳ Creo il progetto <strong>${params.nome_progetto}</strong>...`);
                            const resProy = await fetch('/proyectos', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    nombre: params.nome_progetto,
                                    descripcion: ''
                                })
                            });
                            const dataProy = await resProy.json();
                            if (dataProy.success) {
                                pid = dataProy.proyecto.id;
                                window._onboardingProyectoId = pid;
                                window._onboardingProyectoNombre = dataProy.proyecto.nombre;
                                localStorage.setItem('onboarding_proyecto', JSON.stringify({
                                    id: pid,
                                    nombre: dataProy.proyecto.nombre
                                }));
                                if (typeof window.agregarProyectoNavLateral === 'function') {
                                    window.agregarProyectoNavLateral(dataProy.proyecto);
                                }
                            }
                        }

                        if (!pid) {
                            agregarRespuestaBot('⚠️ Progetto non trovato. Scrivi il nome del progetto e riprova.');
                            return;
                        }

                        agregarRespuestaBot(`⏳ Creo ${categorie.length} categorie...`);
                        let creati = 0;

                        for (const cat of categorie) {
                            const resCat = await fetch('/categorias', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    nombre: cat,
                                    proyecto_id: pid
                                })
                            });
                            const dataCat = await resCat.json();

                            if (dataCat.success) {
                                creati++;
                                if (tipologie[cat]) {
                                    for (const tip of tipologie[cat]) {
                                        await fetch('/tipologias', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': csrfToken
                                            },
                                            body: JSON.stringify({
                                                nombre: tip,
                                                categoria_id: dataCat.categoria.id
                                            })
                                        });
                                    }
                                }
                            }
                        }

                        agregarRespuestaBot(`✅ ${creati} categorie create! Il tuo progetto è pronto 🎉`);

                        // Abrir DataRoom
                        // Abrir DataRoom y luego guiar
                        setTimeout(() => {
                            if (window._onboardingProyectoId && typeof window.seleccionarProyectoChat ===
                                'function') {
                                window.seleccionarProyectoChat(window._onboardingProyectoId, window
                                    ._onboardingProyectoNombre);
                            }
                            localStorage.removeItem('onboarding_proyecto');
                            localStorage.setItem('onboarding_completado', '1');

                            // Guía para subir documentos
                            setTimeout(() => {
                                agregarRespuestaBot(`
                                <div style="display:flex;flex-direction:column;gap:10px;">
                                    <p style="margin:0;font-size:13px;font-weight:600;color:var(--text-color);">🎉 Il tuo progetto è pronto!</p>
                                    <p style="margin:0;font-size:13px;color:var(--icon-color);">Ora puoi caricare i tuoi documenti. È semplicissimo:</p>
                                    <div style="display:flex;align-items:flex-start;gap:12px;padding:12px;background:var(--outgoing-chat-bg);border-radius:12px;border:1px solid var(--incoming-chat-border);">
                                        <div style="width:34px;height:34px;border-radius:8px;background:#6366f120;border:1px solid #6366f140;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <span class="material-symbols-rounded" style="font-size:18px;color:#6366f1;">add_2</span>
                                        </div>
                                        <div>
                                            <p style="margin:0;font-size:12px;font-weight:700;color:var(--text-color);">Clicca il pulsante <strong style="color:#6366f1;">+</strong> nell'input qui sotto</p>
                                            <p style="margin:0;font-size:11px;color:var(--icon-color);margin-top:3px;">Seleziona uno o più file — PDF, Word, Excel, immagini</p>
                                        </div>
                                    </div>
                                    <div style="display:flex;align-items:flex-start;gap:12px;padding:12px;background:var(--outgoing-chat-bg);border-radius:12px;border:1px solid var(--incoming-chat-border);">
                                        <div style="width:34px;height:34px;border-radius:8px;background:#f59e0b20;border:1px solid #f59e0b40;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <span class="material-symbols-rounded" style="font-size:18px;color:#f59e0b;">robot_2</span>
                                        </div>
                                        <div>
                                            <p style="margin:0;font-size:12px;font-weight:700;color:var(--text-color);">L'AI analizza automaticamente</p>
                                            <p style="margin:0;font-size:11px;color:var(--icon-color);margin-top:3px;">Categorizza, nomina e archivia ogni documento al posto tuo</p>
                                        </div>
                                    </div>
                                    <div style="display:flex;align-items:flex-start;gap:12px;padding:12px;background:var(--outgoing-chat-bg);border-radius:12px;border:1px solid var(--incoming-chat-border);">
                                        <div style="width:34px;height:34px;border-radius:8px;background:#10b98120;border:1px solid #10b98140;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <span class="material-symbols-rounded" style="font-size:18px;color:#10b981;">folder_open</span>
                                        </div>
                                        <div>
                                            <p style="margin:0;font-size:12px;font-weight:700;color:var(--text-color);">Trovi tutto organizzato nel DataRoom</p>
                                            <p style="margin:0;font-size:11px;color:var(--icon-color);margin-top:3px;">Puoi visualizzare, scaricare o cercare qualsiasi documento</p>
                                        </div>
                                    </div>
                                </div>`);
                            }, 2000);
                        }, 1500);
                    };

                    crearYProceder();
                    break;
                }

                case 'onboarding_completo':
                    // Abrir el DataRoom del proyecto creado
                    setTimeout(() => {
                        if (window._onboardingProyectoId && typeof window.seleccionarProyectoChat === 'function') {
                            window.seleccionarProyectoChat(window._onboardingProyectoId, window
                                ._onboardingProyectoNombre);
                        }
                        localStorage.removeItem('onboarding_proyecto');
                    }, 800);
                    break;

                case 'mostrar_documentos': {
                    const docs = params.docs || [];
                    setTimeout(() => {
                        let html =
                            `<div style="overflow-x:auto;overflow-y:auto;max-height:500px;margin:10px 0;border-radius:8px;">`;
                        docs.forEach(doc => {
                            const iconMap = {
                                'application/pdf': {
                                    label: 'PDF',
                                    color: '#ef4444'
                                },
                                'image/jpeg': {
                                    label: 'JPG',
                                    color: '#ec4899'
                                },
                                'image/png': {
                                    label: 'PNG',
                                    color: '#8b5cf6'
                                },
                            };
                            const ico = iconMap[doc.mime_type] || {
                                label: 'FILE',
                                color: '#6b7280'
                            };

                            html += `
                                <button onclick="abrirDocumentoDesdeChat(${doc.id}, '${doc.nombre.replace(/'/g,"\\'")}', '${doc.mime_type}', ${doc.proyecto_id}, '${doc.proyecto?.replace(/'/g,"\\'")||''}')"
                                    style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:12px;cursor:pointer;font-family:'Poppins',sans-serif;text-align:left;width:100%;transition:all 0.2s;"
                                    onmouseover="this.style.borderColor='#6366f1';this.style.background='#6366f108';this.style.transform='translateX(4px)'"
                                    onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='var(--incoming-chat-bg)';this.style.transform='translateX(0)'">
                                    <div style="width:36px;height:36px;border-radius:8px;background:${ico.color}20;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;font-weight:700;color:${ico.color};">${ico.label}</div>
                                    <div style="flex:1;min-width:0;">
                                        <p style="margin:0;font-size:13px;font-weight:600;color:var(--text-color);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${doc.nombre}</p>
                                        <p style="margin:2px 0 0;font-size:11px;color:var(--icon-color);">
                                            ${doc.proyecto ? `📁 ${doc.proyecto}` : ''} ${doc.categoria ? `· ${doc.categoria}` : ''}
                                        </p>
                                    </div>
                                    <span class="material-symbols-rounded" style="font-size:18px;color:var(--icon-color);flex-shrink:0;">open_in_new</span>
                                </button>`;
                        });
                        html += `</div>`;
                        agregarRespuestaBot(html);
                    }, 300);
                    break;
                }

                case 'mostrar_proyectos': {
                    const proyectos = params.proyectos || [];
                    setTimeout(() => {
                        let html = `<div style="display:flex;flex-direction:column;gap:8px;margin-top:8px;">`;
                        proyectos.forEach(p => {
                            html += `
                        <button onclick="window.seleccionarProyectoChat(${p.id}, '${p.nombre.replace(/'/g, "\\'")}')"
                            style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:12px;cursor:pointer;font-family:'Poppins',sans-serif;text-align:left;width:100%;transition:all 0.2s;"
                            onmouseover="this.style.borderColor='#f59e0b';this.style.background='#f59e0b08';this.style.transform='translateX(4px)'"
                            onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='var(--incoming-chat-bg)';this.style.transform='translateX(0)'">
                            <div style="width:36px;height:36px;border-radius:8px;background:#f59e0b20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <span class="material-symbols-rounded" style="font-size:20px;color:#f59e0b;">folder</span>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <p style="margin:0;font-size:13px;font-weight:600;color:var(--text-color);">${p.nombre}</p>
                                <p style="margin:2px 0 0;font-size:11px;color:var(--icon-color);">Clicca per aprire il DataRoom</p>
                            </div>
                            <span class="material-symbols-rounded" style="font-size:18px;color:var(--icon-color);flex-shrink:0;">arrow_forward</span>
                        </button>`;
                        });
                        html += `</div>`;
                        agregarRespuestaBot(html);
                    }, 300);
                    break;
                }

                case 'onboarding_genera_prompt': {
                    const businessType = params.business_type || '';
                    if (!businessType) break;

                    setTimeout(async () => {
                        try {
                            const res = await fetch('/user/business-prompt', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    business_type: businessType
                                })
                            });
                            const data = await res.json();

                            if (data.success) {
                                window.esNuevoUsuario = false;
                                localStorage.setItem('onboarding_completado', '1');
                                agregarRespuestaBot(`
                    <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:var(--outgoing-chat-bg);border:1px solid #6366f130;border-radius:12px;">
                        <span class="material-symbols-rounded" style="font-size:24px;color:#6366f1;flex-shrink:0;">psychology</span>
                        <div>
                            <p style="margin:0;font-size:13px;font-weight:600;color:var(--text-color);">Profilo professionale creato ✅</p>
                            <p style="margin:0;font-size:11px;color:var(--icon-color);margin-top:2px;">Docum24 è ora ottimizzato per <strong>${businessType}</strong></p>
                        </div>
                    </div>`);

                                // Continuar onboarding — sugerir nombre proyecto
                                setTimeout(() => {
                                    sendQuickMessage(
                                        `Il mio business è: ${businessType}. Suggeriscimi un nome per il progetto.`
                                    );
                                }, 800);
                            }
                        } catch (e) {
                            console.error('Error generando prompt:', e);
                        }
                    }, 400);
                    break;
                }
                case 'avvia_report':
                    setTimeout(() => avviaWizardReport(), 400);
                    break;

                case 'guida_carica_file': {
                    const proyectos = await fetch('/proyectos').then(r => r.json());
                    if (proyectos.length === 0) {
                        agregarRespuestaBot('⚠️ Prima devi creare un <strong>Progetto</strong>. Vuoi crearne uno ora?');
                        return;
                    }
                    avviaGuida('carica_file', proyectos);
                    break;
                }

                case 'guida_interroga_file': {
                    const proyectos = await fetch('/proyectos').then(r => r.json());
                    if (proyectos.length === 0) {
                        agregarRespuestaBot('⚠️ Prima devi creare un <strong>Progetto</strong>. Vuoi crearne uno ora?');
                        return;
                    }
                    avviaGuida('interroga_file', proyectos);
                    break;
                }

            }  // ← cierre del switch

        }  // ← cierre de eseguireAzione

        function avviaGuida(tipo, proyectos) {
    // Limpiar guida anterior
    document.getElementById('guida-overlay')?.remove();

    const overlay = document.createElement('div');
    overlay.id = 'guida-overlay';
    overlay.style.cssText = `position:fixed;inset:0;z-index:9998;pointer-events:none;`;
    document.body.appendChild(overlay);

    const passi = tipo === 'carica_file' ? [
        {
            target: null, // paso de selección — sin highlight
            msg: '📁 Prima di tutto, dobbiamo aprire un progetto. Selezionalo qui:',
            tipo: 'seleziona_progetto',
            proyectos: proyectos
        },
        {
            target: '#btn-upload-plus',
            msg: '✅ Progetto aperto! Ora clicca il pulsante <strong>+</strong> qui sotto per selezionare i tuoi file.',
            tipo: 'highlight'
        }
    ] : [
        {
            target: null,
            msg: '📁 Prima apriamo un progetto. Selezionalo qui:',
            tipo: 'seleziona_progetto',
            proyectos: proyectos
        },
        {
            target: '.doc-check-box',
            msg: '☑️ Spunta uno o più file dalla lista. Clicca il quadratino accanto al file che vuoi interrogare.',
            tipo: 'highlight_click',
            waitForEvent: 'checkbox_change'
        },
        {
            target: '#toolbar-btn-chat',
            msg: '💬 Ora clicca il pulsante <strong style="color:#6366f1;">Chat</strong> nella toolbar che è apparsa a destra.',
            tipo: 'highlight_click',
            waitForEvent: 'toolbar_chat_click'
        },
        {
            target: '#btn-attiva-modalita',
            msg: '⚠️ Se vedi file in arancione significa che non sono leggibili (immagini o PDF scansionati). Clicca <strong style="color:#f59e0b;">"Rimuovi tutti i non leggibili"</strong> per attivarli, poi clicca il pulsante blu <strong style="color:#6366f1;">Sì, attiva modalità</strong>.',
            tipo: 'highlight_click',
            waitForEvent: 'modalita_attiva'
        },
        {
            target: '#btn-upload-plus',
            msg: '✅ Modalità attiva! Ora scrivi la tua domanda nel campo di testo. Per uscire dalla modalità clicca la <strong style="color:#ef4444;">✕</strong> qui.',
            tipo: 'highlight'
        }
    ];

    eseguiPasso(passi, 0);
}

async function eseguiPasso(passi, idx) {
    // Rimuovi tooltip precedente
    document.getElementById('guida-tooltip')?.remove();
    document.getElementById('guida-spotlight')?.remove();

    if (idx >= passi.length) {
        document.getElementById('guida-overlay')?.remove();
        return;
    }

    const passo = passi[idx];

    if (passo.tipo === 'seleziona_progetto') {
        // Mostra lista progetti nel chat
        let html = `<div style="display:flex;flex-direction:column;gap:8px;">
            <p style="margin:0 0 8px;font-size:13px;color:var(--text-color);">${passo.msg}</p>`;
        passo.proyectos.forEach(p => {
            html += `
            <button onclick="guidaApriProgetto(${p.id}, '${p.nombre.replace(/'/g,"\\'")}', this)"
                style="display:flex;align-items:center;gap:10px;padding:10px 14px;
                       background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);
                       border-radius:10px;cursor:pointer;font-family:'Poppins',sans-serif;
                       font-size:13px;color:var(--text-color);text-align:left;width:100%;transition:all 0.2s;"
                onmouseover="this.style.borderColor='#f59e0b';this.style.background='#f59e0b08'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='var(--incoming-chat-bg)'">
                <span class="material-symbols-rounded" style="color:#f59e0b;font-size:18px;">folder</span>
                ${p.nombre}
                <span class="material-symbols-rounded" style="margin-left:auto;font-size:16px;color:var(--icon-color);">arrow_forward</span>
            </button>`;
        });
        html += `</div>`;
        agregarRespuestaBot(html);

        // Guardar passi para continuar después
        window._guidaPassi = passi;
        window._guidaIdx = idx + 1;
        return;
    }

    if (passo.tipo === 'info_selezione') {
        agregarRespuestaBot(`
            <div style="display:flex;align-items:flex-start;gap:10px;padding:12px;
                        background:#6366f115;border:1px solid #6366f130;border-radius:12px;">
                <span class="material-symbols-rounded" style="color:#6366f1;font-size:22px;flex-shrink:0;">info</span>
                <div>
                    <p style="margin:0;font-size:13px;color:var(--text-color);">${passo.msg}</p>
                </div>
            </div>`);
        document.getElementById('guida-overlay')?.remove();
        return;
    }

    if (passo.tipo === 'highlight_click' || passo.tipo === 'highlight') {
    // Aspetta un attimo se l'elemento potrebbe non essere ancora nel DOM
    await new Promise(r => setTimeout(r, 400));

    const el = document.querySelector(passo.target);
    if (!el) {
        agregarRespuestaBot(passo.msg);
        document.getElementById('guida-overlay')?.remove();
        return;
    }

    const rect = el.getBoundingClientRect();

    // Spotlight
    const spotlight = document.createElement('div');
    spotlight.id = 'guida-spotlight';
    spotlight.style.cssText = `
        position:fixed;
        top:${rect.top - 8}px;left:${rect.left - 8}px;
        width:${rect.width + 16}px;height:${rect.height + 16}px;
        border-radius:12px;
        box-shadow:0 0 0 9999px rgba(0,0,0,0.55);
        z-index:9999;pointer-events:none;
        border:2px solid #6366f1;
        animation:pulse 1.5s infinite;`;
    document.body.appendChild(spotlight);

    // Tooltip
    const tooltip = document.createElement('div');
    tooltip.id = 'guida-tooltip';

    const topPos = rect.top > 200
        ? `bottom:${window.innerHeight - rect.top + 16}px;`
        : `top:${rect.bottom + 16}px;`;

    tooltip.style.cssText = `
        position:fixed;
        ${topPos}
        left:50%;transform:translateX(-50%);
        background:var(--incoming-chat-bg);
        border:1px solid #6366f1;
        border-radius:14px;padding:14px 18px;
        max-width:320px;width:90%;
        box-shadow:0 8px 24px rgba(0,0,0,0.4);
        z-index:10000;font-family:'Poppins',sans-serif;
        animation:fadeIn 0.3s ease;`;
    tooltip.innerHTML = `
        <p style="margin:0 0 10px;font-size:13px;color:var(--text-color);line-height:1.5;">${passo.msg}</p>
        <button onclick="
            document.getElementById('guida-spotlight')?.remove();
            document.getElementById('guida-tooltip')?.remove();
            eseguiPasso(window._guidaPassi, window._guidaIdx);"
            style="background:#6366f1;color:#fff;border:none;border-radius:8px;
                   padding:7px 16px;font-size:12px;font-weight:600;cursor:pointer;
                   font-family:'Poppins',sans-serif;">
            Ho capito ✓
        </button>`;
    document.body.appendChild(tooltip);

    window._guidaPassi = passi;
    window._guidaIdx = idx + 1;
}
}

async function guidaApriProgetto(id, nombre, btn) {
    // Disabilita bottoni lista
    btn.closest('div').querySelectorAll('button').forEach(b => b.disabled = true);

    // Apri il progetto
    if (typeof window.seleccionarProyectoChat === 'function') {
        window.seleccionarProyectoChat(id, nombre);
    }

    agregarRespuestaBot(`✅ Progetto <strong>${nombre}</strong> aperto!`);

    // Continua con il passo successivo
    setTimeout(() => {
        if (window._guidaPassi && window._guidaIdx < window._guidaPassi.length) {
            eseguiPasso(window._guidaPassi, window._guidaIdx);
        } else {
            document.getElementById('guida-overlay')?.remove();
        }
    }, 1000);
}

        async function aprireProgettoPorNome(nome) {
            const res = await fetch('/proyectos');
            const proyectos = await res.json();
            const found = proyectos.find(p =>
                p.nombre.toLowerCase().includes(nome.toLowerCase())
            );
            if (found && typeof window.seleccionarProyectoChat === 'function') {
                window.seleccionarProyectoChat(found.id, found.nombre);
            }
        }

        // Cierra el menú al hacer clic en cualquier parte de la pantalla
        window.addEventListener('click', function(e) {
            const menu = document.getElementById('user-menu');
            // Si el clic no fue en el botón del avatar, cierra el menú
            if (!e.target.closest('button')) {
                menu.style.display = 'none';
            }
        });

        // ── WIZARD REPORT ─────────────────────────────────────────
        let _reportWizard = {
            proyectoId: null,
            formato: 'pdf'
        };

        async function avviaWizardReport() {
            // Eliminar wizard anterior si existe
            document.querySelectorAll('.report-wizard-wrapper').forEach(el => el.remove());
            const proyectoActualId = typeof dataroomProyectoId !== 'undefined' ? dataroomProyectoId : null;
            const proyectoActualNombre = document.getElementById('dataroom-titulo')?.textContent?.trim() || null;

            _reportWizard.proyectoId = proyectoActualId;

            let selectorProyecto = '';

            if (!proyectoActualId) {
                const res = await fetch('/report/proyectos');
                const proyectos = await res.json();

                const opcionesProy = proyectos.map(p =>
                    `<button onclick="seleccionarProyectoReport(${p.id}, this)"
                style="display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:8px;
                       border:1px solid var(--incoming-chat-border);background:transparent;
                       color:var(--text-color);font-family:'Poppins',sans-serif;font-size:12px;
                       cursor:pointer;text-align:left;transition:all 0.2s;width:100%;"
                onmouseover="this.style.borderColor='#f59e0b';this.style.background='#f59e0b0d'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='transparent'">
                <span class="material-symbols-rounded" style="font-size:16px;color:#f59e0b;">folder</span>
                ${p.nombre}
                <span style="color:var(--icon-color);font-size:11px;margin-left:auto;">${p.documentos_count} doc</span>
            </button>`
                ).join('');

                selectorProyecto = `
        <div style="margin-bottom:14px;">
            <p style="margin:0 0 8px;font-size:12px;color:var(--icon-color);font-weight:500;">PROGETTO</p>
            <div style="display:flex;flex-direction:column;gap:5px;" id="report-proy-lista">
                <button onclick="seleccionarProyectoReport(null, this)"
                    style="display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:8px;
                           border:2px solid #6366f1;background:#6366f115;
                           color:var(--text-color);font-family:'Poppins',sans-serif;font-size:12px;
                           cursor:pointer;text-align:left;transition:all 0.2s;width:100%;">
                    <span class="material-symbols-rounded" style="font-size:16px;color:#6366f1;">folder_special</span>
                    Tutti i Progetti
                </button>
                ${opcionesProy}
            </div>
        </div>`;
            } else {
                selectorProyecto = `
        <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:8px;
                    background:#f59e0b15;border:1px solid #f59e0b40;margin-bottom:14px;">
            <span class="material-symbols-rounded" style="font-size:16px;color:#f59e0b;">folder_open</span>
            <span style="font-size:13px;color:var(--text-color);font-weight:500;">${proyectoActualNombre}</span>
            <span style="font-size:11px;color:var(--icon-color);margin-left:4px;">(progetto attivo)</span>
        </div>`;
            }

            const wizardDiv = document.createElement('div');
            wizardDiv.className = 'chat incoming report-wizard-wrapper';
            wizardDiv.style.padding = '16px 10px';
            wizardDiv.innerHTML = `
    <div class="chat-content">
        <div class="chat-icon"><span class="material-symbols-rounded">robot_2</span></div>
        <div class="chat-text" style="font-size:13px;">
            <div style="display:flex;flex-direction:column;gap:2px;min-width:280px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                    <p style="margin:0;font-size:13px;color:var(--text-color);font-weight:600;">
                        <span class="material-symbols-rounded" style="font-size:16px;vertical-align:middle;color:#6366f1;">description</span>
                        Configura il Report
                    </p>
                    <button onclick="document.querySelectorAll('.report-wizard-wrapper').forEach(el=>el.remove())"
                        style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;transition:all 0.2s;"
                        onmouseover="this.style.color='#ef4444'"
                        onmouseout="this.style.color='var(--icon-color)'">
                        <span class="material-symbols-rounded" style="font-size:18px;">close</span>
                    </button>
                </div>

                ${selectorProyecto}

                <div style="margin-bottom:14px;">
                    <p style="margin:0 0 6px;font-size:12px;color:var(--icon-color);font-weight:500;">DESCRIVI IL REPORT</p>
                    <textarea id="report-instrucciones" rows="3"
                        placeholder="Es. Voglio un report con tabella di tutti i documenti per categoria, con statistiche e riepilogo finale..."
                        style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);
                               border-radius:8px;padding:10px 12px;font-size:12px;color:var(--text-color);
                               font-family:'Poppins',sans-serif;outline:none;resize:none;box-sizing:border-box;transition:border-color 0.2s;"
                        onfocus="this.style.borderColor='#6366f1'"
                        onblur="this.style.borderColor='var(--incoming-chat-border)'"></textarea>
                </div>

                <div style="margin-bottom:14px;">
                    <p style="margin:0 0 6px;font-size:12px;color:var(--icon-color);font-weight:500;">FORMATO</p>
                    <div style="display:flex;gap:8px;">
                        <button id="report-btn-pdf" onclick="seleccionarFormatoReport('pdf')"
                            style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:9px;
                                   border-radius:8px;border:2px solid #ef4444;background:#ef444420;
                                   color:#ef4444;font-family:'Poppins',sans-serif;font-size:12px;font-weight:600;cursor:pointer;">
                            <span class="material-symbols-rounded" style="font-size:16px;">picture_as_pdf</span> PDF
                        </button>
                        <button id="report-btn-docx" onclick="seleccionarFormatoReport('docx')"
                            style="flex:1;display:flex;align-items:center;justify-content:center;gap:6px;padding:9px;
                                   border-radius:8px;border:1px solid var(--incoming-chat-border);background:transparent;
                                   color:var(--text-color);font-family:'Poppins',sans-serif;font-size:12px;cursor:pointer;">
                            <span class="material-symbols-rounded" style="font-size:16px;">article</span> Word
                        </button>
                    </div>
                </div>

                <button id="report-btn-genera" onclick="eseguireReport()"
                    style="width:100%;background:#6366f1;color:#fff;border:none;border-radius:10px;padding:11px;
                           font-size:13px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;
                           display:flex;align-items:center;justify-content:center;gap:8px;transition:background 0.2s;"
                    onmouseover="this.style.background='#4f46e5'"
                    onmouseout="this.style.background='#6366f1'">
                    <span class="material-symbols-rounded" style="font-size:17px;">download</span>
                    Genera e Scarica
                </button>
            </div>
        </div>
    </div>`;
            wizardDiv.style.opacity = '0';
            wizardDiv.style.transform = 'translateY(12px)';
            wizardDiv.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            chatContainer.appendChild(wizardDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            setTimeout(() => {
                wizardDiv.style.opacity = '1';
                wizardDiv.style.transform = 'translateY(0)';
            }, 30);
        }

        function seleccionarProyectoReport(id, btn) {
            _reportWizard.proyectoId = id;
            document.querySelectorAll('#report-proy-lista button').forEach(b => {
                b.style.border = '1px solid var(--incoming-chat-border)';
                b.style.background = 'transparent';
            });
            btn.style.border = '2px solid #6366f1';
            btn.style.background = '#6366f115';
        }

        function seleccionarFormatoReport(fmt) {
            _reportWizard.formato = fmt;
            const pdf = document.getElementById('report-btn-pdf');
            const docx = document.getElementById('report-btn-docx');
            if (fmt === 'pdf') {
                pdf.style.cssText += ';border:2px solid #ef4444;background:#ef444420;color:#ef4444;';
                docx.style.cssText +=
                    ';border:1px solid var(--incoming-chat-border);background:transparent;color:var(--text-color);';
            } else {
                docx.style.cssText += ';border:2px solid #3b82f6;background:#3b82f620;color:#3b82f6;';
                pdf.style.cssText +=
                    ';border:1px solid var(--incoming-chat-border);background:transparent;color:var(--text-color);';
            }
        }

        async function eseguireReport() {
            const instrucciones = document.getElementById('report-instrucciones')?.value.trim();
            if (!instrucciones) {
                const ta = document.getElementById('report-instrucciones');
                if (ta) {
                    ta.style.borderColor = '#ef4444';
                    ta.focus();
                }
                return;
            }

            const btn = document.getElementById('report-btn-genera');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML =
                    `<span class="material-symbols-rounded" style="font-size:17px;animation:spin 1s linear infinite;">autorenew</span> Generazione in corso...`;
            }

            try {
                const res = await fetch('/report/generar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        instrucciones: instrucciones,
                        formato: _reportWizard.formato || 'pdf',
                        proyecto_id: _reportWizard.proyectoId || null,
                    })
                });

                if (!res.ok) throw new Error('Errore');

                const blob = await res.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `Docum24_report_${Date.now()}.${_reportWizard.formato || 'pdf'}`;
                a.click();
                URL.revokeObjectURL(url);

                agregarRespuestaBot('✅ Report generato e scaricato con successo!');

                // Eliminar el wizard
                document.querySelectorAll('.report-wizard-wrapper').forEach(el => el.remove());

            } catch (e) {
                agregarRespuestaBot('❌ Errore durante la generazione. Riprova.');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML =
                        `<span class="material-symbols-rounded" style="font-size:17px;">download</span> Genera e Scarica`;
                }
            }
        }

        function toggleDropdownCrea() {
            const d = document.getElementById('dropdown-crea');
            d.style.display = d.style.display === 'none' ? 'block' : 'none';
        }

        // Cerrar al click fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[onclick="toggleDropdownCrea()"]') &&
                !e.target.closest('#dropdown-crea')) {
                const d = document.getElementById('dropdown-crea');
                if (d) d.style.display = 'none';
            }
        });

        async function sendMessageDocMode(message) {
            if (!message) return;

            const defaultText = chatContainer.querySelector('.default-text');
            if (defaultText) defaultText.remove();

            chatInput.disabled = true;
            sendBtn.classList.add('disabled');
            addUserMessage(message);
            chatInput.value = '';
            chatInput.style.height = 'auto';
            addTypingAnimation();

            // Controller
            currentAbortController = new AbortController();
            const signal = currentAbortController.signal;

            // Cambiar btn a cancel
            sendBtn.querySelector('span').textContent = 'cancel';
            sendBtn.querySelector('span').style.color = '#ef4444';
            sendBtn.removeEventListener('click', sendMessage);
            sendBtn.addEventListener('click', cancelarEnvio);

            try {
                const response = await fetch('/chat/con-documenti', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        ids: window._chatDocIds,
                        message: message,
                        historial: window._chatDocHistorial,
                    }),
                    signal: signal
                });

                const data = await response.json();
                removeTypingAnimation();

                if (data.success) {
                    window._chatDocHistorial.push({
                        role: 'user',
                        content: message
                    });
                    window._chatDocHistorial.push({
                        role: 'assistant',
                        content: data.response
                    });

                    if (window._chatDocHistorial.length > 10) {
                        window._chatDocHistorial = window._chatDocHistorial.slice(-10);
                    }

                    await addBotMessage(data.response);

                    // Actualizar widget tokens
                    window.actualizarTokenWidget && window.actualizarTokenWidget();

                } else if (data.error === 'quota_esaurita') {
                    await addBotMessage(`
        <div style="display:flex;align-items:flex-start;gap:10px;padding:12px;
                    background:#ef444410;border:1px solid #ef444430;border-radius:12px;">
            <span class="material-symbols-rounded" style="color:#ef4444;font-size:22px;flex-shrink:0;">battery_0_bar</span>
            <div>
                <p style="margin:0;font-size:13px;font-weight:600;color:#ef4444;">Token esauriti</p>
                <p style="margin:4px 0 0;font-size:12px;color:var(--icon-color);">
                    Hai consumato tutti i token disponibili del tuo piano.<br>
                    La modalità Chat Documenti non è disponibile.
                </p>
            </div>
        </div>`);
                    disattivareChatDocumenti();

                } else {
                    await addBotMessage('❌ Errore: ' + (data.error || 'Riprova.'));
                }

            } catch (error) {
                removeTypingAnimation();
                if (error.name === 'AbortError') {
                    agregarRespuestaBot('⚠️ Richiesta annullata.');
                } else {
                    await addBotMessage('❌ Errore di connessione. Riprova.');
                    console.error('Error:', error);
                }
            }

            resetSendBtn();
            chatInput.focus();
        }

        function cancelarEnvio() {
            if (currentAbortController) {
                currentAbortController.abort();
                currentAbortController = null;
            }
        }

        function resetSendBtn() {
            currentAbortController = null;
            sendBtn.innerHTML = '<span class="material-symbols-rounded">send</span>';
            sendBtn.style.background = '';
            sendBtn.removeEventListener('click', cancelarEnvio);
            sendBtn.addEventListener('click', sendMessage);
            chatInput.disabled = false;
            sendBtn.classList.remove('disabled');
        }

        function togglePianoWidget() {
            const dd = document.getElementById('piano-dropdown');
            dd.style.display = dd.style.display === 'none' ? 'block' : 'none';
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#btn-piano-widget') &&
                !e.target.closest('#piano-dropdown')) {
                const dd = document.getElementById('piano-dropdown');
                if (dd) dd.style.display = 'none';
            }
        });

        async function cargarTokenWidget() {
            try {
                const res = await fetch('/user/tokens');
                const data = await res.json();
                if (!data.tiene_suscripcion) return;

                const tokenPct = Math.min(data.porcentaje, 100);
                const storagePct = Math.min(data.storage_porcentaje, 100);

                const getColor = (pct) => {
                    if (pct >= 90) return '#ef4444';
                    if (pct >= 60) return '#f59e0b';
                    return '#10b981';
                };

                // Badge
                const badge = document.getElementById('piano-badge');
                // Badge por tokens
                if (badge) {
                    const badgePorTokens = tokenPct >= 90 || storagePct >= 90;
                    const badgePorEmpresa = badge.dataset.empresa === '1';
                    badge.style.display = (badgePorTokens || badgePorEmpresa) ? 'block' : 'none';
                }

                // Plan
                const planNome = document.getElementById('pw-plan-nome');
                const planFecha = document.getElementById('pw-fecha');
                const planFecha2 = document.getElementById('pw-fecha2');
                if (!planNome) return;

                planNome.textContent = data.plan;
                if (planFecha) planFecha.textContent = 'fino al ' + data.fecha_fin;
                if (planFecha2) planFecha2.textContent = data.fecha_fin;

                // Tokens
                const tc = getColor(tokenPct);
                const tokenBar = document.getElementById('pw-token-bar');
                const tokenTxt = document.getElementById('pw-token-txt');
                if (tokenBar) {
                    tokenBar.style.width = tokenPct + '%';
                    tokenBar.style.background = tc;
                }
                if (tokenTxt) {
                    tokenTxt.textContent = formatTokens(data.tokens_restantes) + ' rimanenti (' + tokenPct + '%)';
                    tokenTxt.style.color = tc;
                }

                // Storage
                const sc = getColor(storagePct);
                const storageBar = document.getElementById('pw-storage-bar');
                const storageTxt = document.getElementById('pw-storage-txt');
                if (storageBar) {
                    storageBar.style.width = storagePct + '%';
                    storageBar.style.background = sc;
                }
                if (storageTxt) {
                    storageTxt.textContent = data.storage_usado_mb + '/' + data.storage_limite_mb + 'MB (' +
                        storagePct + '%)';
                    storageTxt.style.color = sc;
                }

                // Botones upgrade
                const upgradeBtns = document.getElementById('pw-upgrade-btns');
                if (upgradeBtns) {
                    if (data.plan === 'Trial') {
                        upgradeBtns.innerHTML = `
        <button onclick="iniziareCheckout('Basic')"
            style="display:flex;align-items:center;justify-content:center;gap:6px;
                   padding:7px;border-radius:8px;background:#6366f1;color:#fff;
                   border:none;cursor:pointer;font-size:12px;font-weight:600;
                   width:100%;font-family:'Poppins',sans-serif;transition:background 0.2s;"
            onmouseover="this.style.background='#4f46e5'"
            onmouseout="this.style.background='#6366f1'">
            <span class="material-symbols-rounded" style="font-size:14px;">upgrade</span>
            Passa a Basic — €9/mese
        </button>
        <button onclick="iniziareCheckout('Pro')"
            style="display:flex;align-items:center;justify-content:center;gap:6px;
                   padding:7px;border-radius:8px;background:transparent;
                   color:var(--text-color);border:1px solid var(--incoming-chat-border);
                   cursor:pointer;font-size:12px;width:100%;
                   font-family:'Poppins',sans-serif;transition:all 0.2s;"
            onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1'"
            onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--text-color)'">
            Pro — €29/mese
        </button>`;
                    } else if (data.plan === 'Basic') {
                        upgradeBtns.innerHTML = `
        <button onclick="iniziareCheckout('Pro')"
            style="display:flex;align-items:center;justify-content:center;gap:6px;
                   padding:7px;border-radius:8px;background:#6366f1;color:#fff;
                   border:none;cursor:pointer;font-size:12px;font-weight:600;
                   width:100%;font-family:'Poppins',sans-serif;transition:background 0.2s;"
            onmouseover="this.style.background='#4f46e5'"
            onmouseout="this.style.background='#6366f1'">
            <span class="material-symbols-rounded" style="font-size:14px;">upgrade</span>
            Passa a Pro — €29/mese
        </button>`;
                    } else {
                        upgradeBtns.innerHTML = `
                    <p style="font-size:11px;color:#10b981;text-align:center;font-weight:600;">
                        ✓ Sei al piano massimo
                    </p>`;
                    }
                }

            } catch (e) {
                console.error('Token widget error:', e);
            }
        }

        async function iniziareCheckout(plan) {
            const btns = document.querySelectorAll('#pw-upgrade-btns button');
            btns.forEach(b => {
                b.disabled = true;
                b.innerHTML =
                    '<span class="material-symbols-rounded" style="font-size:14px;animation:spin 1s linear infinite;">autorenew</span> Caricamento...';
            });

            try {
                const res = await fetch(`/checkout/${plan}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await res.json();
                if (data.url) {
                    window.location.href = data.url;
                }
            } catch (e) {
                console.error('Checkout error:', e);
                btns.forEach(b => b.disabled = false);
                cargarTokenWidget(); // restaurar botones
            }
        }

        function formatTokens(n) {
            if (n >= 1000000) return (n / 1000000).toFixed(1) + 'M';
            if (n >= 1000) return (n / 1000).toFixed(0) + 'K';
            return n;
        }

        // Cargar al inicio y cada 30 segundos
        document.addEventListener('DOMContentLoaded', () => {
            cargarTokenWidget();
            setInterval(cargarTokenWidget, 30000);
        });

        window.actualizarTokenWidget = cargarTokenWidget;

        function calcularUrgencia(fechaScadenza) {
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            const scadenza = new Date(fechaScadenza);
            scadenza.setHours(0, 0, 0, 0);
            const giorni = Math.round((scadenza - hoy) / (1000 * 60 * 60 * 24));

            if (giorni <= 1) return {
                label: 'Scade oggi/domani',
                color: '#ef4444',
                bg: '#ef444415',
                border: '#ef444430',
                dias_antes: 0,
                giorni
            };
            if (giorni <= 7) return {
                label: `Scade in ${giorni} giorni`,
                color: '#f59e0b',
                bg: '#f59e0b15',
                border: '#f59e0b30',
                dias_antes: 1,
                giorni
            };
            if (giorni <= 30) return {
                label: `Scade in ${giorni} giorni`,
                color: '#eab308',
                bg: '#eab30815',
                border: '#eab30830',
                dias_antes: 7,
                giorni
            };
            return {
                label: `Scade in ${giorni} giorni`,
                color: '#10b981',
                bg: '#10b98115',
                border: '#10b98130',
                dias_antes: 30,
                giorni
            };
        }

        function mostrarDocsConScadenza(docs) {
            const userEmail = '{{ auth()->user()->email }}';
            const docsFuturi = docs.filter(d => {
                const scadenza = new Date(d.fecha_scadenza);
                scadenza.setHours(0, 0, 0, 0);
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0);
                return scadenza >= hoy;
            });

            if (!docsFuturi.length) return;

            let listaHTML = docsFuturi.map((doc, idx) => {
                const urg = calcularUrgencia(doc.fecha_scadenza);
                const scadenzaStr = new Date(doc.fecha_scadenza).toLocaleDateString('it-IT');
                return `
        <div id="notifica-card-${idx}"
            style="border:1px solid ${urg.border};border-radius:10px;
                   background:${urg.bg};overflow:hidden;transition:all 0.2s;">

            <!-- Header -->
            <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;">
                <span class="material-symbols-rounded"
                    style="font-size:16px;color:${urg.color};flex-shrink:0;">event</span>
                <div style="flex:1;min-width:0;">
                    <p style="margin:0;font-size:12px;font-weight:600;color:var(--text-color);
                              white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        ${doc.nombre}
                    </p>
                    <p style="margin:0;font-size:11px;color:${urg.color};">
                        ${urg.label} · ${scadenzaStr}
                    </p>
                </div>

                <!-- Expand btn -->
                <button onclick="toggleDescDoc(${idx})"
                    style="border:none;background:transparent;cursor:pointer;
                           color:var(--icon-color);padding:2px;border-radius:4px;
                           display:flex;transition:all 0.2s;flex-shrink:0;"
                    onmouseover="this.style.color='var(--text-color)'"
                    onmouseout="this.style.color='var(--icon-color)'">
                    <span id="expand-icon-${idx}" class="material-symbols-rounded"
                        style="font-size:16px;">expand_more</span>
                </button>

                <!-- Switch -->
                <label onclick="toggleNotifica(event, ${idx}, ${doc.id}, '${doc.fecha_scadenza}', ${urg.dias_antes}, '${userEmail}')"
                    style="cursor:pointer;flex-shrink:0;">
                    <div id="switch-${idx}"
                        style="width:36px;height:20px;border-radius:10px;
                               background:var(--incoming-chat-border);
                               position:relative;transition:background 0.2s;">
                        <div style="width:16px;height:16px;border-radius:50%;
                                    background:#fff;position:absolute;top:2px;left:2px;
                                    transition:transform 0.2s;box-shadow:0 1px 3px rgba(0,0,0,0.2);"
                            id="switch-thumb-${idx}"></div>
                    </div>
                </label>
            </div>

            <!-- Descrizione espandibile -->
            <div id="desc-doc-${idx}"
                style="max-height:0;overflow:hidden;transition:max-height 0.3s ease;">
                <div style="padding:0 12px 10px;border-top:1px solid ${urg.border};">
                    <p style="margin:8px 0 0;font-size:11px;color:var(--icon-color);line-height:1.6;">
                        ${doc.descrizione || 'Nessuna descrizione disponibile.'}
                    </p>
                </div>
            </div>
        </div>`;
            }).join('');

            agregarRespuestaBot(`
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="material-symbols-rounded"
                    style="font-size:20px;color:#f59e0b;">notifications_active</span>
                <strong style="font-size:13px;">
                    ${docsFuturi.length} document${docsFuturi.length > 1 ? 'i' : 'o'} con scadenza rilevat${docsFuturi.length > 1 ? 'i' : 'o'}
                </strong>
            </div>
            <p style="margin:0;font-size:12px;color:var(--icon-color);">
                Attiva il promemoria per ricevere un'email prima della scadenza.
            </p>
            <div style="display:flex;flex-direction:column;gap:6px;">
                ${listaHTML}
            </div>
        </div>`);
        }

        // Toggle descripción
        function toggleDescDoc(idx) {
            const desc = document.getElementById(`desc-doc-${idx}`);
            const icon = document.getElementById(`expand-icon-${idx}`);
            const isOpen = desc.style.maxHeight !== '0px' && desc.style.maxHeight !== '';
            desc.style.maxHeight = isOpen ? '0px' : '200px';
            icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }

        // Toggle switch notifica
        const _notificheActivas = {}; // idx -> notifica_id

        async function toggleNotifica(e, idx, docId, fechaScadenza, diasAntes, email) {
            e.preventDefault();
            const sw = document.getElementById(`switch-${idx}`);
            const thumb = document.getElementById(`switch-thumb-${idx}`);
            const isActive = _notificheActivas[idx];

            if (isActive) {
                // Desactivar → DELETE
                try {
                    await fetch(`/notifiche/${_notificheActivas[idx]}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    delete _notificheActivas[idx];
                    sw.style.background = 'var(--incoming-chat-border)';
                    thumb.style.transform = 'translateX(0px)';
                } catch (e) {
                    console.error(e);
                }
            } else {
                // Activar → POST
                try {
                    const res = await fetch('/notifiche', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            documento_id: docId,
                            fecha_scadenza: fechaScadenza,
                            dias_antes: diasAntes,
                            email: email
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        _notificheActivas[idx] = data.notifica.id;
                        sw.style.background = '#10b981';
                        thumb.style.transform = 'translateX(16px)';
                    }
                } catch (e) {
                    console.error(e);
                }
            }
            aggiornareBadgeNotifiche();
        }

        async function crearNotifichePerTutti(docs) {
            const userEmail = '{{ auth()->user()->email }}';
            let creati = 0;

            for (const doc of docs) {
                try {
                    const res = await fetch('/notifiche', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            documento_id: doc.id,
                            fecha_scadenza: doc.fecha_scadenza,
                            dias_antes: 7,
                            email: userEmail
                        })
                    });
                    const data = await res.json();
                    if (data.success) creati++;
                } catch (e) {
                    console.error(e);
                }
            }

            // Rimuovere la card della domanda
            document.querySelectorAll('.chat.incoming').forEach(el => {
                if (el.querySelector('[onclick*="crearNotifichePerTutti"]')) el.remove();
            });

            agregarRespuestaBot(`
            <div style="display:flex;align-items:center;gap:8px;padding:12px;
                        background:#f59e0b10;border:1px solid #f59e0b30;border-radius:12px;">
                <span class="material-symbols-rounded" style="color:#f59e0b;font-size:22px;">check_circle</span>
                <div>
                    <p style="margin:0;font-size:13px;font-weight:600;color:var(--text-color);">
                        ${creati} promemoria creato${creati > 1 ? 'i' : ''}!
                    </p>
                    <p style="margin:4px 0 0;font-size:12px;color:var(--icon-color);">
                        Riceverai un'email a <strong>${userEmail}</strong> 7 giorni prima della scadenza.
                    </p>
                </div>
            </div>`);
            aggiornareBadgeNotifiche();
        }
    </script>

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
            }

            to {
                transform: translateX(0);
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(100%);
            }
        }

        .chat-text table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin: 10px 0;
            table-layout: fixed;
        }

        .chat-text th {
            padding: 8px 12px;
            background: var(--outgoing-chat-bg);
            font-weight: 600;
            color: var(--text-color);
            border-bottom: 2px solid var(--incoming-chat-border);
            text-align: left;
            white-space: nowrap;
        }

        .chat-text td {
            padding: 7px 12px;
            border-bottom: 1px solid var(--incoming-chat-border);
            color: var(--text-color);
            vertical-align: top;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .chat-text tr:last-child td {
            border-bottom: none;
        }

        .chat-text div[style*="overflow-x"] {
            max-height: 500px;
            overflow-y: auto;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.4);
                opacity: 0.6;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>

    <script>
        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }

        // Cerrar al hacer click fuera
        document.addEventListener('click', function(e) {
            if (e.target.closest('#themeToggle')) {
                const newTheme = body.classList.contains('light-mode') ? 'dark' : 'light';
                setTheme(newTheme);
            }
        });

        function sendQuickMessage(text) {
            chatInput.value = text;
            sendMessage();
        }


        let formularioAbierto = false;

        function mostrarFormularioChat({
            icon,
            iconColor,
            title,
            fields,
            submitLabel,
            onSubmit
        }) {
            cerrarTodosLosPaneles();
            if (formularioAbierto) {
                const existing = document.getElementById('form-wrapper-active');
                if (existing) {
                    existing.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    existing.querySelector('.chat-form-card').style.outline = '2px solid #6366f1';
                    setTimeout(() => existing.querySelector('.chat-form-card').style.outline = 'none', 1500);
                }
                agregarRespuestaBot('Ho già aperto un formulario. Compilalo o annullalo prima di aprirne un altro.');
                return;
            }

            formularioAbierto = true;

            const fieldsHTML = fields.map(f => `
        <div style="margin-bottom:12px;">
            <label style="font-size:12px;color:var(--icon-color);display:block;margin-bottom:4px;">${f.label}${f.required ? ' *' : ''}</label>
            <input id="${f.id}" type="text" placeholder="${f.placeholder}"
                style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:10px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                onfocus="this.style.borderColor='#6366f1'"
                onblur="this.style.borderColor='var(--incoming-chat-border)'"
                onkeydown="if(event.key==='Enter'){document.getElementById('form-submit-btn').click();}">
        </div>
    `).join('');

            const div = document.createElement('div');
            div.id = 'form-wrapper-active';
            div.className = 'chat-form-center';
            div.innerHTML = `
        <div class="chat-form-card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <h4 style="font-size:14px;font-weight:600;color:var(--text-color);margin:0;display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="color:${iconColor};font-size:18px;">${icon}</span>
                    ${title}
                </h4>
                <button onclick="cancellarFormulario()"
                    style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;align-items:center;transition:all 0.2s;"
                    onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='#ef4444'"
                    onmouseout="this.style.background='none';this.style.color='var(--icon-color)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">close</span>
                </button>
            </div>
            ${fieldsHTML}
            <div style="display:flex;gap:8px;margin-top:4px;">
                <button id="form-submit-btn"
                    style="flex:1;background:#6366f1;color:#fff;border:none;border-radius:8px;padding:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:background 0.2s;"
                    onmouseover="this.style.background='#4f46e5'"
                    onmouseout="this.style.background='#6366f1'">
                    ${submitLabel}
                </button>
                <button onclick="cancellarFormulario()"
                    style="background:transparent;color:var(--icon-color);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:10px 16px;font-size:13px;cursor:pointer;font-family:'Poppins',sans-serif;">
                    Annulla
                </button>
            </div>
        </div>
    `;

            div.querySelector('#form-submit-btn').addEventListener('click', onSubmit);

            div.style.opacity = '0';
            div.style.transform = 'translateY(10px)';
            div.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            chatContainer.appendChild(div);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            setTimeout(() => {
                div.style.opacity = '1';
                div.style.transform = 'translateY(0)';
            }, 30);
            setTimeout(() => {
                const f = div.querySelector('input');
                if (f) f.focus();
            }, 100);
        }

        function cancellarFormulario(btn) {
            const wrapper = document.getElementById('form-wrapper-active');
            if (wrapper) {
                wrapper.style.opacity = '0';
                wrapper.style.transform = 'translateY(-10px)';
                wrapper.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                setTimeout(() => {
                    wrapper.remove();
                    formularioAbierto = false;
                }, 300);
            }
        }

        window.abrirNuevoProyectoChat = function() {
            mostrarAccionesChat({
                titulo: 'Gestisci Progetti',
                acciones: [{
                        icon: 'create_new_folder',
                        color: '#f59e0b',
                        label: 'Crea',
                        onclick: "accionProyecto('crea')"
                    },
                    {
                        icon: 'edit',
                        color: '#6366f1',
                        label: 'Modifica',
                        onclick: "accionProyecto('edita')"
                    },
                    {
                        icon: 'delete',
                        color: '#ef4444',
                        label: 'Elimina',
                        onclick: "accionProyecto('elimina')"
                    }
                ]
            });
        }

        function mostrarAccionesChat({
            titulo,
            acciones,
            subtitulo
        }) {
            cerrarTodosLosPaneles();
            const div = document.createElement('div');
            div.className = 'chat-action-icons';
            div.style.flexDirection = 'column';
            div.style.alignItems = 'center';

            let botonesHTML = '';
            acciones.forEach(a => {
                botonesHTML += `
                        <button class="chat-action-btn"
                            onclick="${a.onclick}"
                            onmouseover="this.style.borderColor='${a.color}';this.style.background='${a.color}15'"
                            onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='var(--incoming-chat-bg)'">
                            <span class="material-symbols-rounded" style="color:${a.color};">${a.icon}</span>
                            <span class="label">${a.label}</span>
                        </button>`;
            });

            let html = '';
            if (subtitulo) {
                html += '<p style="font-size:12px;color:var(--icon-color);margin-bottom:12px;text-align:center;">' +
                    subtitulo + '</p>';
            }
            html +=
                '<p style="font-size:13px;font-weight:600;color:var(--text-color);margin-bottom:14px;text-align:center;">' +
                titulo + '</p>';
            html += '<div style="display:flex;gap:16px;justify-content:center;">' + botonesHTML + '</div>';

            div.innerHTML = html;
            div.style.opacity = '0';
            div.style.transform = 'translateY(10px)';
            div.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            chatContainer.appendChild(div);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            setTimeout(() => {
                div.style.opacity = '1';
                div.style.transform = 'translateY(0)';
            }, 30);
        }

        function cerrarTodosLosPaneles() {
            // Cerrar formularios
            const wrapper = document.getElementById('form-wrapper-active');
            if (wrapper) {
                wrapper.remove();
                formularioAbierto = false;
            }

            // Cerrar listas y paneles de acciones
            document.querySelectorAll('.chat-form-center, .chat-action-icons').forEach(el => el.remove());
        }

        function mostrarListaProyectosChat(proyectos, accion) {
            cerrarTodosLosPaneles();
            const isDelete = accion === 'delete';
            const color = isDelete ? '#ef4444' : '#6366f1';
            const icono = isDelete ? 'delete' : 'edit';
            const titulo = isDelete ? 'Elimina Progetto' : 'Modifica Progetto';

            let itemsHTML = '';
            proyectos.forEach(p => {
                const onclickFn = isDelete ?
                    'eliminarProyecto(' + p.id + ', \'' + p.nombre + '\', this)' :
                    'editarProyecto(' + p.id + ', \'' + p.nombre + '\', this)';

                itemsHTML += `
            <button onclick="${onclickFn}"
                style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;border:1px solid var(--incoming-chat-border);background:transparent;cursor:pointer;color:var(--text-color);font-family:'Poppins',sans-serif;font-size:13px;transition:all 0.2s;text-align:left;width:100%;"
                onmouseover="this.style.borderColor='${color}';this.style.background='${color}15'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='transparent'">
                <span class="material-symbols-rounded" style="color:#f59e0b;font-size:20px;flex-shrink:0;">folder</span>
                <span style="flex:1;">${p.nombre}</span>
                <span class="material-symbols-rounded" style="font-size:16px;color:${color};">${icono}</span>
            </button>
        `;
            });

            const div = document.createElement('div');
            div.className = 'chat-form-center';
            div.innerHTML = `
        <div class="chat-form-card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <h4 style="font-size:14px;font-weight:600;color:var(--text-color);margin:0;display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="color:${color};font-size:18px;">${icono}</span>
                    ${titulo}
                </h4>
                <button onclick="this.closest('.chat-form-center').remove()"
                    style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;"
                    onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--icon-color)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">close</span>
                </button>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                ${itemsHTML}
            </div>
        </div>
    `;

            div.style.opacity = '0';
            div.style.transform = 'translateY(10px)';
            div.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            chatContainer.appendChild(div);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            setTimeout(() => {
                div.style.opacity = '1';
                div.style.transform = 'translateY(0)';
            }, 30);
        }

        function editarProyecto(id, nombreActual, btn) {
            const card = btn.closest('.chat-form-card');
            card.innerHTML = `
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
            <span class="material-symbols-rounded" style="color:#6366f1;font-size:18px;">edit</span>
            <h4 style="font-size:14px;font-weight:600;color:var(--text-color);margin:0;">Modifica Progetto</h4>
        </div>
        <div style="margin-bottom:12px;">
            <label style="font-size:12px;color:var(--icon-color);display:block;margin-bottom:4px;">Nuovo nome *</label>
            <input id="edit-proy-nombre" type="text" value="${nombreActual}"
                style="width:100%;background:var(--outgoing-chat-bg);border:1px solid #6366f1;border-radius:8px;padding:10px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;">
        </div>
        <div style="display:flex;gap:8px;">
            <button onclick="guardarEdicionProyecto(${id}, this)"
                style="flex:1;background:#6366f1;color:#fff;border:none;border-radius:8px;padding:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;">
                Salva
            </button>
            <button onclick="this.closest('.chat-form-center').remove()"
                style="background:transparent;color:var(--icon-color);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:10px 16px;font-size:13px;cursor:pointer;font-family:'Poppins',sans-serif;">
                Annulla
            </button>
        </div>
    `;
            setTimeout(() => document.getElementById('edit-proy-nombre')?.focus(), 100);
        }

        async function guardarEdicionProyecto(id, btn) {
            const nombre = document.getElementById('edit-proy-nombre')?.value.trim();
            if (!nombre) return;

            btn.disabled = true;
            btn.textContent = 'Salvataggio...';

            const res = await fetch(`/proyectos/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    nombre
                })
            });

            const data = await res.json();
            btn.closest('.chat-form-center').remove();

            if (data.success) {
                // Actualizar nav lateral
                document.querySelectorAll('.nav-proyecto-item').forEach(item => {
                    if (item.dataset.id == id) {
                        item.dataset.nombre = nombre.toLowerCase();
                        item.querySelector('p').textContent = nombre;
                    }
                });
                agregarRespuestaBot(`✅ Progetto aggiornato: <strong>${nombre}</strong>`);
            }
        }

        async function eliminarProyecto(id, nombre, btn) {
            const result = await Swal.fire({
                title: 'Eliminare il progetto?',
                html: `Stai per eliminare <strong>${nombre}</strong>. Questa azione non può essere annullata.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: 'transparent',
                confirmButtonText: 'Sì, elimina',
                cancelButtonText: 'Annulla',
                background: 'var(--incoming-chat-bg)',
                color: 'var(--text-color)',
                customClass: {
                    cancelButton: 'swal-cancel-custom'
                }
            });

            if (!result.isConfirmed) return;

            const res = await fetch(`/proyectos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await res.json();
            btn.closest('.chat-form-center').remove();

            if (data.success) {
                // Eliminar del nav
                document.querySelectorAll('.nav-proyecto-item').forEach(item => {
                    if (item.dataset.id == id) item.remove();
                });
                agregarRespuestaBot(`🗑️ Progetto <strong>${nombre}</strong> eliminato.`);
            }
        }

        function cerrarFormularioExito() {
            const wrapper = document.getElementById('form-wrapper-active');
            if (wrapper) {
                wrapper.style.opacity = '0';
                wrapper.style.transform = 'translateY(-10px)';
                wrapper.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                setTimeout(() => {
                    wrapper.remove();
                    formularioAbierto = false;
                }, 300);
            }
        }

        function crearProyectoChat() {
            const nombre = document.getElementById('proy-nombre')?.value.trim();
            const descripcion = document.getElementById('proy-desc')?.value.trim();

            if (!nombre) {
                document.getElementById('proy-nombre').style.borderColor = '#ef4444';
                return;
            }

            // Deshabilitar botón
            const btn = document.getElementById('form-submit-btn');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Creazione...';
            }

            fetch('/proyectos', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        nombre,
                        descripcion
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        cerrarFormularioExito();
                        // Respuesta tipo bot
                        const chatDiv = document.createElement('div');
                        chatDiv.className = 'chat incoming';
                        chatDiv.style.padding = '16px 10px';
                        chatDiv.innerHTML = `
                <div class="chat-content">
                    <div class="chat-icon">
                        <span class="material-symbols-rounded">robot_2</span>
                    </div>
                    <div class="chat-text">
                        <div style="display:flex;align-items:center;gap:10px;background:var(--incoming-chat-bg);border:1px solid #f59e0b30;border-radius:12px;padding:14px 16px;max-width:400px;">
                            <span class="material-symbols-rounded" style="color:#f59e0b;font-size:28px;">folder</span>
                            <div>
                                <p style="margin:0;font-size:13px;font-weight:600;color:var(--text-color);">${data.proyecto.nombre}</p>
                                <small style="color:var(--icon-color);font-size:11px;">✅ Progetto creato con successo</small>
                            </div>
                        </div>
                        <p style="font-size:13px;color:var(--icon-color);margin:10px 0 0;">Vuoi aggiungere una <strong style="color:var(--text-color);">Categoria</strong> a questo progetto?</p>
                        <div style="display:flex;gap:8px;margin-top:10px;">
                            <button onclick="window.abrirNuevaCategoriaChat(${data.proyecto.id}, '${data.proyecto.nombre}')"
                                style="background:#6366f120;color:#6366f1;border:1px solid #6366f140;border-radius:8px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:all 0.2s;"
                                onmouseover="this.style.background='#6366f1';this.style.color='#fff'"
                                onmouseout="this.style.background='#6366f120';this.style.color='#6366f1'">
                                + Aggiungi Categoria
                            </button>
                            <button onclick="this.closest('.chat').remove()"
                                style="background:transparent;color:var(--icon-color);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:6px 14px;font-size:12px;cursor:pointer;font-family:'Poppins',sans-serif;">
                                Dopo
                            </button>
                        </div>
                    </div>
                </div>
            `;
                        chatContainer.appendChild(chatDiv);
                        chatContainer.scrollTop = chatContainer.scrollHeight;

                        // Agregar al nav lateral
                        if (typeof window.agregarProyectoNavLateral === 'function') {
                            window.agregarProyectoNavLateral(data.proyecto);
                        }
                    } else if (data.error === 'limite_raggiunto') {
                        cerrarFormularioExito();
                        agregarRespuestaBot(`
        <div style="display:flex;align-items:flex-start;gap:10px;padding:12px;
                    background:#ef444410;border:1px solid #ef444430;border-radius:12px;">
            <span class="material-symbols-rounded" style="color:#ef4444;font-size:22px;flex-shrink:0;">block</span>
            <div>
                <p style="margin:0;font-size:13px;font-weight:600;color:#ef4444;">Limite progetti raggiunto</p>
                <p style="margin:4px 0 0;font-size:12px;color:var(--icon-color);">
                    Hai raggiunto il limite del tuo piano attuale.<br>
                    Effettua l'upgrade per aggiungere più progetti.
                </p>
                <button onclick="iniziareCheckout('Basic')"
                    style="margin-top:8px;background:#6366f1;color:#fff;border:none;border-radius:8px;
                           padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;
                           font-family:'Poppins',sans-serif;">
                    Upgrade piano →
                </button>
            </div>
        </div>`);
                    }
                })
                .catch(() => {
                    agregarRespuestaBot('Errore durante la creazione del progetto. Riprova.');
                });
        }

        function agregarRespuestaBot(texto) {
            const chatDiv = document.createElement('div');
            chatDiv.className = 'chat incoming';
            chatDiv.style.padding = '16px 10px';
            chatDiv.innerHTML = `
        <div class="chat-content">
            <div class="chat-icon"><span class="material-symbols-rounded">robot_2</span></div>
            <div class="chat-text" style="font-size:13px;">${texto}</div>
        </div>`;
            chatContainer.appendChild(chatDiv);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        //categoria
        window.abrirNuevaCategoriaChat = function(proyectoId = null, proyectoNombre = null) {
            // Si no viene proyecto, verificar si hay uno activo
            const pid = proyectoId || proyectoActivo;
            const pnombre = proyectoNombre || document.getElementById('proyecto-activo-nombre')?.textContent;

            if (!pid) {
                agregarRespuestaBot('⚠️ Seleziona prima un <strong>Progetto</strong> per aggiungere una categoria.');
                return;
            }

            mostrarFormularioChat({
                icon: 'category',
                iconColor: '#6366f1',
                title: 'Nuova Categoria',
                fields: [{
                    id: 'cat-nombre',
                    label: 'Nome della categoria',
                    placeholder: 'Es. Documenti Casa, Servizi Pubblici...',
                    required: true
                }],
                submitLabel: 'Crea Categoria',
                onSubmit: () => crearCategoriaChat(pid, pnombre)
            });
        }

        function crearCategoriaChat(proyectoId, proyectoNombre) {
            const nombre = document.getElementById('cat-nombre')?.value.trim();

            if (!nombre) {
                document.getElementById('cat-nombre').style.borderColor = '#ef4444';
                return;
            }

            const btn = document.getElementById('form-submit-btn');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Creazione...';
            }

            fetch('/categorias', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        nombre,
                        proyecto_id: proyectoId
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        cerrarFormularioExito();
                        setTimeout(() => {
                            const chatDiv = document.createElement('div');
                            chatDiv.className = 'chat incoming';
                            chatDiv.style.padding = '16px 10px';
                            chatDiv.innerHTML = `
                    <div class="chat-content">
                        <div class="chat-icon">
                            <span class="material-symbols-rounded">robot_2</span>
                        </div>
                        <div class="chat-text">
                            <div style="background:var(--incoming-chat-bg);border:1px solid #6366f130;border-radius:12px;padding:14px 16px;max-width:400px;">
                                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                                    <span class="material-symbols-rounded" style="color:#6366f1;font-size:28px;">category</span>
                                    <div>
                                        <p style="margin:0;font-size:13px;font-weight:600;color:var(--text-color);">${data.categoria.nombre}</p>
                                        <small style="color:var(--icon-color);font-size:11px;">✅ Categoria creata in <strong>${proyectoNombre}</strong></small>
                                    </div>
                                </div>
                            </div>
                            <p style="font-size:13px;color:var(--icon-color);margin:10px 0 0;">Vuoi aggiungere una <strong style="color:var(--text-color);">Tipologia</strong> a questa categoria?</p>
                            <div style="display:flex;gap:8px;margin-top:10px;">
                                <button onclick="window.abrirNuovaTipologiaChat(${data.categoria.id}, '${data.categoria.nombre}')"
                                    style="background:#10b98120;color:#10b981;border:1px solid #10b98140;border-radius:8px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:all 0.2s;"
                                    onmouseover="this.style.background='#10b981';this.style.color='#fff'"
                                    onmouseout="this.style.background='#10b98120';this.style.color='#10b981'">
                                    + Aggiungi Tipologia
                                </button>
                                <button onclick="this.closest('.chat').remove()"
                                    style="background:transparent;color:var(--icon-color);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:6px 14px;font-size:12px;cursor:pointer;font-family:'Poppins',sans-serif;">
                                    Dopo
                                </button>
                            </div>
                        </div>
                    </div>`;
                            chatDiv.style.opacity = '0';
                            chatDiv.style.transform = 'translateY(10px)';
                            chatDiv.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            chatContainer.appendChild(chatDiv);
                            chatContainer.scrollTop = chatContainer.scrollHeight;
                            setTimeout(() => {
                                chatDiv.style.opacity = '1';
                                chatDiv.style.transform = 'translateY(0)';
                            }, 30);
                        }, 350);

                    } else if (data.error === 'limite_raggiunto') {
                        cerrarFormularioExito();
                        agregarRespuestaBot(`
                <div style="display:flex;align-items:flex-start;gap:10px;padding:12px;
                            background:#ef444410;border:1px solid #ef444430;border-radius:12px;">
                    <span class="material-symbols-rounded" style="color:#ef4444;font-size:22px;flex-shrink:0;">block</span>
                    <div>
                        <p style="margin:0;font-size:13px;font-weight:600;color:#ef4444;">Limite categorie raggiunto</p>
                        <p style="margin:4px 0 0;font-size:12px;color:var(--icon-color);">
                            Hai raggiunto il limite del tuo piano attuale.<br>
                            Effettua l'upgrade per aggiungere più categorie.
                        </p>
                        <button onclick="iniziareCheckout('Basic')"
                            style="margin-top:8px;background:#6366f1;color:#fff;border:none;border-radius:8px;
                                   padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;
                                   font-family:'Poppins',sans-serif;">
                            Upgrade piano →
                        </button>
                    </div>
                </div>`);
                    }
                })
                .catch(() => {
                    agregarRespuestaBot('Errore durante la creazione della categoria. Riprova.');
                    formularioAbierto = false;
                });
        }

        async function accionProyecto(accion) {
            if (accion === 'crea') {
                mostrarFormularioChat({
                    icon: 'create_new_folder',
                    iconColor: '#f59e0b',
                    title: 'Nuovo Progetto',
                    fields: [{
                            id: 'proy-nombre',
                            label: 'Nome del progetto',
                            placeholder: 'Es. Casa Milano...',
                            required: true
                        },
                        {
                            id: 'proy-desc',
                            label: 'Descrizione',
                            placeholder: 'Opzionale...',
                            required: false
                        }
                    ],
                    submitLabel: 'Crea Progetto',
                    onSubmit: crearProyectoChat
                });
                return;
            }

            const res = await fetch('/proyectos');
            const proyectos = await res.json();

            if (proyectos.length === 0) {
                agregarRespuestaBot('Non hai ancora nessun progetto. <strong>Creane uno prima!</strong>');
                return;
            }

            if (accion === 'edita') {
                mostrarListaProyectosChat(proyectos, 'edit');
            } else if (accion === 'elimina') {
                const sinCategorias = proyectos.filter(p => p.categorias_count === 0);
                if (sinCategorias.length === 0) {
                    agregarRespuestaBot(
                        '⚠️ Tutti i progetti hanno categorie associate. <strong>Elimina prima le categorie</strong>.'
                    );
                    return;
                }
                mostrarListaProyectosChat(sinCategorias, 'delete');
            }
        }

        // ============ CATEGORIAS ============

        window.abrirGestionCategoriasChat = function() {
            mostrarAccionesChat({
                titulo: 'Gestisci Categorie',
                acciones: [{
                        icon: 'add_circle',
                        color: '#6366f1',
                        label: 'Crea',
                        onclick: "accionCategoria('crea')"
                    },
                    {
                        icon: 'edit',
                        color: '#f59e0b',
                        label: 'Modifica',
                        onclick: "accionCategoria('edita')"
                    },
                    {
                        icon: 'delete',
                        color: '#ef4444',
                        label: 'Elimina',
                        onclick: "accionCategoria('elimina')"
                    }
                ]
            });
        }

        async function accionCategoria(accion) {
            if (accion === 'crea') {
                // Necesita proyecto primero
                const proyectos = await fetchProyectos();
                if (!proyectos) return;

                mostrarListaProyectosParaCategoria(proyectos, (pid, pnombre) => {
                    cerrarTodosLosPaneles();
                    window.abrirNuevaCategoriaChat(pid, pnombre);
                });
                return;
            }

            // Edita / Elimina: primero selecciona proyecto
            const proyectos = await fetchProyectos();
            if (!proyectos) return;

            mostrarListaProyectosParaCategoria(proyectos, async (pid, pnombre) => {
                cerrarTodosLosPaneles();
                const res = await fetch(`/proyectos/${pid}/categorias`);
                const categorias = await res.json();

                if (!categorias.length) {
                    agregarRespuestaBot(
                        `⚠️ Il progetto <strong>${pnombre}</strong> non ha ancora categorie.`);
                    return;
                }

                if (accion === 'edita') {
                    mostrarListaCategoriasChat(categorias, 'edit');
                } else {
                    mostrarListaCategoriasChat(categorias, 'delete');
                }
            });
        }

        async function fetchProyectos() {
            const res = await fetch('/proyectos');
            const proyectos = await res.json();
            if (!proyectos.length) {
                agregarRespuestaBot('⚠️ Non hai ancora nessun progetto. <strong>Creane uno prima!</strong>');
                return null;
            }
            return proyectos;
        }

        function mostrarListaProyectosParaCategoria(proyectos, onSelect) {
            cerrarTodosLosPaneles();

            let itemsHTML = '';
            proyectos.forEach((p, i) => {
                itemsHTML += `
            <button onclick="seleccionarProyectoParaCategoria(${i})"
                data-idx="${i}"
                style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;border:1px solid var(--incoming-chat-border);background:transparent;cursor:pointer;color:var(--text-color);font-family:'Poppins',sans-serif;font-size:13px;transition:all 0.2s;text-align:left;width:100%;"
                onmouseover="this.style.borderColor='#6366f1';this.style.background='#6366f115'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='transparent'">
                <span class="material-symbols-rounded" style="color:#f59e0b;font-size:20px;flex-shrink:0;">folder</span>
                <span style="flex:1;">${p.nombre}</span>
                <span class="material-symbols-rounded" style="font-size:16px;color:var(--icon-color);">chevron_right</span>
            </button>`;
            });

            const div = document.createElement('div');
            div.className = 'chat-form-center';
            div.id = 'lista-proyectos-cat';
            div.innerHTML = `
        <div class="chat-form-card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <h4 style="font-size:14px;font-weight:600;color:var(--text-color);margin:0;display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="color:#6366f1;font-size:18px;">folder</span>
                    Seleziona Progetto
                </h4>
                <button onclick="this.closest('.chat-form-center').remove()"
                    style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;"
                    onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--icon-color)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">close</span>
                </button>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                ${itemsHTML}
            </div>
        </div>`;

            // Guardamos el callback y los datos en el div para acceder desde los botones
            div._proyectos = proyectos;
            div._onSelect = onSelect;

            div.style.opacity = '0';
            div.style.transform = 'translateY(10px)';
            div.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            chatContainer.appendChild(div);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            setTimeout(() => {
                div.style.opacity = '1';
                div.style.transform = 'translateY(0)';
            }, 30);
        }

        function seleccionarProyectoParaCategoria(idx) {
            const div = document.getElementById('lista-proyectos-cat');
            if (!div) return;
            const proyecto = div._proyectos[idx];
            const cb = div._onSelect;
            div.remove();
            cb(proyecto.id, proyecto.nombre);
        }

        function mostrarListaCategoriasChat(categorias, accion) {
            cerrarTodosLosPaneles();
            const isDelete = accion === 'delete';
            const color = isDelete ? '#ef4444' : '#f59e0b';
            const icono = isDelete ? 'delete' : 'edit';
            const titulo = isDelete ? 'Elimina Categoria' : 'Modifica Categoria';

            let itemsHTML = '';
            categorias.forEach(c => {
                const onclickFn = isDelete ?
                    `eliminarCategoria(${c.id}, '${c.nombre}', this)` :
                    `editarCategoria(${c.id}, '${c.nombre}', this)`;

                itemsHTML += `
            <button onclick="${onclickFn}"
                style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;border:1px solid var(--incoming-chat-border);background:transparent;cursor:pointer;color:var(--text-color);font-family:'Poppins',sans-serif;font-size:13px;transition:all 0.2s;text-align:left;width:100%;"
                onmouseover="this.style.borderColor='${color}';this.style.background='${color}15'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='transparent'">
                <span class="material-symbols-rounded" style="color:#6366f1;font-size:20px;flex-shrink:0;">category</span>
                <span style="flex:1;">${c.nombre}</span>
                <span class="material-symbols-rounded" style="font-size:16px;color:${color};">${icono}</span>
            </button>`;
            });

            const div = document.createElement('div');
            div.className = 'chat-form-center';
            div.innerHTML = `
        <div class="chat-form-card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <h4 style="font-size:14px;font-weight:600;color:var(--text-color);margin:0;display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="color:${color};font-size:18px;">${icono}</span>
                    ${titulo}
                </h4>
                <button onclick="this.closest('.chat-form-center').remove()"
                    style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;"
                    onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--icon-color)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">close</span>
                </button>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">
                ${itemsHTML}
            </div>
        </div>`;

            div.style.opacity = '0';
            div.style.transform = 'translateY(10px)';
            div.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            chatContainer.appendChild(div);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            setTimeout(() => {
                div.style.opacity = '1';
                div.style.transform = 'translateY(0)';
            }, 30);
        }

        function editarCategoria(id, nombreActual, btn) {
            const card = btn.closest('.chat-form-card');
            card.innerHTML = `
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
            <span class="material-symbols-rounded" style="color:#f59e0b;font-size:18px;">edit</span>
            <h4 style="font-size:14px;font-weight:600;color:var(--text-color);margin:0;">Modifica Categoria</h4>
        </div>
        <div style="margin-bottom:12px;">
            <label style="font-size:12px;color:var(--icon-color);display:block;margin-bottom:4px;">Nuovo nome *</label>
            <input id="edit-cat-nombre" type="text" value="${nombreActual}"
                style="width:100%;background:var(--outgoing-chat-bg);border:1px solid #f59e0b;border-radius:8px;padding:10px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;">
        </div>
        <div style="display:flex;gap:8px;">
            <button onclick="guardarEdicionCategoria(${id}, this)"
                style="flex:1;background:#6366f1;color:#fff;border:none;border-radius:8px;padding:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:background 0.2s;"
                onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                Salva
            </button>
            <button onclick="this.closest('.chat-form-center').remove()"
                style="background:transparent;color:var(--icon-color);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:10px 16px;font-size:13px;cursor:pointer;font-family:'Poppins',sans-serif;">
                Annulla
            </button>
        </div>`;
            setTimeout(() => document.getElementById('edit-cat-nombre')?.focus(), 100);
        }

        async function guardarEdicionCategoria(id, btn) {
            const nombre = document.getElementById('edit-cat-nombre')?.value.trim();
            if (!nombre) return;

            btn.disabled = true;
            btn.textContent = 'Salvataggio...';

            const res = await fetch(`/categorias/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    nombre
                })
            });

            const data = await res.json();
            btn.closest('.chat-form-center').remove();

            if (data.success) {
                agregarRespuestaBot(`✅ Categoria aggiornata: <strong>${nombre}</strong>`);
            }
        }

        async function eliminarCategoria(id, nombre, btn) {
            const result = await Swal.fire({
                title: 'Eliminare la categoria?',
                html: `Stai per eliminare <strong>${nombre}</strong>. Questa azione non può essere annullata.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: 'transparent',
                confirmButtonText: 'Sì, elimina',
                cancelButtonText: 'Annulla',
                background: 'var(--incoming-chat-bg)',
                color: 'var(--text-color)',
                customClass: {
                    cancelButton: 'swal-cancel-custom'
                }
            });

            if (!result.isConfirmed) return;

            const res = await fetch(`/categorias/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await res.json();
            btn.closest('.chat-form-center').remove();

            if (data.success) {
                agregarRespuestaBot(`🗑️ Categoria <strong>${nombre}</strong> eliminata.`);
            }
        }

        // ============ TIPOLOGIE ============

        window.abrirGestionTipologieChat = function() {
            mostrarAccionesChat({
                titulo: 'Gestisci Tipologie',
                acciones: [{
                        icon: 'add_circle',
                        color: '#10b981',
                        label: 'Crea',
                        onclick: "accionTipologia('crea')"
                    },
                    {
                        icon: 'edit',
                        color: '#f59e0b',
                        label: 'Modifica',
                        onclick: "accionTipologia('edita')"
                    },
                    {
                        icon: 'delete',
                        color: '#ef4444',
                        label: 'Elimina',
                        onclick: "accionTipologia('elimina')"
                    }
                ]
            });
        }

        async function accionTipologia(accion) {
            const proyectos = await fetchProyectos();
            if (!proyectos) return;

            mostrarListaProyectosParaTipologia(proyectos, async (pid, pnombre) => {
                cerrarTodosLosPaneles();

                const res = await fetch(`/proyectos/${pid}/categorias`);
                const categorias = await res.json();

                if (!categorias.length) {
                    agregarRespuestaBot(
                        `⚠️ Il progetto <strong>${pnombre}</strong> non ha ancora categorie.`);
                    return;
                }

                mostrarListaCategoriasParaTipologia(categorias, async (cid, cnombre) => {
                    cerrarTodosLosPaneles();

                    if (accion === 'crea') {
                        window.abrirNuovaTipologiaChat(cid, cnombre);
                        return;
                    }

                    const res2 = await fetch(`/categorias/${cid}/tipologias`);
                    const tipologias = await res2.json();

                    if (!tipologias.length) {
                        agregarRespuestaBot(
                            `⚠️ La categoria <strong>${cnombre}</strong> non ha ancora tipologie.`
                        );
                        return;
                    }

                    if (accion === 'edita') {
                        mostrarListaTipologiasChat(tipologias, 'edit');
                    } else {
                        mostrarListaTipologiasChat(tipologias, 'delete');
                    }
                });
            });
        }

        function mostrarListaProyectosParaTipologia(proyectos, onSelect) {
            cerrarTodosLosPaneles();

            let itemsHTML = '';
            proyectos.forEach((p, i) => {
                itemsHTML += `
            <button onclick="seleccionarProyectoParaTipologia(${i})"
                data-idx="${i}"
                style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;border:1px solid var(--incoming-chat-border);background:transparent;cursor:pointer;color:var(--text-color);font-family:'Poppins',sans-serif;font-size:13px;transition:all 0.2s;text-align:left;width:100%;"
                onmouseover="this.style.borderColor='#10b981';this.style.background='#10b98115'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='transparent'">
                <span class="material-symbols-rounded" style="color:#f59e0b;font-size:20px;flex-shrink:0;">folder</span>
                <span style="flex:1;">${p.nombre}</span>
                <span class="material-symbols-rounded" style="font-size:16px;color:var(--icon-color);">chevron_right</span>
            </button>`;
            });

            const div = document.createElement('div');
            div.className = 'chat-form-center';
            div.id = 'lista-proyectos-tip';
            div.innerHTML = `
        <div class="chat-form-card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <h4 style="font-size:14px;font-weight:600;color:var(--text-color);margin:0;display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="color:#10b981;font-size:18px;">folder</span>
                    Seleziona Progetto
                </h4>
                <button onclick="this.closest('.chat-form-center').remove()"
                    style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;"
                    onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--icon-color)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">close</span>
                </button>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">${itemsHTML}</div>
        </div>`;

            div._proyectos = proyectos;
            div._onSelect = onSelect;

            div.style.opacity = '0';
            div.style.transform = 'translateY(10px)';
            div.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            chatContainer.appendChild(div);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            setTimeout(() => {
                div.style.opacity = '1';
                div.style.transform = 'translateY(0)';
            }, 30);
        }

        function seleccionarProyectoParaTipologia(idx) {
            const div = document.getElementById('lista-proyectos-tip');
            if (!div) return;
            const proyecto = div._proyectos[idx];
            const cb = div._onSelect;
            div.remove();
            cb(proyecto.id, proyecto.nombre);
        }

        function mostrarListaCategoriasParaTipologia(categorias, onSelect) {
            cerrarTodosLosPaneles();

            let itemsHTML = '';
            categorias.forEach((c, i) => {
                itemsHTML += `
            <button onclick="seleccionarCategoriaParaTipologia(${i})"
                data-idx="${i}"
                style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;border:1px solid var(--incoming-chat-border);background:transparent;cursor:pointer;color:var(--text-color);font-family:'Poppins',sans-serif;font-size:13px;transition:all 0.2s;text-align:left;width:100%;"
                onmouseover="this.style.borderColor='#10b981';this.style.background='#10b98115'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='transparent'">
                <span class="material-symbols-rounded" style="color:#6366f1;font-size:20px;flex-shrink:0;">category</span>
                <span style="flex:1;">${c.nombre}</span>
                <span class="material-symbols-rounded" style="font-size:16px;color:var(--icon-color);">chevron_right</span>
            </button>`;
            });

            const div = document.createElement('div');
            div.className = 'chat-form-center';
            div.id = 'lista-categorias-tip';
            div.innerHTML = `
        <div class="chat-form-card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <h4 style="font-size:14px;font-weight:600;color:var(--text-color);margin:0;display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="color:#10b981;font-size:18px;">category</span>
                    Seleziona Categoria
                </h4>
                <button onclick="this.closest('.chat-form-center').remove()"
                    style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;"
                    onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--icon-color)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">close</span>
                </button>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">${itemsHTML}</div>
        </div>`;

            div._categorias = categorias;
            div._onSelect = onSelect;

            div.style.opacity = '0';
            div.style.transform = 'translateY(10px)';
            div.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            chatContainer.appendChild(div);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            setTimeout(() => {
                div.style.opacity = '1';
                div.style.transform = 'translateY(0)';
            }, 30);
        }

        function seleccionarCategoriaParaTipologia(idx) {
            const div = document.getElementById('lista-categorias-tip');
            if (!div) return;
            const categoria = div._categorias[idx];
            const cb = div._onSelect;
            div.remove();
            cb(categoria.id, categoria.nombre);
        }

        function mostrarListaTipologiasChat(tipologias, accion) {
            cerrarTodosLosPaneles();
            const isDelete = accion === 'delete';
            const color = isDelete ? '#ef4444' : '#f59e0b';
            const icono = isDelete ? 'delete' : 'edit';
            const titulo = isDelete ? 'Elimina Tipologia' : 'Modifica Tipologia';

            let itemsHTML = '';
            tipologias.forEach(t => {
                const onclickFn = isDelete ?
                    `eliminarTipologia(${t.id}, '${t.nombre}', this)` :
                    `editarTipologia(${t.id}, '${t.nombre}', this)`;

                itemsHTML += `
            <button onclick="${onclickFn}"
                style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;border:1px solid var(--incoming-chat-border);background:transparent;cursor:pointer;color:var(--text-color);font-family:'Poppins',sans-serif;font-size:13px;transition:all 0.2s;text-align:left;width:100%;"
                onmouseover="this.style.borderColor='${color}';this.style.background='${color}15'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='transparent'">
                <span class="material-symbols-rounded" style="color:#10b981;font-size:20px;flex-shrink:0;">style</span>
                <span style="flex:1;">${t.nombre}</span>
                <span class="material-symbols-rounded" style="font-size:16px;color:${color};">${icono}</span>
            </button>`;
            });

            const div = document.createElement('div');
            div.className = 'chat-form-center';
            div.innerHTML = `
        <div class="chat-form-card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <h4 style="font-size:14px;font-weight:600;color:var(--text-color);margin:0;display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-rounded" style="color:${color};font-size:18px;">${icono}</span>
                    ${titulo}
                </h4>
                <button onclick="this.closest('.chat-form-center').remove()"
                    style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;"
                    onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--icon-color)'">
                    <span class="material-symbols-rounded" style="font-size:18px;">close</span>
                </button>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">${itemsHTML}</div>
        </div>`;

            div.style.opacity = '0';
            div.style.transform = 'translateY(10px)';
            div.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            chatContainer.appendChild(div);
            chatContainer.scrollTop = chatContainer.scrollHeight;
            setTimeout(() => {
                div.style.opacity = '1';
                div.style.transform = 'translateY(0)';
            }, 30);
        }

        function editarTipologia(id, nombreActual, btn) {
            const card = btn.closest('.chat-form-card');
            card.innerHTML = `
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
            <span class="material-symbols-rounded" style="color:#f59e0b;font-size:18px;">edit</span>
            <h4 style="font-size:14px;font-weight:600;color:var(--text-color);margin:0;">Modifica Tipologia</h4>
        </div>
        <div style="margin-bottom:12px;">
            <label style="font-size:12px;color:var(--icon-color);display:block;margin-bottom:4px;">Nuovo nome *</label>
            <input id="edit-tip-nombre" type="text" value="${nombreActual}"
                style="width:100%;background:var(--outgoing-chat-bg);border:1px solid #f59e0b;border-radius:8px;padding:10px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;">
        </div>
        <div style="display:flex;gap:8px;">
            <button onclick="guardarEdicionTipologia(${id}, this)"
                style="flex:1;background:#6366f1;color:#fff;border:none;border-radius:8px;padding:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:background 0.2s;"
                onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                Salva
            </button>
            <button onclick="this.closest('.chat-form-center').remove()"
                style="background:transparent;color:var(--icon-color);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:10px 16px;font-size:13px;cursor:pointer;font-family:'Poppins',sans-serif;">
                Annulla
            </button>
        </div>`;
            setTimeout(() => document.getElementById('edit-tip-nombre')?.focus(), 100);
        }

        async function guardarEdicionTipologia(id, btn) {
            const nombre = document.getElementById('edit-tip-nombre')?.value.trim();
            if (!nombre) return;

            btn.disabled = true;
            btn.textContent = 'Salvataggio...';

            const res = await fetch(`/tipologias/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    nombre
                })
            });

            const data = await res.json();
            btn.closest('.chat-form-center').remove();

            if (data.success) {
                agregarRespuestaBot(`✅ Tipologia aggiornata: <strong>${nombre}</strong>`);
            }
        }

        async function eliminarTipologia(id, nombre, btn) {
            const result = await Swal.fire({
                title: 'Eliminare la tipologia?',
                html: `Stai per eliminare <strong>${nombre}</strong>. Questa azione non può essere annullata.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: 'transparent',
                confirmButtonText: 'Sì, elimina',
                cancelButtonText: 'Annulla',
                background: 'var(--incoming-chat-bg)',
                color: 'var(--text-color)',
                customClass: {
                    cancelButton: 'swal-cancel-custom'
                }
            });

            if (!result.isConfirmed) return;

            const res = await fetch(`/tipologias/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const data = await res.json();
            btn.closest('.chat-form-center').remove();

            if (data.success) {
                agregarRespuestaBot(`🗑️ Tipologia <strong>${nombre}</strong> eliminata.`);
            }
        }

        // Función llamada desde el flujo de creación de categoría
        window.abrirNuovaTipologiaChat = function(categoriaId = null, categoriaNombre = null) {
            if (!categoriaId) {
                agregarRespuestaBot('⚠️ Seleziona prima una <strong>Categoria</strong> per aggiungere una tipologia.');
                return;
            }

            mostrarFormularioChat({
                icon: 'style',
                iconColor: '#10b981',
                title: 'Nuova Tipologia',
                fields: [{
                    id: 'tip-nombre',
                    label: 'Nome della tipologia',
                    placeholder: 'Es. Contratto, Fattura, Ricevuta...',
                    required: true
                }],
                submitLabel: 'Crea Tipologia',
                onSubmit: () => crearTipologiaChat(categoriaId, categoriaNombre)
            });
        }

        function crearTipologiaChat(categoriaId, categoriaNombre) {
            const nombre = document.getElementById('tip-nombre')?.value.trim();

            if (!nombre) {
                document.getElementById('tip-nombre').style.borderColor = '#ef4444';
                return;
            }

            const btn = document.getElementById('form-submit-btn');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Creazione...';
            }

            fetch('/tipologias', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        nombre,
                        categoria_id: categoriaId
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        cerrarFormularioExito();

                        setTimeout(() => {
                            const chatDiv = document.createElement('div');
                            chatDiv.className = 'chat incoming';
                            chatDiv.style.padding = '16px 10px';
                            chatDiv.innerHTML = `
                    <div class="chat-content">
                        <div class="chat-icon">
                            <span class="material-symbols-rounded">robot_2</span>
                        </div>
                        <div class="chat-text">
                            <div style="background:var(--incoming-chat-bg);border:1px solid #10b98130;border-radius:12px;padding:14px 16px;max-width:400px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <span class="material-symbols-rounded" style="color:#10b981;font-size:28px;">style</span>
                                    <div>
                                        <p style="margin:0;font-size:13px;font-weight:600;color:var(--text-color);">${data.tipologia.nombre}</p>
                                        <small style="color:var(--icon-color);font-size:11px;">✅ Tipologia creata in <strong>${categoriaNombre}</strong></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;
                            chatDiv.style.opacity = '0';
                            chatDiv.style.transform = 'translateY(10px)';
                            chatDiv.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            chatContainer.appendChild(chatDiv);
                            chatContainer.scrollTop = chatContainer.scrollHeight;
                            setTimeout(() => {
                                chatDiv.style.opacity = '1';
                                chatDiv.style.transform = 'translateY(0)';
                            }, 30);
                        }, 350);
                    } else if (data.error === 'limite_raggiunto') {
                        cerrarFormularioExito();
                        agregarRespuestaBot(`
                            <div style="display:flex;align-items:flex-start;gap:10px;padding:12px;
                                        background:#ef444410;border:1px solid #ef444430;border-radius:12px;">
                                <span class="material-symbols-rounded" style="color:#ef4444;font-size:22px;flex-shrink:0;">block</span>
                                <div>
                                    <p style="margin:0;font-size:13px;font-weight:600;color:#ef4444;">Limite tipologie raggiunto</p>
                                    <p style="margin:4px 0 0;font-size:12px;color:var(--icon-color);">
                                        Hai raggiunto il limite del tuo piano attuale.<br>
                                        Effettua l'upgrade per aggiungere più tipologie.
                                    </p>
                                    <button onclick="iniziareCheckout('Basic')"
                                        style="margin-top:8px;background:#6366f1;color:#fff;border:none;border-radius:8px;
                                            padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;
                                            font-family:'Poppins',sans-serif;">
                                        Upgrade piano →
                                    </button>
                                </div>
                            </div>`);
                    }
                })
                .catch(() => {
                    agregarRespuestaBot('Errore durante la creazione della tipologia. Riprova.');
                    formularioAbierto = false;
                });
        }

        // ============ UPLOAD PANEL ============

        let uploadFilesQueue = [];
        let uploadProyectoId = null;

        function abrirSelectorFiles() {
            // Verificar que haya proyecto activo en dataroom
            if (!dataroomProyectoId) {
                agregarRespuestaBot(
                    '⚠️ Apri prima un <strong>Progetto</strong> dal pannello laterale per caricare documenti.');
                return;
            }
            uploadProyectoId = dataroomProyectoId;
            document.getElementById('file-input-chat').click();
        }

        function onFilesSelected(input) {
            const files = Array.from(input.files).slice(0, 10);
            if (!files.length) return;

            uploadFilesQueue = files;
            mostrarUploadPanel(files);
            input.value = '';
        }

        function mostrarUploadPanel(files) {
            const panel = document.getElementById('upload-panel');
            const lista = document.getElementById('upload-files-lista');
            const titulo = document.getElementById('upload-panel-titulo');

            titulo.textContent = `${files.length} file selezionati — ${getNombreProyecto()}`;

            lista.innerHTML = files.map((f, i) => `
        <div id="upload-file-${i}"
            style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;background:var(--outgoing-chat-bg);">
            <span style="font-size:20px;flex-shrink:0;">${getFileIcon(f.type)}</span>
            <div style="flex:1;min-width:0;">
                <p style="margin:0;font-size:12px;font-weight:600;color:var(--text-color);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${f.name}</p>
                <p style="margin:0;font-size:11px;color:var(--icon-color);">${formatBytes(f.size)}</p>
            </div>
            <span id="upload-status-${i}"
                style="font-size:11px;font-weight:600;padding:3px 8px;border-radius:20px;background:var(--icon-hover-bg);color:var(--icon-color);flex-shrink:0;white-space:nowrap;">
                In coda
            </span>
        </div>`).join('');

            panel.style.display = 'block';
            panel.style.opacity = '0';
            panel.style.transition = 'opacity 0.3s ease';
            setTimeout(() => {
                panel.style.opacity = '1';
            }, 30);

            setTimeout(() => procesarUpload(), 500);
        }
        async function procesarUpload() {
            const totalSize = uploadFilesQueue.reduce((acc, f) => acc + f.size, 0);
            if (totalSize > 30 * 1024 * 1024) {
                agregarRespuestaBot(
                    '⚠️ La dimensione totale supera i <strong>30MB</strong>. Riduci il numero di file.');
                chiudiUploadPanel();
                return;
            }

            const formData = new FormData();
            formData.append('proyecto_id', uploadProyectoId);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            uploadFilesQueue.forEach((file, i) => {
                formData.append('archivos[]', file);
                setUploadStatus(i, 'Analisi AI...', '#f59e0b', '#f59e0b20');
            });

            try {
                // Marcar todos como "Analisi AI..."
                uploadFilesQueue.forEach((_, i) => {
                    setUploadStatus(i, 'Analisi AI...', '#f59e0b', '#f59e0b20');
                });

                const res = await fetch('/claude/analizar', {
                    method: 'POST',
                    body: formData
                });

                const data = await res.json();

                if (data.success) {
                    data.resultados.forEach((r, i) => {
                        if (r.success) {
                            setUploadStatus(i, '✓ Completato', '#10b981', '#10b98120');
                        } else {
                            setUploadStatus(i, '⚠ Errore', '#ef4444', '#ef444420');
                        }
                    });

                    const completati = data.resultados.filter(r => r.success).length;
                    const errori = data.resultados.filter(r => !r.success).length;

                    let msg =
                        `📂 <strong>${completati} documento${completati !== 1 ? 'i' : ''}</strong> analizzato${completati !== 1 ? 'i' : ''} e salvato${completati !== 1 ? 'i' : ''}.`;
                    if (errori > 0) msg +=
                        ` <span style="color:#ef4444;">${errori} con errore → Senza Categoria</span>`;
                    agregarRespuestaBot(msg);

                    // Verificar docs con scadenza
                    const conScadenza = data.resultados.filter(r => r.success && r.fecha_scadenza);
                    if (conScadenza.length > 0) {
                        setTimeout(() => mostrarDocsConScadenza(conScadenza), 800);
                    }

                    // Mostrar solo los files nuevos en el DataRoom
                    if (dataroomProyectoId && typeof mostrarFilesNuevos === 'function') {
                        setTimeout(() => mostrarFilesNuevos(data.resultados), 800);
                    }

                    setTimeout(() => chiudiUploadPanel(), 2500);
                }

            } catch (error) {
                uploadFilesQueue.forEach((_, i) => {
                    setUploadStatus(i, '⚠ Errore', '#ef4444', '#ef444420');
                });
                agregarRespuestaBot('❌ Errore durante il caricamento. Riprova.');
            }
        }

        function setUploadStatus(idx, texto, color, bg) {
            const el = document.getElementById(`upload-status-${idx}`);
            if (el) {
                el.textContent = texto;
                el.style.color = color;
                el.style.background = bg;
            }
        }

        function chiudiUploadPanel() {
            const panel = document.getElementById('upload-panel');
            panel.style.opacity = '0';
            setTimeout(() => {
                panel.style.display = 'none';
                uploadFilesQueue = [];
            }, 300);
        }

        function getNombreProyecto() {
            return document.getElementById('dataroom-titulo')?.textContent || '—';
        }

        function formatBytes(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }

        function activarBtnUpload(activo) {
            const btn = document.getElementById('btn-upload-plus');
            if (!btn) return;
            if (activo) {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
                btn.onmouseover = () => {
                    btn.style.background = 'var(--icon-hover-bg)';
                    btn.style.color = 'var(--text-color)';
                };
                btn.onmouseout = () => {
                    btn.style.background = 'transparent';
                    btn.style.color = 'var(--icon-color)';
                };
            } else {
                btn.disabled = true;
                btn.style.opacity = '0.35';
                btn.style.cursor = 'not-allowed';
                btn.onmouseover = null;
                btn.onmouseout = null;
            }
        }

        async function initCardsProgetto() {
            const cardUltimo = document.getElementById('card-ultimo-progetto');
            const cardApri = document.getElementById('card-apri-progetto');
            const nombreEl = document.getElementById('ultimo-progetto-nombre');

            const ultimo = localStorage.getItem('ultimo_proyecto');
            let tieneUltimo = false;

            if (ultimo) {
                try {
                    const data = JSON.parse(ultimo);
                    if (data.id && data.nombre && nombreEl) {
                        nombreEl.textContent = data.nombre;
                        tieneUltimo = true;
                    }
                } catch (e) {
                    tieneUltimo = false;
                }
            }

            // Ajuste de visibilidad individual
            if (cardUltimo) {
                cardUltimo.style.display = tieneUltimo ? 'block' : 'none';
                setTimeout(() => {
                    cardUltimo.style.opacity = '1';
                    cardUltimo.style.transform = 'translateY(0)';
                }, 100);
            }

            if (cardApri) {
                cardApri.style.display = 'block';
                setTimeout(() => {
                    cardApri.style.opacity = '1';
                    cardApri.style.transform = 'translateY(0)';
                }, 200);
            }
        }

        function aprireUltimoProgetto() {
            const ultimo = localStorage.getItem('ultimo_proyecto');
            if (!ultimo) return;
            try {
                const data = JSON.parse(ultimo);
                if (typeof window.seleccionarProyectoChat === 'function') {
                    window.seleccionarProyectoChat(data.id, data.nombre);
                }
            } catch (e) {}
        }

        async function confirmarCategoriasOnboarding() {
            const checkboxes = document.querySelectorAll('[id^="onb-cat-"]');
            const selezionate = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (!selezionate.length) return;

            // Enviar al chat para que Claude genere las tipologías
            const msg = `Ho scelto queste categorie: ${selezionate.join(', ')}. Crea tutto.`;
            chatInput.value = msg;
            sendMessage();
        }

        function iniciarOnboardingDiretto() {
            const defaultText = chatContainer.querySelector('.default-text');
            if (defaultText) defaultText.remove();

            const businessType = '{{ auth()->user()->business_type }}';
            const firstName = '{{ explode(' ', auth()->user()->name)[0] }}';

            // Wrapper centrato come la schermata di benvenuto
            const wrapper = document.createElement('div');
            wrapper.className = 'default-text';
            wrapper.style.cssText =
                'min-height:60vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:20px;text-align:center;';

            wrapper.innerHTML = `
        <h1 style="font-size:2.2rem;margin-bottom:10px;opacity:0;transform:translateY(12px);transition:opacity 0.5s ease,transform 0.5s ease;" id="onb-title">
            Benvenuto in Docum24, ${firstName}! 👋
        </h1>
        <p style="font-size:1rem;color:var(--icon-color);margin:0;opacity:0;transition:opacity 0.5s ease 0.2s;" id="onb-sub1">
            Ho visto che lavori nel settore <strong style="color:var(--text-color);">${businessType}</strong>.
        </p>
        <p style="font-size:1rem;color:var(--icon-color);margin:6px 0 0;opacity:0;transition:opacity 0.5s ease 0.35s;" id="onb-sub2">
            Docum24 organizza i tuoi documenti in
            <strong style="color:var(--text-color);">Progetti</strong> →
            <strong style="color:var(--text-color);">Categorie</strong> →
            <strong style="color:var(--text-color);">Tipologie</strong>.
        </p>
        <p style="font-size:1rem;color:var(--icon-color);margin:4px 0 36px;opacity:0;transition:opacity 0.5s ease 0.5s;" id="onb-sub3">
            Come vuoi iniziare?
        </p>

        <div style="display:flex;flex-direction:column;gap:14px;width:100%;max-width:480px;">

            <button onclick="avviarGuidaAI()"
                id="onb-card-1"
                style="opacity:0;transform:translateY(16px);transition:opacity 0.5s ease 0.65s,transform 0.5s ease 0.65s,border-color 0.2s,background 0.2s,box-shadow 0.2s;
                       display:flex;align-items:center;gap:16px;padding:18px 20px;
                       background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);
                       border-radius:16px;cursor:pointer;font-family:'Poppins',sans-serif;text-align:left;width:100%;"
                onmouseover="this.style.borderColor='#6366f1';this.style.background='#6366f108';this.style.boxShadow='0 8px 24px rgba(99,102,241,0.15)';this.style.transform='translateY(-2px)'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='var(--incoming-chat-bg)';this.style.boxShadow='none';this.style.transform='translateY(0)'">
                <div style="width:46px;height:46px;border-radius:13px;background:#6366f120;border:1px solid #6366f130;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span class="material-symbols-rounded" style="font-size:24px;color:#6366f1;">robot_2</span>
                </div>
                <div style="flex:1;">
                    <p style="margin:0;font-size:14px;font-weight:700;color:var(--text-color);">L'AI configura tutto per me 🚀</p>
                    <p style="margin:4px 0 0;font-size:12px;color:var(--icon-color);line-height:1.5;">Creo automaticamente progetti, categorie e tipologie<br>adatte al tuo settore in pochi secondi</p>
                </div>
                <span class="material-symbols-rounded" style="font-size:20px;color:var(--icon-color);flex-shrink:0;">arrow_forward</span>
            </button>

            <button onclick="avviarModoManuale()"
                id="onb-card-2"
                style="opacity:0;transform:translateY(16px);transition:opacity 0.5s ease 0.82s,transform 0.5s ease 0.82s,border-color 0.2s,background 0.2s,box-shadow 0.2s;
                       display:flex;align-items:center;gap:16px;padding:18px 20px;
                       background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);
                       border-radius:16px;cursor:pointer;font-family:'Poppins',sans-serif;text-align:left;width:100%;"
                onmouseover="this.style.borderColor='#f59e0b';this.style.background='#f59e0b08';this.style.boxShadow='0 8px 24px rgba(245,158,11,0.15)';this.style.transform='translateY(-2px)'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='var(--incoming-chat-bg)';this.style.boxShadow='none';this.style.transform='translateY(0)'">
                <div style="width:46px;height:46px;border-radius:13px;background:#f59e0b20;border:1px solid #f59e0b30;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span class="material-symbols-rounded" style="font-size:24px;color:#f59e0b;">tune</span>
                </div>
                <div style="flex:1;">
                    <p style="margin:0;font-size:14px;font-weight:700;color:var(--text-color);">Preferisco configurare da solo</p>
                    <p style="margin:4px 0 0;font-size:12px;color:var(--icon-color);line-height:1.5;">Creo manualmente progetti e categorie,<br>l'AI è sempre disponibile per aiutarmi</p>
                </div>
                <span class="material-symbols-rounded" style="font-size:20px;color:var(--icon-color);flex-shrink:0;">arrow_forward</span>
            </button>

        </div>`;

            chatContainer.appendChild(wrapper);
            chatContainer.scrollTop = chatContainer.scrollHeight;

            // Trigger animazioni con piccoli delay
            requestAnimationFrame(() => {
                ['onb-title', 'onb-sub1', 'onb-sub2', 'onb-sub3', 'onb-card-1', 'onb-card-2'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.style.opacity = '1';
                        el.style.transform = 'translateY(0)';
                    }
                });
            });
        }

        function avviarGuidaAI() {
            // Elimina las cards de bienvenida y arranca el flujo automático
            const businessType = '{{ auth()->user()->business_type }}';
            setTimeout(() => {
                sendQuickMessage(
                    `Il mio business è: ${businessType}. Suggeriscimi un nome per il progetto.`
                );
            }, 300);
        }

        function avviarModoManuale() {
            // Muestra mensaje explicativo y deja al usuario libre
            agregarRespuestaBot(`
        <p style="font-size:13px;margin-bottom:10px;">Perfetto! 😊 Ecco come funziona Docum24:</p>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;
                        background:var(--outgoing-chat-bg);border-radius:10px;
                        border:1px solid var(--incoming-chat-border);">
                <span style="font-size:18px;flex-shrink:0;">📁</span>
                <div>
                    <p style="margin:0;font-size:12px;font-weight:700;color:var(--text-color);">
                        1. Crea un Progetto
                    </p>
                    <p style="margin:2px 0 0;font-size:11px;color:var(--icon-color);">
                        È il contenitore principale — es. "Cliente Rossi", "Cantiere Milano"
                    </p>
                </div>
            </div>
            <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;
                        background:var(--outgoing-chat-bg);border-radius:10px;
                        border:1px solid var(--incoming-chat-border);">
                <span style="font-size:18px;flex-shrink:0;">📂</span>
                <div>
                    <p style="margin:0;font-size:12px;font-weight:700;color:var(--text-color);">
                        2. Aggiungi Categorie
                    </p>
                    <p style="margin:2px 0 0;font-size:11px;color:var(--icon-color);">
                        Sezioni dentro il progetto — es. "Contratti", "Fatture", "Corrispondenza"
                    </p>
                </div>
            </div>
            <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;
                        background:var(--outgoing-chat-bg);border-radius:10px;
                        border:1px solid var(--incoming-chat-border);">
                <span style="font-size:18px;flex-shrink:0;">🏷️</span>
                <div>
                    <p style="margin:0;font-size:12px;font-weight:700;color:var(--text-color);">
                        3. Definisci Tipologie
                    </p>
                    <p style="margin:2px 0 0;font-size:11px;color:var(--icon-color);">
                        Tipi di documento — es. "NDA", "Fattura proforma", "PEC"
                    </p>
                </div>
            </div>
        </div>
        <p style="font-size:12px;color:var(--icon-color);margin-top:10px;">
            Usa i pulsanti in alto oppure scrivimi cosa vuoi fare. Sono qui! 😊
        </p>
    `);
        }

        async function abrirDocumentoDesdeChat(docId, nombre, mimeType, proyectoId, proyectoNombre) {
            // 1. Abrir DataRoom del proyecto
            if (typeof window.seleccionarProyectoChat === 'function') {
                window.seleccionarProyectoChat(proyectoId, proyectoNombre);
            }
            // 2. Esperar que cargue el DataRoom, luego abrir visor y resaltar
            setTimeout(() => {
                // Abrir visor
                if (typeof window.openFileViewer === 'function') {
                    window.openFileViewer(docId, nombre, mimeType);
                }

                // Abrir acordeón de la categoría del documento y resaltar el item
                const docItem = document.getElementById(`doc-item-${docId}`);
                if (docItem) {
                    // Encontrar la categoría padre
                    const catContainer = docItem.closest('[id^="cat-files-"]');
                    if (catContainer) {
                        // Abrir acordeón si está cerrado
                        if (catContainer.style.maxHeight === '0px') {
                            const catId = catContainer.id.replace('cat-files-', '');
                            toggleCategoria(parseInt(catId));
                        }
                        // Scroll al documento
                        setTimeout(() => {
                            docItem.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                            // Resaltar
                            docItem.style.borderLeftColor = '#6366f1';
                            docItem.style.background = '#6366f110';
                            docItem.style.transition = 'all 0.5s ease';
                            setTimeout(() => {
                                docItem.style.borderLeftColor = 'var(--incoming-chat-border)';
                                docItem.style.background = 'transparent';
                            }, 4000);
                        }, 200);
                    }
                }
            }, 900); // un poco más que antes para que el DOM esté listo
        }
    </script>

    <!-- Modal Cronologia -->
    <div id="modal-cronologia"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:2000;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
        <div
            style="background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:18px;width:100%;max-width:680px;max-height:85vh;display:flex;flex-direction:column;margin:20px;box-shadow:0 24px 60px rgba(0,0,0,0.5);">

            <!-- Header -->
            <div
                style="padding:20px 24px;border-bottom:1px solid var(--incoming-chat-border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="material-symbols-rounded" style="color:#6366f1;font-size:22px;">history</span>
                    <h3 style="margin:0;font-size:16px;font-weight:700;color:var(--text-color);">Cronologia Attività
                    </h3>
                </div>
                <button onclick="chiudiCronologia()"
                    style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:6px;border-radius:8px;display:flex;transition:all 0.2s;"
                    onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='#ef4444'"
                    onmouseout="this.style.background='none';this.style.color='var(--icon-color)'">
                    <span class="material-symbols-rounded" style="font-size:20px;">close</span>
                </button>
            </div>

            <!-- Filtros -->
            <div
                style="padding:16px 24px;border-bottom:1px solid var(--incoming-chat-border);display:flex;gap:10px;flex-wrap:wrap;flex-shrink:0;">
                <input id="cron-search" type="text" placeholder="🔍 Cerca attività..."
                    oninput="filtrarCronologia()"
                    style="flex:1;min-width:180px;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:8px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;transition:border-color 0.2s;"
                    onfocus="this.style.borderColor='#6366f1'"
                    onblur="this.style.borderColor='var(--incoming-chat-border)'">
                <input id="cron-desde" type="date" onchange="filtrarCronologia()"
                    style="background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:8px 10px;font-size:12px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;cursor:pointer;transition:border-color 0.2s;"
                    onfocus="this.style.borderColor='#6366f1'"
                    onblur="this.style.borderColor='var(--incoming-chat-border)'">
                <input id="cron-hasta" type="date" onchange="filtrarCronologia()"
                    style="background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:8px 10px;font-size:12px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;cursor:pointer;transition:border-color 0.2s;"
                    onfocus="this.style.borderColor='#6366f1'"
                    onblur="this.style.borderColor='var(--incoming-chat-border)'">
                <button onclick="limpiarFiltrosCronologia()"
                    style="background:transparent;border:1px solid var(--incoming-chat-border);border-radius:8px;padding:8px 12px;font-size:12px;color:var(--icon-color);cursor:pointer;font-family:'Poppins',sans-serif;transition:all 0.2s;white-space:nowrap;"
                    onmouseover="this.style.borderColor='#ef4444';this.style.color='#ef4444'"
                    onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)'">
                    Pulisci
                </button>
            </div>

            <!-- Counter -->
            <div style="padding:10px 24px 0;flex-shrink:0;">
                <span id="cron-counter" style="font-size:11px;color:var(--icon-color);"></span>
            </div>

            <!-- Lista -->
            <div id="cron-lista"
                style="flex:1;overflow-y:auto;padding:12px 24px 20px;display:flex;flex-direction:column;gap:6px;">
                <div style="text-align:center;padding:40px;color:var(--icon-color);">
                    <span class="material-symbols-rounded"
                        style="font-size:36px;display:block;margin-bottom:8px;opacity:0.4;">history</span>
                    <p style="font-size:13px;">Caricamento...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let _cronDatos = [];

        const _tipoIconos = {
            progetto_creato: {
                icon: 'create_new_folder',
                color: '#f59e0b'
            },
            progetto_aggiornato: {
                icon: 'edit',
                color: '#6366f1'
            },
            progetto_eliminato: {
                icon: 'delete',
                color: '#ef4444'
            },
            categoria_creata: {
                icon: 'category',
                color: '#6366f1'
            },
            categoria_aggiornata: {
                icon: 'edit',
                color: '#6366f1'
            },
            categoria_eliminata: {
                icon: 'delete',
                color: '#ef4444'
            },
            tipologia_creata: {
                icon: 'style',
                color: '#10b981'
            },
            tipologia_aggiornata: {
                icon: 'edit',
                color: '#10b981'
            },
            tipologia_eliminata: {
                icon: 'delete',
                color: '#ef4444'
            },
            documento_caricato: {
                icon: 'upload_file',
                color: '#3b82f6'
            },
            documento_eliminato: {
                icon: 'delete',
                color: '#ef4444'
            },
        };

        function getTipoInfo(tipo) {
            return _tipoIconos[tipo] || {
                icon: 'radio_button_checked',
                color: '#8b8fa8'
            };
        }

        function formatFecha(str) {
            const d = new Date(str);
            return d.toLocaleDateString('it-IT', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) +
                ' ' + d.toLocaleTimeString('it-IT', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        }

        async function abrirCronologia() {
            const modal = document.getElementById('modal-cronologia');
            modal.style.display = 'flex';
            setTimeout(() => modal.style.opacity = '1', 10);
            await cargarCronologia();
        }

        function chiudiCronologia() {
            const modal = document.getElementById('modal-cronologia');
            modal.style.display = 'none';
        }

        // Cerrar al click fuera
        document.getElementById('modal-cronologia').addEventListener('click', function(e) {
            if (e.target === this) chiudiCronologia();
        });

        async function cargarCronologia() {
            const q = document.getElementById('cron-search').value;
            const desde = document.getElementById('cron-desde').value;
            const hasta = document.getElementById('cron-hasta').value;

            const params = new URLSearchParams();
            if (q) params.set('q', q);
            if (desde) params.set('desde', desde);
            if (hasta) params.set('hasta', hasta);

            try {
                const res = await fetch('/actividades?' + params.toString());
                _cronDatos = await res.json();
                renderCronologia(_cronDatos);
            } catch (e) {
                document.getElementById('cron-lista').innerHTML =
                    '<p style="text-align:center;color:#ef4444;font-size:13px;">Errore nel caricamento.</p>';
            }
        }

        function filtrarCronologia() {
            // Debounce ligero para el search
            clearTimeout(window._cronTimeout);
            window._cronTimeout = setTimeout(() => cargarCronologia(), 300);
        }

        function limpiarFiltrosCronologia() {
            document.getElementById('cron-search').value = '';
            document.getElementById('cron-desde').value = '';
            document.getElementById('cron-hasta').value = '';
            cargarCronologia();
        }

        function renderCronologia(items) {
            const lista = document.getElementById('cron-lista');
            const counter = document.getElementById('cron-counter');

            counter.textContent = items.length ?
                `${items.length} attività trovate` :
                '';

            if (!items.length) {
                lista.innerHTML = `
            <div style="text-align:center;padding:40px;color:var(--icon-color);">
                <span class="material-symbols-rounded" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.4;">search_off</span>
                <p style="font-size:13px;">Nessuna attività trovata</p>
            </div>`;
                return;
            }

            lista.innerHTML = items.map(a => {
                const {
                    icon,
                    color
                } = getTipoInfo(a.tipo);
                const proyecto = a.proyecto?.nombre ?
                    `<span style="font-size:11px;color:var(--icon-color);margin-top:2px;display:block;">
                   <span class="material-symbols-rounded" style="font-size:11px;vertical-align:middle;">folder</span>
                   ${a.proyecto.nombre}
               </span>` :
                    '';
                return `
        <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;border-radius:10px;border:1px solid var(--incoming-chat-border);background:var(--outgoing-chat-bg);transition:border-color 0.2s;"
            onmouseover="this.style.borderColor='${color}40'"
            onmouseout="this.style.borderColor='var(--incoming-chat-border)'">
            <div style="width:34px;height:34px;border-radius:10px;background:${color}20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span class="material-symbols-rounded" style="font-size:18px;color:${color};">${icon}</span>
            </div>
            <div style="flex:1;min-width:0;">
                <p style="margin:0;font-size:13px;color:var(--text-color);line-height:1.4;">${a.descripcion}</p>
                ${proyecto}
            </div>
            <span style="font-size:11px;color:var(--icon-color);white-space:nowrap;flex-shrink:0;margin-top:2px;">${formatFecha(a.created_at)}</span>
        </div>`;
            }).join('');
        }

        function seleccionarNombreOnboarding(nombre) {
            sendQuickMessage(nombre);
        }

        // ── CRONOLOGIA CHAT ──────────────────────────────────────
        function apriCronologiaChat() {
            const modal = document.getElementById('modal-cronologia-chat');
            modal.style.display = 'flex';
            cargarConversaciones();
        }

        function chiudiCronologiaChat() {
            document.getElementById('modal-cronologia-chat').style.display = 'none';
        }

        // Cerrar al click fuera
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('modal-cronologia-chat');
            if (modal && e.target === modal) chiudiCronologiaChat();
        });

        function cargarConversaciones() {
            const lista = document.getElementById('cronologia-chat-lista');
            const loading = document.getElementById('cronologia-chat-loading');
            if (loading) loading.style.display = 'block';

            fetch('/conversaciones', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (loading) loading.style.display = 'none';
                    lista.innerHTML = '';

                    if (!data.length) {
                        lista.innerHTML = `
                <div style="text-align:center;padding:40px;color:var(--icon-color);">
                    <span class="material-symbols-rounded" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.4;">chat_bubble_outline</span>
                    <p style="font-size:13px;">Nessuna conversazione salvata</p>
                </div>`;
                        return;
                    }

                    data.forEach(conv => {
                        const fecha = new Date(conv.ultimo_mensaje_at);
                        const fechaStr = fecha.toLocaleDateString('it-IT', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        const item = document.createElement('div');
                        item.style.cssText = `
                display:flex;align-items:center;justify-content:space-between;
                padding:12px 14px;border-radius:10px;cursor:pointer;
                border:1px solid var(--incoming-chat-border);
                background:var(--outgoing-chat-bg);
                transition:all 0.2s;`;

                        item.innerHTML = `
                <div style="flex:1;min-width:0;cursor:pointer;" onclick="restaurarConversacion('${conv.session_id}')">
                    <div style="font-size:13px;font-weight:500;color:var(--text-color);
                                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        ${conv.titulo}
                    </div>
                    <div style="font-size:11px;color:var(--icon-color);margin-top:3px;">
                        ${fechaStr}
                    </div>
                </div>
                <button onclick="eliminarConversacion(event, ${conv.id}, this)"
                    style="background:transparent;border:none;cursor:pointer;
                           color:var(--icon-color);padding:4px;margin-left:8px;
                           border-radius:6px;transition:all 0.2s;display:flex;"
                    onmouseover="this.style.color='#ef4444';this.style.background='#ef444415'"
                    onmouseout="this.style.color='var(--icon-color)';this.style.background='transparent'"
                    title="Elimina">
                    <span class="material-symbols-rounded" style="font-size:18px;">delete</span>
                </button>`;

                        item.addEventListener('mouseenter', () => {
                            item.style.borderColor = '#6366f1';
                            item.style.background = 'rgba(99,102,241,0.06)';
                        });
                        item.addEventListener('mouseleave', () => {
                            item.style.borderColor = 'var(--incoming-chat-border)';
                            item.style.background = 'var(--outgoing-chat-bg)';
                        });

                        lista.appendChild(item);
                    });
                })
                .catch(() => {
                    if (loading) loading.innerHTML =
                        '<p style="color:#ef4444;font-size:13px;text-align:center;">Errore nel caricamento</p>';
                });
        }

        async function restaurarConversacion(sessionId) {
            fetch(`/conversaciones/by-session/${sessionId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(async conv => {
                    sessionId = conv.session_id;
                    localStorage.setItem('chat_session_id', conv.session_id);

                    chatContainer.innerHTML = '';

                    for (const msg of conv.historial) {
                        if (msg.role === 'user') {
                            addUserMessage(msg.content);
                        } else if (msg.role === 'assistant') {
                            const content = typeof msg.content === 'string' ?
                                msg.content :
                                (msg.content?.message || '');
                            if (content) {
                                // Render directo sin typewriter para historial
                                const html = `
                        <div class="chat-content">
                            <div class="chat-icon"><span class="material-symbols-rounded">robot_2</span></div>
                            <div class="chat-text">${formatMarkdown(content)}</div>
                            <button onclick="copiarRisposta(this)" title="Copia risposta"
                                style="align-self:flex-end;flex-shrink:0;border:none;background:transparent;
                                       cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;
                                       display:flex;opacity:0.4;transition:all 0.2s;visibility:visible;"
                                onmouseover="this.style.opacity='1';this.style.background='var(--icon-hover-bg)'"
                                onmouseout="this.style.opacity='0.4';this.style.background='transparent'">
                                <span class="material-symbols-rounded" style="font-size:16px;">content_copy</span>
                            </button>
                        </div>`;
                                const chatDiv = document.createElement('div');
                                chatDiv.className = 'chat incoming';
                                chatDiv.innerHTML = html;
                                chatContainer.appendChild(chatDiv);
                            }
                        }
                    }

                    scrollToBottom();
                    chiudiCronologiaChat();
                    chatInput.focus();
                });
        }

        function eliminarConversacion(e, id, btn) {
            e.stopPropagation();
            const item = btn.closest('div[style]');

            fetch(`/conversaciones/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        item.style.opacity = '0';
                        item.style.transform = 'translateX(20px)';
                        item.style.transition = 'all 0.3s';
                        setTimeout(() => item.remove(), 300);
                    }
                });
        }

        function copiarRisposta(btn) {
            const text = btn.closest('.chat-content').querySelector('.chat-text')?.innerText || '';
            navigator.clipboard.writeText(text).then(() => {
                const icon = btn.querySelector('span');
                icon.textContent = 'check';
                btn.style.color = '#10b981';
                btn.style.opacity = '1';
                setTimeout(() => {
                    icon.textContent = 'content_copy';
                    btn.style.color = '';
                    btn.style.opacity = '0.4';
                }, 1500);
            });
        }

        async function abrirNotifica() {
            const res = await fetch('/notifiche');
            const notifiche = await res.json();

            if (!notifiche.length) {
                agregarRespuestaBot(`
            <div style="display:flex;align-items:center;gap:10px;padding:12px;
                        background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);
                        border-radius:12px;">
                <span class="material-symbols-rounded" style="font-size:20px;color:var(--icon-color);">notifications_off</span>
                <p style="margin:0;font-size:13px;color:var(--icon-color);">Nessuna notifica attiva.</p>
            </div>`);
                return;
            }

            // Ordenar por urgencia
            const oggi = new Date();
            oggi.setHours(0, 0, 0, 0);
            notifiche.sort((a, b) => new Date(a.fecha_scadenza) - new Date(b.fecha_scadenza));

            const listaHTML = notifiche.map(n => {
                const urg = calcularUrgencia(n.fecha_scadenza);
                const scadenzaStr = new Date(n.fecha_scadenza).toLocaleDateString('it-IT');
                const nombre = n.documento?.nombre || '—';

                return `
        <div id="notifica-row-${n.id}"
            style="display:flex;align-items:center;gap:10px;padding:10px 12px;
                   border:1px solid ${urg.border};border-radius:10px;background:${urg.bg};
                   transition:all 0.3s;">
            <span class="material-symbols-rounded"
                style="font-size:16px;color:${urg.color};flex-shrink:0;">event</span>
            <div style="flex:1;min-width:0;">
                <p style="margin:0;font-size:12px;font-weight:600;color:var(--text-color);
                          white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${nombre}</p>
                <p style="margin:0;font-size:11px;color:${urg.color};">${urg.label} · ${scadenzaStr}</p>
            </div>
            <!-- Switch -->
            <label onclick="disattivaNotifica(event, ${n.id})" style="cursor:pointer;flex-shrink:0;" title="Disattiva">
                <div style="width:36px;height:20px;border-radius:10px;background:#10b981;
                            position:relative;transition:background 0.2s;">
                    <div style="width:16px;height:16px;border-radius:50%;background:#fff;
                                position:absolute;top:2px;left:18px;transition:transform 0.2s;
                                box-shadow:0 1px 3px rgba(0,0,0,0.2);"></div>
                </div>
            </label>
        </div>`;
            }).join('');

            agregarRespuestaBot(`
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="material-symbols-rounded" style="font-size:20px;color:#f59e0b;">notifications_active</span>
                <strong style="font-size:13px;">${notifiche.length} notifica${notifiche.length > 1 ? 'e attive' : ' attiva'}</strong>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;">${listaHTML}</div>
        </div>`);
        }

        async function disattivaNotifica(e, id) {
            e.preventDefault();
            const row = document.getElementById(`notifica-row-${id}`);
            if (row) {
                row.style.opacity = '0.4';
                row.style.pointerEvents = 'none';
            }

            try {
                await fetch(`/notifiche/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                if (row) {
                    row.style.transition = 'all 0.3s';
                    row.style.maxHeight = '0';
                    row.style.padding = '0';
                    row.style.margin = '0';
                    row.style.overflow = 'hidden';
                    setTimeout(() => row.remove(), 300);
                }
                aggiornareBadgeNotifiche();
            } catch (e) {
                console.error(e);
            }
        }

        async function aggiornareBadgeNotifiche() {
            try {
                const res = await fetch('/notifiche');
                const notifiche = await res.json();
                const badge = document.getElementById('notifica-badge');
                if (!badge) return;
                if (notifiche.length > 0) {
                    badge.style.display = 'flex';
                    badge.textContent = notifiche.length;
                } else {
                    badge.style.display = 'none';
                }
            } catch (e) {}
        }

        // Cargar badge al inicio
        document.addEventListener('DOMContentLoaded', () => {
            aggiornareBadgeNotifiche();
        });

        // ── DEEP SEARCH ──────────────────────────────────────────
        let _deepSearchActivo = false;

        function toggleDeepSearch() {
            _deepSearchActivo = !_deepSearchActivo;
            const btn = document.getElementById('btn-deep-search');
            const input = document.getElementById('chat-input');

            if (_deepSearchActivo) {
                btn.style.color = '#6366f1';
                btn.style.opacity = '1';
                btn.style.background = '#6366f115';
                btn.style.borderRadius = '8px';
                input.placeholder = '🔍 DeepSearch attivo — scrivi la tua domanda...';
                input.style.borderColor = '#6366f1';
                input.style.outline = '2px solid #6366f155';
            } else {
                btn.style.color = 'var(--icon-color)';
                btn.style.opacity = '0.5';
                btn.style.background = 'transparent';
                input.placeholder = 'Scrivi il tuo messaggio qui...';
                input.style.borderColor = '';
                input.style.outline = '';
            }
        }

        // ── PANEL REFERENCIAS ────────────────────────────────────
        function abrirPanelReferencias(referencias) {
            const panel = document.getElementById('panel-referencias');
            const contenido = document.getElementById('panel-referencias-contenido');
            panel.style.right = '0px';

            // Ajustar chat
            document.getElementById('chatContainer').style.marginRight = '360px';
            document.querySelector('.typing-container').style.paddingRight = '370px';

            if (!referencias || !referencias.length) {
                contenido.innerHTML = `
            <div style="text-align:center;padding:40px;color:var(--icon-color);">
                <span class="material-symbols-rounded" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.4;">search_off</span>
                <p style="font-size:12px;">Nessuna fonte trovata</p>
            </div>`;
                return;
            }

            contenido.innerHTML = referencias.map(r => `
            <div style="padding:10px 12px;border:1px solid var(--incoming-chat-border);
                        border-radius:10px;margin-bottom:6px;cursor:pointer;
                        background:${r.tipo === 'favorita' ? '#6366f108' : 'var(--outgoing-chat-bg)'};
                        transition:all 0.2s;"
                id="ref-card-${btoa(r.url).slice(0,10)}"
                onmouseover="this.style.borderColor='#6366f1'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)'">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <span class="material-symbols-rounded"
                        style="font-size:14px;color:${r.tipo === 'favorita' ? '#6366f1' : '#10b981'};">
                        ${r.tipo === 'favorita' ? 'star' : 'language'}
                    </span>
                    <span style="font-size:12px;font-weight:600;color:var(--text-color);flex:1;">${r.nombre}</span>
                    ${r.tipo !== 'favorita' ? `
                                <button onclick="event.stopPropagation();mostrarFormGuardarUrl('${r.url}', '${r.nombre}')"
                                    style="border:none;background:#6366f115;color:#6366f1;border-radius:6px;
                                        padding:3px 8px;font-size:10px;font-weight:600;cursor:pointer;
                                        font-family:'Poppins',sans-serif;white-space:nowrap;transition:all 0.2s;"
                                    onmouseover="this.style.background='#6366f1';this.style.color='#fff'"
                                    onmouseout="this.style.background='#6366f115';this.style.color='#6366f1'">
                                    + Salva
                                </button>` : '<span style="font-size:10px;color:#6366f1;background:#6366f115;padding:2px 6px;border-radius:4px;">Salvata</span>'}
                </div>
                <a href="${r.url}" target="_blank" onclick="event.stopPropagation()"
                    style="font-size:11px;color:var(--icon-color);word-break:break-all;
                        text-decoration:none;transition:color 0.2s;"
                    onmouseover="this.style.color='#6366f1'"
                    onmouseout="this.style.color='var(--icon-color)'">${r.url}</a>
            </div>`).join('');
        }

        function chiudiPanelReferencias() {
            document.getElementById('panel-referencias').style.right = '-380px';
            document.getElementById('chatContainer').style.marginRight = '0px';
            document.querySelector('.typing-container').style.paddingRight = '10px';
        }

        function switchTab(tab) {
            const tabRef = document.getElementById('tab-referencias');
            const tabFav = document.getElementById('tab-favoritas');
            const contRef = document.getElementById('panel-referencias-contenido');
            const contFav = document.getElementById('panel-favoritas-contenido');
            const addUrl = document.getElementById('panel-add-url');

            if (tab === 'referencias') {
                tabRef.style.color = '#6366f1';
                tabRef.style.borderBottom = '2px solid #6366f1';
                tabFav.style.color = 'var(--icon-color)';
                tabFav.style.borderBottom = '2px solid transparent';
                contRef.style.display = 'block';
                contFav.style.display = 'none';
                addUrl.style.display = 'none';
            } else {
                tabFav.style.color = '#6366f1';
                tabFav.style.borderBottom = '2px solid #6366f1';
                tabRef.style.color = 'var(--icon-color)';
                tabRef.style.borderBottom = '2px solid transparent';
                contRef.style.display = 'none';
                contFav.style.display = 'block';
                addUrl.style.display = 'block';
                cargarUrlFavoritas();
            }
        }

        async function cargarUrlFavoritas() {
            const cont = document.getElementById('panel-favoritas-contenido');
            const res = await fetch('/url-favoritas');
            const urls = await res.json();

            if (!urls.length) {
                cont.innerHTML = `
            <div style="text-align:center;padding:30px;color:var(--icon-color);">
                <span class="material-symbols-rounded" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.4;">bookmark_add</span>
                <p style="font-size:12px;">Nessuna fonte salvata.<br>Aggiungine una qui sotto.</p>
            </div>`;
                return;
            }

            cont.innerHTML = urls.map(u => `
        <div style="display:flex;align-items:flex-start;gap:8px;padding:10px 12px;
                    border:1px solid var(--incoming-chat-border);border-radius:10px;margin-bottom:6px;
                    background:var(--outgoing-chat-bg);">
            <span class="material-symbols-rounded" style="font-size:16px;color:#6366f1;flex-shrink:0;margin-top:2px;">star</span>
            <div style="flex:1;min-width:0;">
                <p style="margin:0;font-size:12px;font-weight:600;color:var(--text-color);">${u.nombre}</p>
                <a href="${u.url}" target="_blank"
                    style="font-size:11px;color:var(--icon-color);word-break:break-all;text-decoration:none;"
                    onmouseover="this.style.color='#6366f1'"
                    onmouseout="this.style.color='var(--icon-color)'">${u.url}</a>
            </div>
            <button onclick="eliminarUrlFavorita(${u.id}, this)"
                style="border:none;background:transparent;cursor:pointer;color:var(--icon-color);
                       padding:2px;border-radius:4px;display:flex;flex-shrink:0;transition:all 0.2s;"
                onmouseover="this.style.color='#ef4444'"
                onmouseout="this.style.color='var(--icon-color)'">
                <span class="material-symbols-rounded" style="font-size:16px;">delete</span>
            </button>
        </div>`).join('');
        }

        async function guardarUrlFavorita() {
            const nombre = document.getElementById('add-url-nombre').value.trim();
            const url = document.getElementById('add-url-url').value.trim();
            if (!nombre || !url) return;

            const res = await fetch('/url-favoritas', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    nombre,
                    url
                })
            });
            const data = await res.json();

            if (data.success) {
                document.getElementById('add-url-nombre').value = '';
                document.getElementById('add-url-url').value = '';
                cargarUrlFavoritas();
            } else if (data.error === 'limite_raggiunto') {
                agregarRespuestaBot('⚠️ Hai raggiunto il limite di 10 fonti salvate.');
            }
        }

        async function eliminarUrlFavorita(id, btn) {
            const item = btn.closest('div[style]');
            if (item) {
                item.style.opacity = '0.4';
                item.style.pointerEvents = 'none';
            }
            await fetch(`/url-favoritas/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            cargarUrlFavoritas();
        }

        function mostrarFormGuardarUrl(url, nombreSuggerito) {
            const addUrl = document.getElementById('panel-add-url');
            const inputNombre = document.getElementById('add-url-nombre');
            const inputUrl = document.getElementById('add-url-url');

            // Cambiar a tab favoritas
            switchTab('favoritas');

            // Prellenar los campos
            inputUrl.value = url;
            inputNombre.value = nombreSuggerito || new URL(url).hostname;
            inputNombre.focus();
            inputNombre.select();

            // Highlight del form
            addUrl.style.background = '#6366f115';
            addUrl.style.transition = 'background 0.3s';
            setTimeout(() => addUrl.style.background = '', 1000);
        }

        async function toggleTeamSelector() {
            const dd = document.getElementById('team-selector-dropdown');
            if (dd.style.display === 'block') {
                dd.style.display = 'none';
                return;
            }

            // Cargar teams
            const res = await fetch('/teams');
            const data = await res.json();
            const owned = data.owned || [];
            const member = data.member || [];
            const tutti = [...owned, ...member];

            const currentTeamId = {{ auth()->user()->current_team_id ?? 'null' }};

            let html = `
        <div style="padding:8px 0;">
            <button onclick="cambiarTeamActivo(null)"
                style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 14px;
                       background:${!currentTeamId ? '#6366f115' : 'transparent'};
                       color:var(--text-color);border:none;font-family:'Poppins',sans-serif;
                       font-size:12px;cursor:pointer;transition:background 0.2s;text-align:left;"
                onmouseover="this.style.background='var(--icon-hover-bg)'"
                onmouseout="this.style.background='${!currentTeamId ? '#6366f115' : 'transparent'}'">
                <span class="material-symbols-rounded" style="font-size:16px;color:var(--icon-color);">person</span>
                <span style="flex:1;">Personale</span>
                ${!currentTeamId ? '<span class="material-symbols-rounded" style="font-size:14px;color:#6366f1;">check</span>' : ''}
            </button>`;

            if (tutti.length) {
                html += `<div style="height:1px;background:var(--incoming-chat-border);margin:4px 0;"></div>`;
                tutti.forEach(t => {
                    const isActive = t.id === currentTeamId;
                    html += `
            <button onclick="cambiarTeamActivo(${t.id})"
                style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 14px;
                       background:${isActive ? '#6366f115' : 'transparent'};
                       color:var(--text-color);border:none;font-family:'Poppins',sans-serif;
                       font-size:12px;cursor:pointer;transition:background 0.2s;text-align:left;"
                onmouseover="this.style.background='var(--icon-hover-bg)'"
                onmouseout="this.style.background='${isActive ? '#6366f115' : 'transparent'}'">
                <span class="material-symbols-rounded" style="font-size:16px;color:#6366f1;">group</span>
                <span style="flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${t.name}</span>
                ${isActive ? '<span class="material-symbols-rounded" style="font-size:14px;color:#6366f1;">check</span>' : ''}
            </button>`;
                });
            }

            html += `
        <div style="height:1px;background:var(--incoming-chat-border);margin:4px 0;"></div>
        @if($esPro)
        <button onclick="abrirGestioneTeam();document.getElementById('team-selector-dropdown').style.display='none';"
            style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 14px;
                   background:transparent;color:#6366f1;border:none;font-family:'Poppins',sans-serif;
                   font-size:12px;cursor:pointer;transition:background 0.2s;text-align:left;"
            onmouseover="this.style.background='var(--icon-hover-bg)'"
            onmouseout="this.style.background='transparent'">
            <span class="material-symbols-rounded" style="font-size:16px;">settings</span>
            Gestisci team
        </button>
        @endif
    </div>`;

            dd.innerHTML = html;
            dd.style.display = 'block';
        }

        async function cambiarTeamActivo(teamId) {
            document.getElementById('team-selector-dropdown').style.display = 'none';

            if (teamId) {
                await fetch(`/teams/${teamId}/switch`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            } else {
                // Quitar team activo
                await fetch('/teams/switch/personal', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            }

            // Recargar página para reflejar el cambio
            window.location.reload();
        }

        // Cerrar al click fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#team-selector-wrap')) {
                const dd = document.getElementById('team-selector-dropdown');
                if (dd) dd.style.display = 'none';
            }
        });

        // Inicializar label
        document.addEventListener('DOMContentLoaded', async () => {
            const currentTeamId = {{ auth()->user()->current_team_id ?? 'null' }};
            if (currentTeamId) {
                const res = await fetch('/teams');
                const data = await res.json();
                const tutti = [...(data.owned || []), ...(data.member || [])];
                const team = tutti.find(t => t.id === currentTeamId);
                if (team) {
                    document.getElementById('team-selector-label').textContent = team.name;
                }
            }
        });
    </script>

    @include('components.nav-proyectos')
    @include('components.nav-dataroom')
    @include('components.nav-view-file')
    @include('components.modal-impostazioni')
    @include('components.modal-perfil')
    @include('components.ai-chatbot')
    @include('components.modal-team')
    {{-- Modal Cronologia Chat --}}
    <div id="modal-cronologia-chat"
        style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
        <div
            style="background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:18px;width:100%;max-width:520px;max-height:85vh;display:flex;flex-direction:column;margin:20px;box-shadow:0 24px 60px rgba(0,0,0,0.5);">

            <!-- Header -->
            <div
                style="padding:20px 24px;border-bottom:1px solid var(--incoming-chat-border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="material-symbols-rounded" style="color:#6366f1;font-size:22px;">forum</span>
                    <h3 style="margin:0;font-size:16px;font-weight:700;color:var(--text-color);">Cronologia
                        Conversazioni</h3>
                </div>
                <button onclick="chiudiCronologiaChat()"
                    style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:6px;border-radius:8px;display:flex;transition:all 0.2s;"
                    onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='#ef4444'"
                    onmouseout="this.style.background='none';this.style.color='var(--icon-color)'">
                    <span class="material-symbols-rounded" style="font-size:20px;">close</span>
                </button>
            </div>

            <!-- Lista -->
            <div id="cronologia-chat-lista"
                style="overflow-y:auto;flex:1;padding:12px 16px;display:flex;flex-direction:column;gap:6px;">
                <div id="cronologia-chat-loading" style="text-align:center;padding:40px;color:var(--icon-color);">
                    <span class="material-symbols-rounded"
                        style="font-size:36px;display:block;margin-bottom:8px;opacity:0.4;">hourglass_empty</span>
                    <p style="font-size:13px;">Caricamento...</p>
                </div>
            </div>

            <!-- Footer -->
            <div
                style="padding:14px 20px;border-top:1px solid var(--incoming-chat-border);text-align:center;flex-shrink:0;">
                <span style="font-size:11px;color:var(--icon-color);">Clicca su una conversazione per
                    ripristinarla</span>
            </div>
        </div>
    </div>

    <!-- Panel Referencias DeepSearch -->
    <div id="panel-referencias"
        style="position:fixed;top:0;right:-380px;width:360px;height:100vh;
           background:var(--incoming-chat-bg);border-left:1px solid var(--incoming-chat-border);
           z-index:500;transition:right 0.3s ease;display:flex;flex-direction:column;
           box-shadow:-4px 0 20px rgba(0,0,0,0.2);">

        <!-- Header -->
        <div
            style="padding:16px 20px;border-bottom:1px solid var(--incoming-chat-border);
                display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="material-symbols-rounded" style="font-size:20px;color:#6366f1;">travel_explore</span>
                <h3 style="margin:0;font-size:14px;font-weight:700;color:var(--text-color);">Fonti DeepSearch</h3>
            </div>
            <button onclick="chiudiPanelReferencias()"
                style="border:none;background:none;cursor:pointer;color:var(--icon-color);
                   padding:4px;border-radius:6px;display:flex;transition:all 0.2s;"
                onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='var(--icon-color)'">
                <span class="material-symbols-rounded" style="font-size:20px;">close</span>
            </button>
        </div>

        <!-- Tabs -->
        <div style="display:flex;border-bottom:1px solid var(--incoming-chat-border);">
            <button id="tab-referencias" onclick="switchTab('referencias')"
                style="flex:1;padding:10px;border:none;background:transparent;
                   font-family:'Poppins',sans-serif;font-size:12px;font-weight:600;
                   color:#6366f1;cursor:pointer;border-bottom:2px solid #6366f1;
                   transition:all 0.2s;">
                Fonti usate
            </button>
            <button id="tab-favoritas" onclick="switchTab('favoritas')"
                style="flex:1;padding:10px;border:none;background:transparent;
                   font-family:'Poppins',sans-serif;font-size:12px;
                   color:var(--icon-color);cursor:pointer;border-bottom:2px solid transparent;
                   transition:all 0.2s;">
                Le mie fonti
            </button>
        </div>

        <!-- Contenido -->
        <div style="flex:1;overflow-y:auto;padding:12px 16px;" id="panel-referencias-contenido"></div>
        <div style="flex:1;overflow-y:auto;padding:12px 16px;display:none;" id="panel-favoritas-contenido"></div>

        <!-- Footer agregar URL -->
        <div id="panel-add-url"
            style="display:none;padding:12px 16px;border-top:1px solid var(--incoming-chat-border);">
            <div style="display:flex;flex-direction:column;gap:6px;">
                <input id="add-url-nombre" type="text" placeholder="Nome fonte (es. Giustizia Admin.)"
                    style="background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);
                       border-radius:8px;padding:8px 10px;font-size:12px;color:var(--text-color);
                       font-family:'Poppins',sans-serif;outline:none;transition:border-color 0.2s;"
                    onfocus="this.style.borderColor='#6366f1'"
                    onblur="this.style.borderColor='var(--incoming-chat-border)'">
                <input id="add-url-url" type="url" placeholder="https://..."
                    style="background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);
                       border-radius:8px;padding:8px 10px;font-size:12px;color:var(--text-color);
                       font-family:'Poppins',sans-serif;outline:none;transition:border-color 0.2s;"
                    onfocus="this.style.borderColor='#6366f1'"
                    onblur="this.style.borderColor='var(--incoming-chat-border)'">
                <button onclick="guardarUrlFavorita()"
                    style="background:#6366f1;color:#fff;border:none;border-radius:8px;
                       padding:8px;font-size:12px;font-weight:600;cursor:pointer;
                       font-family:'Poppins',sans-serif;transition:background 0.2s;"
                    onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                    Aggiungi fonte
                </button>
            </div>
        </div>
    </div>

    @if(!$tieneEmpresa)
<script>
document.addEventListener('DOMContentLoaded', () => {
    const card = document.getElementById('card-notifica-empresa');
    const texto = document.getElementById('notifica-empresa-texto');
    if (!card || !texto) return;

    setTimeout(() => {
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';

        const msg = 'Completa il profilo azienda per personalizzare i tuoi documenti con logo e dati.';
        let i = 0;
        const interval = setInterval(() => {
            texto.textContent += msg[i];
            i++;
            if (i >= msg.length) clearInterval(interval);
        }, 18);
    }, 800);
});
</script>
@endif

</body>

</html>
