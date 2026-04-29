<div id="modal-team"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:2000;
           align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div style="background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);
                border-radius:18px;width:100%;max-width:680px;max-height:85vh;
                display:flex;flex-direction:column;margin:20px;
                box-shadow:0 24px 60px rgba(0,0,0,0.5);">

        <!-- Header -->
        <div style="padding:20px 24px;border-bottom:1px solid var(--incoming-chat-border);
                    display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:10px;">
                <span class="material-symbols-rounded" style="color:#6366f1;font-size:22px;">group</span>
                <h3 style="margin:0;font-size:16px;font-weight:700;color:var(--text-color);">Gestione Team</h3>
            </div>
            <button onclick="chiudiGestioneTeam()"
                style="border:none;background:none;cursor:pointer;color:var(--icon-color);
                       padding:6px;border-radius:8px;display:flex;transition:all 0.2s;"
                onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='#ef4444'"
                onmouseout="this.style.background='none';this.style.color='var(--icon-color)'">
                <span class="material-symbols-rounded" style="font-size:20px;">close</span>
            </button>
        </div>

        <!-- Tabs -->
        <div style="display:flex;border-bottom:1px solid var(--incoming-chat-border);flex-shrink:0;">
            <button id="tab-miei-team" onclick="switchTeamTab('miei')"
                style="flex:1;padding:12px;border:none;background:transparent;
                       font-family:'Poppins',sans-serif;font-size:12px;font-weight:600;
                       color:#6366f1;cursor:pointer;border-bottom:2px solid #6366f1;transition:all 0.2s;">
                I miei Team
            </button>
            <button id="tab-altri-team" onclick="switchTeamTab('altri')"
                style="flex:1;padding:12px;border:none;background:transparent;
                       font-family:'Poppins',sans-serif;font-size:12px;
                       color:var(--icon-color);cursor:pointer;border-bottom:2px solid transparent;transition:all 0.2s;">
                Team di cui faccio parte
            </button>
        </div>

        <!-- Content -->
        <div style="flex:1;overflow-y:auto;padding:20px 24px;" id="team-content">
            <div style="text-align:center;padding:40px;color:var(--icon-color);">
                <span class="material-symbols-rounded" style="font-size:36px;display:block;margin-bottom:8px;opacity:0.4;animation:spin 1s linear infinite;">progress_activity</span>
                <p style="font-size:13px;">Caricamento...</p>
            </div>
        </div>

        <!-- Footer crea team -->
        <div id="team-footer" style="padding:16px 24px;border-top:1px solid var(--incoming-chat-border);flex-shrink:0;">
            <div style="display:flex;gap:8px;">
                <input id="new-team-name" type="text" placeholder="Nome nuovo team..."
                    style="flex:1;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);
                           border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);
                           font-family:'Poppins',sans-serif;outline:none;transition:border-color 0.2s;"
                    onfocus="this.style.borderColor='#6366f1'"
                    onblur="this.style.borderColor='var(--incoming-chat-border)'"
                    onkeydown="if(event.key==='Enter') crearTeam()">
                <button onclick="crearTeam()"
                    style="background:#6366f1;color:#fff;border:none;border-radius:8px;
                           padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer;
                           font-family:'Poppins',sans-serif;transition:background 0.2s;white-space:nowrap;"
                    onmouseover="this.style.background='#4f46e5'"
                    onmouseout="this.style.background='#6366f1'">
                    + Crea Team
                </button>
            </div>
            <p id="team-limit-info" style="margin:6px 0 0;font-size:11px;color:var(--icon-color);"></p>
        </div>
    </div>
</div>

<script>
let _teamTab = 'miei';
let _teamData = { owned: [], member: [] };
let _teamSelezionato = null;

async function abrirGestioneTeam() {
    const modal = document.getElementById('modal-team');
    modal.style.display = 'flex';
    await cargarTeams();
}

function chiudiGestioneTeam() {
    document.getElementById('modal-team').style.display = 'none';
    _teamSelezionato = null;
}

document.getElementById('modal-team').addEventListener('click', function(e) {
    if (e.target === this) chiudiGestioneTeam();
});

async function cargarTeams() {
    try {
        const res = await fetch('/teams');
        _teamData = await res.json();
        renderTeamTab();
    } catch(e) {
        console.error(e);
    }
}

