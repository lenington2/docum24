{{-- AI Support Chatbot --}}
<div id="support-chatbot-container">

    {{-- Bottone Flottante --}}
    <button id="support-toggle" aria-label="Supporto AI"
        style="position:fixed;bottom:24px;right:24px;width:52px;height:52px;
               background:var(--ink);border:none;border-radius:50%;cursor:pointer;
               box-shadow:0 4px 16px rgba(0,0,0,0.3);display:flex;align-items:center;
               justify-content:center;transition:all 0.3s ease;z-index:9999;">
        <span id="support-icon-open" class="material-symbols-rounded" style="font-size:24px;color:#fff;transition:all 0.3s;">support_agent</span>
        <span id="support-icon-close" class="material-symbols-rounded" style="font-size:24px;color:#fff;position:absolute;opacity:0;transform:rotate(90deg);transition:all 0.3s;">close</span>
        <span id="support-badge"
            style="display:none;position:absolute;top:-2px;right:-2px;
                   background:#ef4444;color:#fff;border-radius:50%;
                   width:18px;height:18px;font-size:10px;font-weight:700;
                   align-items:center;justify-content:center;
                   border:2px solid var(--outgoing-chat-bg);">1</span>
    </button>

    {{-- Modal --}}
    <div id="support-modal"
        style="position:fixed;bottom:90px;right:24px;width:380px;
               max-width:calc(100vw - 48px);height:520px;
               max-height:calc(100vh - 120px);
               background:var(--incoming-chat-bg);
               border:1px solid var(--incoming-chat-border);
               border-radius:16px;display:flex;flex-direction:column;
               box-shadow:0 16px 48px rgba(0,0,0,0.3);
               opacity:0;transform:translateY(16px) scale(0.97);
               pointer-events:none;transition:all 0.3s cubic-bezier(0.4,0,0.2,1);
               z-index:9998;overflow:hidden;">

        <!-- Header -->
        <div style="padding:16px 20px;border-bottom:1px solid var(--incoming-chat-border);
                    display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:10px;background:#6366f120;
                            border:1px solid #6366f130;display:flex;align-items:center;justify-content:center;">
                    <span class="material-symbols-rounded" style="font-size:20px;color:#6366f1;">support_agent</span>
                </div>
                <div>
                    <p style="margin:0;font-size:13px;font-weight:700;color:var(--text-color);">Supporto Docum24</p>
                    <p style="margin:0;font-size:11px;color:#10b981;display:flex;align-items:center;gap:4px;">
                        <span style="width:6px;height:6px;border-radius:50%;background:#10b981;display:inline-block;"></span>
                        AI Online
                    </p>
                </div>
            </div>
            <div style="display:flex;gap:6px;">
                <button onclick="clearSupportChat()" title="Pulisci"
                    style="width:30px;height:30px;border:none;background:transparent;cursor:pointer;
                           color:var(--icon-color);border-radius:6px;display:flex;align-items:center;
                           justify-content:center;transition:all 0.2s;"
                    onmouseover="this.style.background='var(--icon-hover-bg)'"
                    onmouseout="this.style.background='transparent'">
                    <span class="material-symbols-rounded" style="font-size:18px;">delete_sweep</span>
                </button>
                <button onclick="toggleSupportChat()" title="Chiudi"
                    style="width:30px;height:30px;border:none;background:transparent;cursor:pointer;
                           color:var(--icon-color);border-radius:6px;display:flex;align-items:center;
                           justify-content:center;transition:all 0.2s;"
                    onmouseover="this.style.background='var(--icon-hover-bg)'"
                    onmouseout="this.style.background='transparent'">
                    <span class="material-symbols-rounded" style="font-size:18px;">close</span>
                </button>
            </div>
        </div>

        <!-- Messages -->
        <div id="support-messages"
            style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:10px;">
        </div>

        <!-- Quick actions -->
        <div id="support-quick-actions"
            style="padding:8px 16px;display:flex;gap:6px;flex-wrap:wrap;border-top:1px solid var(--incoming-chat-border);">
            <button onclick="sendSupportQuick('Come creo un report?')"
                style="font-size:11px;padding:5px 10px;border-radius:8px;border:1px solid var(--incoming-chat-border);
                       background:var(--outgoing-chat-bg);color:var(--icon-color);cursor:pointer;
                       font-family:'Poppins',sans-serif;transition:all 0.2s;white-space:nowrap;"
                onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)'">
                📊 Report
            </button>
            <button onclick="sendSupportQuick('Come carico documenti?')"
                style="font-size:11px;padding:5px 10px;border-radius:8px;border:1px solid var(--incoming-chat-border);
                       background:var(--outgoing-chat-bg);color:var(--icon-color);cursor:pointer;
                       font-family:'Poppins',sans-serif;transition:all 0.2s;white-space:nowrap;"
                onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)'">
                📂 Documenti
            </button>
            <button onclick="sendSupportQuick('Come funzionano i progetti?')"
                style="font-size:11px;padding:5px 10px;border-radius:8px;border:1px solid var(--incoming-chat-border);
                       background:var(--outgoing-chat-bg);color:var(--icon-color);cursor:pointer;
                       font-family:'Poppins',sans-serif;transition:all 0.2s;white-space:nowrap;"
                onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)'">
                📁 Progetti
            </button>
            <button onclick="sendSupportQuick('Cosa posso fare con Docum24?')"
                style="font-size:11px;padding:5px 10px;border-radius:8px;border:1px solid var(--incoming-chat-border);
                       background:var(--outgoing-chat-bg);color:var(--icon-color);cursor:pointer;
                       font-family:'Poppins',sans-serif;transition:all 0.2s;white-space:nowrap;"
                onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)'">
                ❓ Aiuto
            </button>
        </div>

        <!-- Input -->
        <div style="padding:12px 16px;border-top:1px solid var(--incoming-chat-border);flex-shrink:0;">
            <div style="display:flex;gap:8px;align-items:flex-end;">
                <textarea id="support-input" rows="1" placeholder="Scrivi una domanda..."
                    style="flex:1;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);
                           border-radius:10px;padding:10px 12px;font-size:13px;color:var(--text-color);
                           font-family:'Poppins',sans-serif;outline:none;resize:none;
                           max-height:100px;transition:border-color 0.2s;line-height:1.4;"
                    onfocus="this.style.borderColor='#6366f1'"
                    onblur="this.style.borderColor='var(--incoming-chat-border)'"
                    oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"
                    onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendSupportMessage();}">
                </textarea>
                <button onclick="sendSupportMessage()"
                    style="width:38px;height:38px;border:none;background:#6366f1;border-radius:10px;
                           cursor:pointer;display:flex;align-items:center;justify-content:center;
                           transition:background 0.2s;flex-shrink:0;"
                    onmouseover="this.style.background='#4f46e5'"
                    onmouseout="this.style.background='#6366f1'">
                    <span class="material-symbols-rounded" style="font-size:18px;color:#fff;">send</span>
                </button>
            </div>
            <p style="margin:6px 0 0;font-size:10px;color:var(--icon-color);text-align:center;">
                Powered by Docum24 AI
            </p>
        </div>
    </div>
