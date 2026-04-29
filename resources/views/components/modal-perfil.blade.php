<!-- Modal Perfilo -->
<div id="modal-perfil"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:2000;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div style="background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:18px;width:100%;max-width:480px;max-height:90vh;display:flex;flex-direction:column;margin:20px;box-shadow:0 24px 60px rgba(0,0,0,0.5);overflow:hidden;">

        <!-- Header -->
        <div style="padding:20px 24px;border-bottom:1px solid var(--incoming-chat-border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#60a5fa,#3b82f6);display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;" id="perfil-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <h3 style="margin:0;font-size:15px;font-weight:700;color:var(--text-color);">Il mio Profilo</h3>
                    <p style="margin:0;font-size:11px;color:var(--icon-color);">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <button onclick="chiudiPerfil()"
                style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:6px;border-radius:8px;display:flex;transition:all 0.2s;"
                onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='#ef4444'"
                onmouseout="this.style.background='none';this.style.color='var(--icon-color)'">
                <span class="material-symbols-rounded" style="font-size:20px;">close</span>
            </button>
        </div>

        <!-- Contenido scrollable -->
        <div style="flex:1;overflow-y:auto;padding:20px 24px;display:flex;flex-direction:column;gap:20px;">

            <!-- Sección: Info -->
            <div>
                <p style="font-size:11px;font-weight:600;color:var(--icon-color);text-transform:uppercase;letter-spacing:0.05em;margin:0 0 12px;">Informazioni Account</p>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div>
                        <label style="font-size:11px;color:var(--icon-color);display:block;margin-bottom:4px;">Nome</label>
                        <input id="perfil-nombre" type="text" value="{{ auth()->user()->name }}"
                            style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='#6366f1'"
                            onblur="this.style.borderColor='var(--incoming-chat-border)'">
                    </div>
                    <div>
                        <label style="font-size:11px;color:var(--icon-color);display:block;margin-bottom:4px;">Email</label>
                        <input id="perfil-email" type="email" value="{{ auth()->user()->email }}"
                            style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='#6366f1'"
                            onblur="this.style.borderColor='var(--incoming-chat-border)'">
                    </div>
                    <button onclick="guardarInfoPerfil()"
                        style="align-self:flex-end;background:#6366f1;color:#fff;border:none;border-radius:8px;padding:8px 20px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:background 0.2s;"
                        onmouseover="this.style.background='#4f46e5'"
                        onmouseout="this.style.background='#6366f1'">
                        Salva modifiche
                    </button>
                    <div id="perfil-info-msg" style="display:none;font-size:12px;padding:8px 12px;border-radius:8px;"></div>
                </div>
            </div>

            <hr style="border:none;border-top:1px solid var(--incoming-chat-border);margin:0;">

            <!-- Sección: Password -->
            <div>
                <p style="font-size:11px;font-weight:600;color:var(--icon-color);text-transform:uppercase;letter-spacing:0.05em;margin:0 0 12px;">Cambia Password</p>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div>
                        <label style="font-size:11px;color:var(--icon-color);display:block;margin-bottom:4px;">Password attuale</label>
                        <input id="perfil-pass-current" type="password" placeholder="••••••••"
                            style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='#6366f1'"
                            onblur="this.style.borderColor='var(--incoming-chat-border)'">
                    </div>
                    <div>
                        <label style="font-size:11px;color:var(--icon-color);display:block;margin-bottom:4px;">Nuova password</label>
                        <input id="perfil-pass-new" type="password" placeholder="Minimo 8 caratteri"
                            style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='#6366f1'"
                            onblur="this.style.borderColor='var(--incoming-chat-border)'">
                    </div>
                    <div>
                        <label style="font-size:11px;color:var(--icon-color);display:block;margin-bottom:4px;">Conferma nuova password</label>
                        <input id="perfil-pass-confirm" type="password" placeholder="Ripeti la password"
                            style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                            onfocus="this.style.borderColor='#6366f1'"
                            onblur="this.style.borderColor='var(--incoming-chat-border)'">
                    </div>
                    <button onclick="cambiarPassword()"
                        style="align-self:flex-end;background:var(--outgoing-chat-bg);color:var(--text-color);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:8px 20px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:all 0.2s;"
                        onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1'"
                        onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--text-color)'">
                        Aggiorna password
                    </button>
                    <div id="perfil-pass-msg" style="display:none;font-size:12px;padding:8px 12px;border-radius:8px;"></div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function abrirPerfil() {
    document.getElementById('modal-perfil').style.display = 'flex';
}