function switchTeamTab(tab) {
    _teamTab = tab;
    _teamSelezionato = null;
    const tabMiei  = document.getElementById('tab-miei-team');
    const tabAltri = document.getElementById('tab-altri-team');
    const footer   = document.getElementById('team-footer');

    if (tab === 'miei') {
        tabMiei.style.color = '#6366f1';
        tabMiei.style.borderBottom = '2px solid #6366f1';
        tabAltri.style.color = 'var(--icon-color)';
        tabAltri.style.borderBottom = '2px solid transparent';
        footer.style.display = 'block';
    } else {
        tabAltri.style.color = '#6366f1';
        tabAltri.style.borderBottom = '2px solid #6366f1';
        tabMiei.style.color = 'var(--icon-color)';
        tabMiei.style.borderBottom = '2px solid transparent';
        footer.style.display = 'none';
    }
    renderTeamTab();
}

function renderTeamTab() {
    const content = document.getElementById('team-content');
    const limitInfo = document.getElementById('team-limit-info');

    if (_teamTab === 'miei') {
        const teams = _teamData.owned || [];
        limitInfo.textContent = `${teams.length}/3 team creati`;

        if (!teams.length) {
            content.innerHTML = `
                <div style="text-align:center;padding:40px;color:var(--icon-color);">
                    <span class="material-symbols-rounded" style="font-size:40px;display:block;margin-bottom:8px;opacity:0.3;">group_add</span>
                    <p style="font-size:13px;">Nessun team ancora.<br>Crea il tuo primo team!</p>
                </div>`;
            return;
        }

        content.innerHTML = teams.map(t => renderTeamCard(t)).join('');

    } else {
        const teams = _teamData.member || [];

        if (!teams.length) {
            content.innerHTML = `
                <div style="text-align:center;padding:40px;color:var(--icon-color);">
                    <span class="material-symbols-rounded" style="font-size:40px;display:block;margin-bottom:8px;opacity:0.3;">group</span>
                    <p style="font-size:13px;">Non fai parte di nessun team.</p>
                </div>`;
            return;
        }

        content.innerHTML = teams.map(t => `
            <div style="padding:14px 16px;border:1px solid var(--incoming-chat-border);
                        border-radius:12px;margin-bottom:8px;background:var(--outgoing-chat-bg);">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span class="material-symbols-rounded" style="font-size:20px;color:#6366f1;">group</span>
                    <div style="flex:1;">
                        <p style="margin:0;font-size:13px;font-weight:600;color:var(--text-color);">${t.name}</p>
                        <p style="margin:0;font-size:11px;color:var(--icon-color);">Owner: ${t.owner?.name || '—'}</p>
                    </div>
                    <span style="font-size:11px;color:#6366f1;background:#6366f115;padding:3px 8px;border-radius:6px;">
                        ${t.pivot?.role || 'member'}
                    </span>
                </div>
            </div>`).join('');
    }
}