</div>

<script>
let _supportOpen = false;
let _supportHistorial = [];

// Mensaje de bienvenida al abrir por primera vez
function initSupportChat() {
    const msgs = document.getElementById('support-messages');
    if (msgs.children.length === 0) {
        addSupportMessage('bot', `Ciao **{{ explode(' ', auth()->user()->name)[0] }}**! 👋 Sono l'assistente di supporto Docum24.\n\nPosso aiutarti a capire come usare l'app, spiegare le funzionalità e aprire direttamente i form per te.\n\nCosa vuoi fare?`);
    }
}

function toggleSupportChat() {
    _supportOpen = !_supportOpen;
    const modal = document.getElementById('support-modal');
    const iconOpen = document.getElementById('support-icon-open');
    const iconClose = document.getElementById('support-icon-close');
    const badge = document.getElementById('support-badge');

    if (_supportOpen) {
        modal.style.opacity = '1';
        modal.style.transform = 'translateY(0) scale(1)';
        modal.style.pointerEvents = 'all';
        iconOpen.style.opacity = '0';
        iconOpen.style.transform = 'rotate(-90deg)';
        iconClose.style.opacity = '1';
        iconClose.style.transform = 'rotate(0deg)';
        badge.style.display = 'none';
        initSupportChat();
        setTimeout(() => document.getElementById('support-input')?.focus(), 300);
    } else {
        modal.style.opacity = '0';
        modal.style.transform = 'translateY(16px) scale(0.97)';
        modal.style.pointerEvents = 'none';
        iconOpen.style.opacity = '1';
        iconOpen.style.transform = 'rotate(0deg)';
        iconClose.style.opacity = '0';
        iconClose.style.transform = 'rotate(90deg)';
    }
}