function chiudiPerfil() {
    document.getElementById('modal-perfil').style.display = 'none';
    // Limpiar campos password
    ['perfil-pass-current','perfil-pass-new','perfil-pass-confirm'].forEach(id => {
        document.getElementById(id).value = '';
    });
    document.getElementById('perfil-info-msg').style.display = 'none';
    document.getElementById('perfil-pass-msg').style.display = 'none';
}

// Cerrar al click fuera
document.getElementById('modal-perfil').addEventListener('click', function(e) {
    if (e.target === this) chiudiPerfil();
});

function mostrarMsgPerfil(elId, texto, tipo) {
    const el = document.getElementById(elId);
    el.textContent = texto;
    el.style.display = 'block';
    el.style.background = tipo === 'ok' ? '#10b98120' : '#ef444420';
    el.style.color     = tipo === 'ok' ? '#10b981'   : '#ef4444';
    el.style.border    = `1px solid ${tipo === 'ok' ? '#10b98140' : '#ef444440'}`;
    setTimeout(() => el.style.display = 'none', 4000);
}

async function guardarInfoPerfil() {
    const name  = document.getElementById('perfil-nombre').value.trim();
    const email = document.getElementById('perfil-email').value.trim();
    if (!name || !email) return;

    try {
        const res = await fetch('/user/profile-information', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name, email })
        });

        if (res.ok) {
            // Actualizar avatar y nombre en el dropdown
            document.getElementById('perfil-avatar').textContent = name.substring(0,2).toUpperCase();
            mostrarMsgPerfil('perfil-info-msg', '✅ Profilo aggiornato con successo', 'ok');
        } else {
            const data = await res.json();
            const msg = data.errors ? Object.values(data.errors).flat().join(' ') : 'Errore nel salvataggio.';
            mostrarMsgPerfil('perfil-info-msg', msg, 'err');
        }
    } catch(e) {
        mostrarMsgPerfil('perfil-info-msg', 'Errore di connessione.', 'err');
    }
}

async function cambiarPassword() {
    const current = document.getElementById('perfil-pass-current').value;
    const newPass  = document.getElementById('perfil-pass-new').value;
    const confirm  = document.getElementById('perfil-pass-confirm').value;

    if (!current || !newPass || !confirm) {
        mostrarMsgPerfil('perfil-pass-msg', 'Compila tutti i campi.', 'err');
        return;
    }
    if (newPass !== confirm) {
        mostrarMsgPerfil('perfil-pass-msg', 'Le password non coincidono.', 'err');
        return;
    }
    if (newPass.length < 8) {
        mostrarMsgPerfil('perfil-pass-msg', 'La password deve avere almeno 8 caratteri.', 'err');
        return;
    }

    try {
        const res = await fetch('/user/password', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                current_password: current,
                password: newPass,
                password_confirmation: confirm
            })
        });

        if (res.ok) {
            ['perfil-pass-current','perfil-pass-new','perfil-pass-confirm'].forEach(id => {
                document.getElementById(id).value = '';
            });
            mostrarMsgPerfil('perfil-pass-msg', '✅ Password aggiornata con successo', 'ok');
        } else {
            const data = await res.json();
            const msg = data.errors ? Object.values(data.errors).flat().join(' ') : 'Password attuale errata.';
            mostrarMsgPerfil('perfil-pass-msg', msg, 'err');
        }
    } catch(e) {
        mostrarMsgPerfil('perfil-pass-msg', 'Errore di connessione.', 'err');
    }
}
</script>