function renderTeamCard(team) {
    const members = team.users || [];
    const proyectos = team.proyectos || [];

    return `
    <div style="border:1px solid var(--incoming-chat-border);border-radius:12px;
                margin-bottom:12px;overflow:hidden;">

        <!-- Team header -->
        <div style="padding:14px 16px;background:var(--outgoing-chat-bg);
                    display:flex;align-items:center;gap:10px;">
            <span class="material-symbols-rounded" style="font-size:20px;color:#6366f1;">group</span>
            <div style="flex:1;min-width:0;">
                <p style="margin:0;font-size:13px;font-weight:700;color:var(--text-color);">${team.name}</p>
                <p style="margin:0;font-size:11px;color:var(--icon-color);">
                    ${members.length} membri · ${proyectos.length} progetti
                </p>
            </div>
            <button onclick="toggleTeamDetail(${team.id})"
                style="border:none;background:transparent;cursor:pointer;color:var(--icon-color);
                       padding:4px;border-radius:6px;display:flex;transition:all 0.2s;"
                onmouseover="this.style.color='#6366f1'"
                onmouseout="this.style.color='var(--icon-color)'">
                <span class="material-symbols-rounded" id="team-chevron-${team.id}" style="font-size:18px;">expand_more</span>
            </button>
            <button onclick="eliminaTeam(${team.id}, '${team.name}')"
                style="border:none;background:transparent;cursor:pointer;color:var(--icon-color);
                       padding:4px;border-radius:6px;display:flex;transition:all 0.2s;"
                onmouseover="this.style.color='#ef4444'"
                onmouseout="this.style.color='var(--icon-color)'">
                <span class="material-symbols-rounded" style="font-size:18px;">delete</span>
            </button>
        </div>

        <!-- Team detail -->
        <div id="team-detail-${team.id}" style="display:none;padding:16px;">

            <!-- Membri -->
            <p style="margin:0 0 8px;font-size:11px;font-weight:600;letter-spacing:.06em;
                      text-transform:uppercase;color:var(--icon-color);">Membri</p>

            <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:16px;">
                <!-- Owner -->
                <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;
                            background:var(--outgoing-chat-bg);border-radius:8px;
                            border:1px solid var(--incoming-chat-border);">
                    <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#4f46e5);
                                display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div style="flex:1;">
                        <p style="margin:0;font-size:12px;font-weight:600;color:var(--text-color);">{{ auth()->user()->name }}</p>
                        <p style="margin:0;font-size:11px;color:var(--icon-color);">{{ auth()->user()->email }}</p>
                    </div>
                    <span style="font-size:10px;color:#6366f1;background:#6366f115;padding:2px 8px;border-radius:6px;font-weight:600;">Admin</span>
                </div>

                ${members.map(m => `
                <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;
                            background:var(--outgoing-chat-bg);border-radius:8px;
                            border:1px solid var(--incoming-chat-border);">
                    <div style="width:28px;height:28px;border-radius:50%;background:var(--incoming-chat-border);
                                display:flex;align-items:center;justify-content:center;color:var(--text-color);font-size:11px;font-weight:700;">
                        ${m.name.substring(0,2).toUpperCase()}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p style="margin:0;font-size:12px;font-weight:600;color:var(--text-color);">${m.name}</p>
                        <p style="margin:0;font-size:11px;color:var(--icon-color);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${m.email}</p>
                    </div>
                    <select onchange="cambiaRuoloMembro(${team.id}, ${m.id}, this.value)"
                        style="background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);
                               border-radius:6px;padding:3px 6px;font-size:11px;color:var(--text-color);
                               font-family:'Poppins',sans-serif;outline:none;cursor:pointer;">
                        <option value="editor" ${m.pivot?.role === 'editor' ? 'selected' : ''}>Editor</option>
                        <option value="viewer" ${m.pivot?.role === 'viewer' ? 'selected' : ''}>Viewer</option>
                    </select>
                    <button onclick="rimuoviMembro(${team.id}, ${m.id})"
                        style="border:none;background:transparent;cursor:pointer;color:var(--icon-color);
                               padding:2px;border-radius:4px;display:flex;transition:all 0.2s;"
                        onmouseover="this.style.color='#ef4444'"
                        onmouseout="this.style.color='var(--icon-color)'">
                        <span class="material-symbols-rounded" style="font-size:16px;">person_remove</span>
                    </button>
                </div>`).join('')}
            </div>

            <!-- Invita membro -->
            <p style="margin:0 0 8px;font-size:11px;font-weight:600;letter-spacing:.06em;
                      text-transform:uppercase;color:var(--icon-color);">Invita membro</p>
            <div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap;">
                <input id="invite-email-${team.id}" type="email" placeholder="email@esempio.com"
                    style="flex:1;min-width:160px;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);
                           border-radius:8px;padding:8px 10px;font-size:12px;color:var(--text-color);
                           font-family:'Poppins',sans-serif;outline:none;transition:border-color 0.2s;"
                    onfocus="this.style.borderColor='#6366f1'"
                    onblur="this.style.borderColor='var(--incoming-chat-border)'">
                <select id="invite-role-${team.id}"
                    style="background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);
                           border-radius:8px;padding:8px 10px;font-size:12px;color:var(--text-color);
                           font-family:'Poppins',sans-serif;outline:none;cursor:pointer;">
                    <option value="editor">Editor</option>
                    <option value="viewer">Viewer</option>
                </select>
                <button onclick="invitaMembro(${team.id})"
                    style="background:#6366f1;color:#fff;border:none;border-radius:8px;
                           padding:8px 14px;font-size:12px;font-weight:600;cursor:pointer;
                           font-family:'Poppins',sans-serif;transition:background 0.2s;white-space:nowrap;"
                    onmouseover="this.style.background='#4f46e5'"
                    onmouseout="this.style.background='#6366f1'">
                    Invita
                </button>
            </div>

            <!-- Progetti del team -->
            <p style="margin:0 0 8px;font-size:11px;font-weight:600;letter-spacing:.06em;
                      text-transform:uppercase;color:var(--icon-color);">Progetti assegnati</p>
            <div id="team-proyectos-${team.id}" style="display:flex;flex-direction:column;gap:4px;margin-bottom:12px;">
                ${proyectos.length ? proyectos.map(p => `
                    <div style="display:flex;align-items:center;gap:8px;padding:6px 10px;
                                background:var(--outgoing-chat-bg);border-radius:8px;
                                border:1px solid var(--incoming-chat-border);">
                        <span class="material-symbols-rounded" style="font-size:16px;color:#f59e0b;">folder</span>
                        <span style="font-size:12px;color:var(--text-color);flex:1;">${p.nombre}</span>
                    </div>`).join('') :
                    '<p style="font-size:12px;color:var(--icon-color);margin:0;">Nessun progetto assegnato.</p>'
                }
            </div>

            <!-- Assegna progetto -->
            <div style="display:flex;gap:6px;" id="assign-project-form-${team.id}"></div>
        </div>
    </div>`;
}