document.getElementById('support-toggle').addEventListener('click', toggleSupportChat);

function addSupportMessage(role, text) {
    const msgs = document.getElementById('support-messages');
    const div = document.createElement('div');
    div.style.cssText = `display:flex;gap:8px;${role === 'user' ? 'flex-direction:row-reverse;' : ''}`;

    // Formatear texto simple (bold con **)
    const formatted = text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\n/g, '<br>');

    div.innerHTML = `
        <div style="width:28px;height:28px;border-radius:8px;flex-shrink:0;
                    background:${role === 'bot' ? '#6366f120' : 'var(--outgoing-chat-bg)'};
                    border:1px solid ${role === 'bot' ? '#6366f130' : 'var(--incoming-chat-border)'};
                    display:flex;align-items:center;justify-content:center;">
            <span class="material-symbols-rounded" style="font-size:16px;color:${role === 'bot' ? '#6366f1' : 'var(--icon-color)'};">
                ${role === 'bot' ? 'support_agent' : 'person'}
            </span>
        </div>
        <div style="max-width:80%;">
            <div style="background:${role === 'bot' ? 'var(--outgoing-chat-bg)' : '#6366f1'};
                        color:${role === 'bot' ? 'var(--text-color)' : '#fff'};
                        border:1px solid ${role === 'bot' ? 'var(--incoming-chat-border)' : 'transparent'};
                        border-radius:${role === 'bot' ? '4px 12px 12px 12px' : '12px 4px 12px 12px'};
                        padding:10px 12px;font-size:13px;line-height:1.6;">
                ${formatted}
            </div>
        </div>`;

    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function addSupportTyping() {
    const msgs = document.getElementById('support-messages');
    const div = document.createElement('div');
    div.id = 'support-typing';
    div.style.cssText = 'display:flex;gap:8px;';
    div.innerHTML = `
        <div style="width:28px;height:28px;border-radius:8px;flex-shrink:0;
                    background:#6366f120;border:1px solid #6366f130;
                    display:flex;align-items:center;justify-content:center;">
            <span class="material-symbols-rounded" style="font-size:16px;color:#6366f1;">support_agent</span>
        </div>
        <div style="background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);
                    border-radius:4px 12px 12px 12px;padding:12px 16px;display:flex;gap:4px;align-items:center;">
            <span style="width:7px;height:7px;border-radius:50%;background:var(--icon-color);
                         animation:supportDot 1.4s infinite;display:block;"></span>
            <span style="width:7px;height:7px;border-radius:50%;background:var(--icon-color);
                         animation:supportDot 1.4s infinite 0.2s;display:block;"></span>
            <span style="width:7px;height:7px;border-radius:50%;background:var(--icon-color);
                         animation:supportDot 1.4s infinite 0.4s;display:block;"></span>
        </div>`;
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function removeSupportTyping() {
    document.getElementById('support-typing')?.remove();
}

async function sendSupportMessage() {
    const input = document.getElementById('support-input');
    const text = input.value.trim();
    if (!text) return;

    // Ocultar quick actions después del primer mensaje
    document.getElementById('support-quick-actions').style.display = 'none';

    addSupportMessage('user', text);
    input.value = '';
    input.style.height = 'auto';

    _supportHistorial.push({ role: 'user', content: text });
    addSupportTyping();

    try {
        const res = await fetch('/support/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                message: text,
                historial: _supportHistorial
            })
        });

        const data = await res.json();
        removeSupportTyping();

        if (data.success) {
            addSupportMessage('bot', data.response);
            _supportHistorial.push({ role: 'assistant', content: data.response });

            // Limitar historial
            if (_supportHistorial.length > 10) {
                _supportHistorial = _supportHistorial.slice(-10);
            }

            // Ejecutar acción si viene
            if (data.action && data.action !== 'nessuna') {
                setTimeout(() => executeSupportAction(data.action), 600);
            }
        } else {
            addSupportMessage('bot', 'Mi dispiace, si è verificato un errore. Riprova!');
        }
    } catch(e) {
        removeSupportTyping();
        addSupportMessage('bot', 'Errore di connessione. Controlla la tua connessione.');
    }
}

function sendSupportQuick(text) {
    document.getElementById('support-input').value = text;
    sendSupportMessage();
}

function clearSupportChat() {
    document.getElementById('support-messages').innerHTML = '';
    _supportHistorial = [];
    document.getElementById('support-quick-actions').style.display = 'flex';
    initSupportChat();
}

function executeSupportAction(action) {
    // Integración con las funciones existentes del dashboard
    switch(action) {
        case 'apri_report':
            if (typeof avviaWizardReport === 'function') avviaWizardReport();
            break;
        case 'apri_progetto':
            if (typeof toggleNav === 'function') toggleNav();
            break;
        case 'crea_progetto':
            if (typeof accionProyecto === 'function') accionProyecto('crea');
            break;
        case 'crea_categoria':
            if (typeof accionCategoria === 'function') accionCategoria('crea');
            break;
        case 'apri_cronologia':
            if (typeof abrirCronologia === 'function') abrirCronologia();
            break;
        case 'apri_impostazioni':
            if (typeof abrirImpostazioni === 'function') abrirImpostazioni();
            break;
        case 'suggerisci_supporto':
            setTimeout(() => {
                addSupportMessage('bot', `Non riesco a risolvere questa richiesta direttamente. Vuoi che giri la domanda al supporto umano?

        **"${_supportHistorial[_supportHistorial.length-2]?.content || ''}"**`);

                const msgs = document.getElementById('support-messages');
                const div = document.createElement('div');
                div.style.cssText = 'display:flex;gap:8px;margin-top:4px;';
                div.innerHTML = `
                    <div style="width:28px;flex-shrink:0;"></div>
                    <div style="display:flex;gap:6px;">
                        <button onclick="trasferireASupporto()"
                            style="background:#6366f1;color:#fff;border:none;border-radius:8px;
                                   padding:7px 14px;font-size:12px;font-weight:600;cursor:pointer;
                                   font-family:'Poppins',sans-serif;transition:background 0.2s;"
                            onmouseover="this.style.background='#4f46e5'"
                            onmouseout="this.style.background='#6366f1'">
                            Sì, contatta supporto
                        </button>
                        <button onclick="this.closest('div[style]').remove()"
                            style="background:transparent;color:var(--icon-color);
                                   border:1px solid var(--incoming-chat-border);border-radius:8px;
                                   padding:7px 14px;font-size:12px;cursor:pointer;
                                   font-family:'Poppins',sans-serif;">
                            No, grazie
                        </button>
                    </div>`;
                msgs.appendChild(div);
                msgs.scrollTop = msgs.scrollHeight;
            }, 300);
            break;
    }
}

function trasferireASupporto() {
    const ultimaDomanda = _supportHistorial
        .filter(m => m.role === 'user')
        .pop()?.content || '';

    // Abrir el chat principal con la pregunta
    toggleSupportChat();

    setTimeout(() => {
        const input = document.getElementById('chat-input');
        if (input) {
            input.value = ultimaDomanda;
            input.focus();
            // Highlight del input
            input.style.outline = '2px solid #6366f1';
            input.style.borderColor = '#6366f1';
            setTimeout(() => {
                input.style.outline = '';
                input.style.borderColor = '';
            }, 2000);
        }
        // Toast informativo
        const toast = document.createElement('div');
        toast.style.cssText = `position:fixed;bottom:24px;left:50%;transform:translateX(-50%);
            z-index:9999;background:#6366f1;color:#fff;padding:10px 18px;border-radius:10px;
            font-size:13px;font-weight:600;font-family:'Poppins',sans-serif;
            box-shadow:0 8px 24px rgba(0,0,0,0.2);animation:fadeIn 0.3s ease;`;
        toast.textContent = '💬 Domanda trasferita al chat principale';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }, 400);
}

// Mostrar badge después de 3 segundos si el chat no está abierto
setTimeout(() => {
    if (!_supportOpen) {
        const badge = document.getElementById('support-badge');
        if (badge) badge.style.display = 'flex';
    }
}, 3000);
</script>

<style>
@keyframes supportDot {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.7; }
    30% { transform: translateY(-6px); opacity: 1; }
}
</style>