function toggleTeamDetail(teamId) {
    const detail = document.getElementById(`team-detail-${teamId}`);
    const chevron = document.getElementById(`team-chevron-${teamId}`);
    const isOpen = detail.style.display !== 'none';
    detail.style.display = isOpen ? 'none' : 'block';
    chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';

    if (!isOpen) {
        // Cargar proyectos disponibles para asignar
        cargarProyectosParaAsignar(teamId);
    }
}

async function cargarProyectosParaAsignar(teamId) {
    const form = document.getElementById(`assign-project-form-${teamId}`);
    if (!form) return;

    const res = await fetch('/proyectos');
    const proyectos = await res.json();
    const sinTeam = proyectos.filter(p => !p.team_id || p.team_id === teamId);

    if (!sinTeam.length) {
        form.innerHTML = '<p style="font-size:11px;color:var(--icon-color);">Nessun progetto disponibile da assegnare.</p>';
        return;
    }

    form.innerHTML = `
        <select id="assign-proy-select-${teamId}"
            style="flex:1;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);
                   border-radius:8px;padding:8px 10px;font-size:12px;color:var(--text-color);
                   font-family:'Poppins',sans-serif;outline:none;cursor:pointer;">
            <option value="">Seleziona progetto...</option>
            ${sinTeam.map(p => `<option value="${p.id}">${p.nombre}</option>`).join('')}
        </select>
        <button onclick="assegnaProgetto(${teamId})"
            style="background:#f59e0b;color:#fff;border:none;border-radius:8px;
                   padding:8px 14px;font-size:12px;font-weight:600;cursor:pointer;
                   font-family:'Poppins',sans-serif;white-space:nowrap;"
            onmouseover="this.style.background='#d97706'"
            onmouseout="this.style.background='#f59e0b'">
            Assegna
        </button>`;
}

async function crearTeam() {
    const input = document.getElementById('new-team-name');
    const name = input.value.trim();
    if (!name) return;

    const res = await fetch('/teams', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ name })
    });
    const data = await res.json();

    if (data.success) {
        input.value = '';
        await cargarTeams();
    } else if (data.error === 'limite_raggiunto') {
        agregarRespuestaBot(`⚠️ ${data.message}`);
        chiudiGestioneTeam();
    }
}

async function invitaMembro(teamId) {
    const email = document.getElementById(`invite-email-${teamId}`)?.value.trim();
    const role  = document.getElementById(`invite-role-${teamId}`)?.value;
    if (!email) return;

    const res = await fetch(`/teams/${teamId}/invite`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ email, role })
    });
    const data = await res.json();

    if (data.success) {
        document.getElementById(`invite-email-${teamId}`).value = '';
        agregarRespuestaBot(`✅ Invito inviato a <strong>${email}</strong>`);
    } else {
        agregarRespuestaBot(`⚠️ ${data.message}`);
    }
}

async function rimuoviMembro(teamId, memberId) {
    const res = await fetch(`/teams/${teamId}/members/${memberId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken }
    });
    const data = await res.json();
    if (data.success) await cargarTeams();
}

async function cambiaRuoloMembro(teamId, memberId, role) {
    await fetch(`/teams/${teamId}/members/${memberId}/role`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ role })
    });
}

async function assegnaProgetto(teamId) {
    const proyectoId = document.getElementById(`assign-proy-select-${teamId}`)?.value;
    if (!proyectoId) return;

    const res = await fetch(`/teams/${teamId}/projects`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ proyecto_id: proyectoId })
    });
    const data = await res.json();
    if (data.success) await cargarTeams();
}

async function eliminaTeam(teamId, name) {
    const result = await Swal.fire({
        title: 'Eliminare il team?',
        html: `Stai per eliminare <strong>${name}</strong>.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: 'transparent',
        confirmButtonText: 'Sì, elimina',
        cancelButtonText: 'Annulla',
        background: 'var(--incoming-chat-bg)',
        color: 'var(--text-color)',
        customClass: { cancelButton: 'swal-cancel-custom' }
    });

    if (!result.isConfirmed) return;

    const res = await fetch(`/teams/${teamId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrfToken }
    });
    const data = await res.json();
    if (data.success) await cargarTeams();
}
</script>
