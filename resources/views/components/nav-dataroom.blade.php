<!-- DataRoom -->
<div id="nav-dataroom"
    style="position:fixed;top:0;left:-420px;width:400px;height:100vh;background:var(--incoming-chat-bg);border-right:1px solid var(--incoming-chat-border);z-index:500;transition:left 0.3s ease;display:flex;flex-direction:column;box-shadow:4px 0 20px rgba(0,0,0,0.3);">

    <!-- Header -->
    <div
        style="padding:16px 20px;border-bottom:1px solid var(--incoming-chat-border);display:flex;align-items:center;gap:10px;">
        <button onclick="cerrarDataRoom()"
            style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;align-items:center;transition:all 0.2s;flex-shrink:0;"
            onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='var(--text-color)'"
            onmouseout="this.style.background='none';this.style.color='var(--icon-color)'">
            <span class="material-symbols-rounded" style="font-size:20px;">arrow_back</span>
        </button>
        <div style="flex:1;min-width:0;">
            <h2 id="dataroom-titulo"
                style="font-size:14px;font-weight:700;color:var(--text-color);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                DataRoom</h2>
            <small id="dataroom-subtitulo" style="font-size:11px;color:var(--icon-color);">Seleziona un progetto</small>
        </div>
    </div>

    <!-- Buscador -->
    <div style="padding:12px 16px;border-bottom:1px solid var(--incoming-chat-border);">
        <div
            style="display:flex;align-items:center;gap:8px;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:8px 12px;">
            <span class="material-symbols-rounded" style="font-size:16px;color:var(--icon-color);">search</span>
            <input type="text" id="search-dataroom" placeholder="Cerca file..." oninput="filtrarDataRoom(this.value)"
                autocomplete="off"
                style="background:transparent;border:none;outline:none;color:var(--text-color);font-size:13px;width:100%;font-family:'Poppins',sans-serif;">
        </div>
    </div>
    <!-- Seleziona tutti -->
    <div
        style="padding:8px 16px;border-bottom:1px solid var(--incoming-chat-border);display:flex;align-items:center;gap:8px;">
        <label onclick="toggleSelezionaTutti(this)" style="cursor:pointer;display:flex;align-items:center;gap:8px;">
            <input type="checkbox" id="checkbox-seleziona-tutti" style="display:none;">
            <div id="box-seleziona-tutti"
                style="
            width:18px;height:18px;
            border-radius:6px;
            border:2px solid var(--incoming-chat-border);
            background:transparent;
            display:flex;align-items:center;justify-content:center;
            transition:all 0.2s ease;
            flex-shrink:0;">
                <span class="material-symbols-rounded"
                    style="font-size:13px;color:#fff;opacity:0;transition:opacity 0.15s;">check</span>
            </div>
            <span style="font-size:12px;color:var(--icon-color);font-weight:500;user-select:none;">Seleziona
                tutti</span>
        </label>
        <span id="contatore-selezionati"
            style="font-size:11px;color:#6366f1;margin-left:auto;display:none;font-weight:600;"></span>
    </div>

    <!-- Contenido -->
    <div style="flex:1;overflow-y:auto;padding:12px 16px;" id="dataroom-contenido"></div>

    <!-- Footer -->
    <div style="padding:12px 16px;border-top:1px solid var(--incoming-chat-border);">
        <span id="dataroom-stats" style="font-size:11px;color:var(--icon-color);">—</span>
    </div>
</div>

<!-- Modal Editar Documento -->
<div id="modal-editar-doc"
    style="display:none;position:fixed;inset:0;z-index:600;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div
        style="background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:20px;padding:28px;width:100%;max-width:440px;margin:0 16px;box-shadow:0 20px 60px rgba(0,0,0,0.4);">

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <span class="material-symbols-rounded" style="color:#6366f1;font-size:22px;">edit_document</span>
                <h3 style="font-size:15px;font-weight:700;color:var(--text-color);margin:0;">Modifica Documento</h3>
            </div>
            <button onclick="chiudiEditarDoc()"
                style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;transition:all 0.2s;"
                onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='#ef4444'"
                onmouseout="this.style.background='none';this.style.color='var(--icon-color)'">
                <span class="material-symbols-rounded" style="font-size:20px;">close</span>
            </button>
        </div>

        <input type="hidden" id="edit-doc-id">

        <div style="display:flex;flex-direction:column;gap:12px;">
            <div>
                <label style="font-size:11px;color:var(--icon-color);display:block;margin-bottom:4px;">Nome
                    documento</label>
                <input id="edit-doc-nombre" type="text"
                    style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                    onfocus="this.style.borderColor='#6366f1'"
                    onblur="this.style.borderColor='var(--incoming-chat-border)'">
            </div>

            <div style="display:flex;gap:10px;">
                <div style="flex:1;">
                    <label style="font-size:11px;color:var(--icon-color);display:block;margin-bottom:4px;">Categoria
                        *</label>
                    <select id="edit-doc-categoria" onchange="cargarTipologiasEdit(this.value)"
                        style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;">
                        <option value="">Seleziona...</option>
                    </select>
                </div>
                <div style="flex:1;">
                    <label style="font-size:11px;color:var(--icon-color);display:block;margin-bottom:4px;">Tipologia
                        *</label>
                    <select id="edit-doc-tipologia"
                        style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;">
                        <option value="">Prima categoria...</option>
                    </select>
                </div>
            </div>

            <div>
                <label style="font-size:11px;color:var(--icon-color);display:block;margin-bottom:4px;">Data
                    documento</label>
                <input id="edit-doc-fecha" type="date"
                    style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;">
            </div>

            <div>
                <label
                    style="font-size:11px;color:var(--icon-color);display:block;margin-bottom:4px;">Descrizione</label>
                <textarea id="edit-doc-descripcion" rows="3"
                    style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;resize:none;transition:border-color 0.2s;"
                    onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='var(--incoming-chat-border)'"></textarea>
            </div>
        </div>

        <div style="display:flex;gap:8px;margin-top:16px;">
            <button onclick="guardarEditarDoc()"
                style="flex:1;background:#6366f1;color:#fff;border:none;border-radius:8px;padding:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:background 0.2s;"
                onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                Salva
            </button>
            <button onclick="chiudiEditarDoc()"
                style="background:transparent;color:var(--icon-color);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:10px 16px;font-size:13px;cursor:pointer;font-family:'Poppins',sans-serif;">
                Annulla
            </button>
        </div>
    </div>
</div>

<!-- Floating toolbar -->
<div id="floating-doc-toolbar"
    style="display:none;position:fixed;left:408px;top:50%;transform:translateY(-50%);
           flex-direction:column;gap:8px;z-index:501;
           background:var(--incoming-chat-bg);
           border:1px solid var(--incoming-chat-border);
           border-radius:14px;
           padding:8px;
           box-shadow:4px 0 20px rgba(0,0,0,0.2);">

    <!-- Chat -->
    <button id="toolbar-btn-chat" onclick="toolbarChat()" title="Chatta con i documenti selezionati"
        style="width:40px;height:40px;border-radius:10px;border:none;background:transparent;
           cursor:pointer;display:flex;align-items:center;justify-content:center;
           color:#6366f1;transition:all 0.2s;opacity:0.35;"
        disabled onmouseover="if(!this.disabled) this.style.background='#6366f115'"
        onmouseout="this.style.background='transparent'">
        <span class="material-symbols-rounded" style="font-size:22px;">chat</span>
    </button>
    <span id="toolbar-chat-tooltip"
        style="font-size:10px;color:var(--icon-color);text-align:center;
           max-width:40px;line-height:1.2;word-break:break-word;"></span>

    <div style="height:1px;background:var(--incoming-chat-border);margin:0 4px;"></div>

    <!-- Download -->
    <button onclick="toolbarDownload()" title="Scarica selezionati"
        style="width:40px;height:40px;border-radius:10px;border:none;background:transparent;
               cursor:pointer;display:flex;align-items:center;justify-content:center;
               color:#10b981;transition:all 0.2s;"
        onmouseover="this.style.background='#10b98115'" onmouseout="this.style.background='transparent'">
        <span class="material-symbols-rounded" style="font-size:22px;">download</span>
    </button>

    <div style="height:1px;background:var(--incoming-chat-border);margin:0 4px;"></div>

    <!-- Elimina -->
    <button onclick="toolbarElimina()" title="Elimina selezionati"
        style="width:40px;height:40px;border-radius:10px;border:none;background:transparent;
               cursor:pointer;display:flex;align-items:center;justify-content:center;
               color:#ef4444;transition:all 0.2s;"
        onmouseover="this.style.background='#ef444415'" onmouseout="this.style.background='transparent'">
        <span class="material-symbols-rounded" style="font-size:22px;">delete</span>
    </button>

</div>

<script>
    let dataroomProyectoId = null;
    let dataroomProyectoNombre = null;
    let dataroomData = null;
    let dropdownAbierto = null;
    window._chatDocHistorial = [];

    window.seleccionarProyectoChat = function(id, nombre) {
        dataroomProyectoId = id;
        dataroomProyectoNombre = nombre;
        abrirDataRoom(id, nombre);
    }

    function abrirDataRoom(id, nombre) {
        const navLateral = document.getElementById('nav-lateral');
        const overlay = document.getElementById('nav-overlay');
        const navToggle = document.getElementById('nav-toggle');

        if (navLateral) navLateral.style.left = '-420px';
        if (overlay) overlay.style.display = 'none';
        if (navToggle) navToggle.querySelector('.material-symbols-rounded').textContent = 'chevron_right';
        navAbierto = false;

        document.getElementById('dataroom-titulo').textContent = nombre;
        document.getElementById('dataroom-subtitulo').textContent = 'Caricamento...';
        document.getElementById('dataroom-stats').textContent = '—';

        document.getElementById('search-dataroom').value = '';
        document.getElementById('nav-dataroom').style.left = '0px';
        activarBtnUpload(true);
        cargarDataRoom(id);
        document.getElementById('chatContainer').style.marginLeft = '400px';
        document.querySelector('.typing-container').style.paddingLeft = '410px';
        document.getElementById('upload-panel').style.paddingLeft = '410px';
        // Al inicio de abrirDataRoom, después de asignar dataroomProyectoId
        localStorage.setItem('ultimo_proyecto', JSON.stringify({
            id: id,
            nombre: nombre
        }));
    }

    function cerrarDataRoom() {
        addBotMessage(`
            <div style="display:flex;flex-direction:column;gap:10px;">
                <p style="margin:0;font-size:13px;color:var(--text-color);">Vuoi uscire dal progetto <strong>${dataroomProyectoNombre}</strong>?</p>
                <div style="display:flex;gap:8px;">
                    <button onclick="confirmarSalirDataRoom()"
                        style="background:#6366f1;color:#fff;border:none;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:background 0.2s;"
                        onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                        Sì, esci
                    </button>
                    <button onclick="this.closest('.chat').remove()"
                        style="background:transparent;color:var(--icon-color);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:8px 16px;font-size:12px;cursor:pointer;font-family:'Poppins',sans-serif;">
                        Annulla
                    </button>
                </div>
            </div>`);
    }

    function confirmarSalirDataRoom() {
        if (typeof closeFileViewer === 'function') closeFileViewer();

        document.getElementById('nav-dataroom').style.left = '-420px';
        activarBtnUpload(false);
        cerrarDropdownActivo();
        //limpiar toolbar y checkboxes
        document.getElementById('floating-doc-toolbar').style.display = 'none';
        document.getElementById('chat-doc-confirm-card')?.remove();
        document.getElementById('banner-modo-doc')?.remove();
        window._modoChatDoc = false;
        window._chatDocIds = null;
        window._chatDocHistorial = [];

        document.getElementById('chatContainer').style.marginLeft = '0px';
        document.querySelector('.typing-container').style.paddingLeft = '10px';
        document.getElementById('upload-panel').style.paddingLeft = '10px';

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
                <div id="card-ultimo-progetto" style="flex:1;min-width:220px;max-width:280px;opacity:0;transform:translateY(16px);transition:opacity 0.3s ease, transform 0.3s ease;">
                    <button onclick="aprireUltimoProgetto()" style="width:100%;display:flex;align-items:center;gap:14px;padding:16px 20px;background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:16px;cursor:pointer;transition:all 0.2s;font-family:'Poppins',sans-serif;text-align:left;"
                        onmouseover="this.style.borderColor='#f59e0b';this.style.background='#f59e0b08';this.style.transform='translateY(-2px)'"
                        onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='var(--incoming-chat-bg)';this.style.transform='translateY(0)'">
                        <div style="width:40px;height:40px;border-radius:12px;background:#f59e0b20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span class="material-symbols-rounded" style="font-size:22px;color:#f59e0b;">folder_open</span>
                        </div>
                        <div style="min-width:0;">
                            <p style="margin:0;font-size:11px;color:var(--icon-color);font-weight:500;">Continua con</p>
                            <p id="ultimo-progetto-nombre" style="margin:0;font-size:13px;font-weight:700;color:var(--text-color);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;"></p>
                        </div>
                        <span class="material-symbols-rounded" style="font-size:18px;color:var(--icon-color);margin-left:auto;flex-shrink:0;">arrow_forward</span>
                    </button>
                </div>
                <div id="card-apri-progetto" style="flex:1;min-width:220px;max-width:280px;opacity:0;transform:translateY(16px);transition:opacity 0.3s ease 0.15s, transform 0.3s ease 0.15s;">
                    <button onclick="toggleNav()" style="width:100%;display:flex;align-items:center;gap:14px;padding:16px 20px;background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:16px;cursor:pointer;transition:all 0.2s;font-family:'Poppins',sans-serif;text-align:left;"
                        onmouseover="this.style.borderColor='#6366f1';this.style.background='#6366f108';this.style.transform='translateY(-2px)'"
                        onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='var(--incoming-chat-bg)';this.style.transform='translateY(0)'">
                        <div style="width:40px;height:40px;border-radius:12px;background:#6366f120;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <span class="material-symbols-rounded" style="font-size:22px;color:#6366f1;">folder</span>
                        </div>
                        <div style="min-width:0;">
                            <p style="margin:0;font-size:11px;color:var(--icon-color);font-weight:500;">Scegli un progetto</p>
                            <p style="margin:0;font-size:13px;font-weight:700;color:var(--text-color);">Apri Progetto</p>
                        </div>
                        <span class="material-symbols-rounded" style="font-size:18px;color:var(--icon-color);margin-left:auto;flex-shrink:0;">arrow_forward</span>
                    </button>
                </div>
            </div>
        </div>`;

        sessionId = generateUUID();
        localStorage.setItem('chat_session_id', sessionId);
        proyectosCargados = null;
        iniciarBienvenida();
    }

    async function cargarDataRoom(proyectoId, nuevosIds = []) {
        documentosNuevos = nuevosIds;
        const contenido = document.getElementById('dataroom-contenido');
        contenido.innerHTML = `
        <div style="text-align:center;padding:40px 0;color:var(--icon-color);">
            <span class="material-symbols-rounded" style="font-size:36px;opacity:0.5;display:block;margin-bottom:8px;animation:spin 1s linear infinite;">progress_activity</span>
            <p style="font-size:13px;">Caricamento...</p>
        </div>`;

        const res = await fetch(`/proyectos/${proyectoId}/dataroom`);
        dataroomData = await res.json();
        renderDataRoom(dataroomData);

        // Resaltar después del render
        if (documentosNuevos.length > 0) {
            setTimeout(() => {
                documentosNuevos.forEach(id => resaltarDocumentoNuevo(id));
                documentosNuevos = [];
            }, 300);
        }
    }

    function renderDataRoom(data) {
        const contenido = document.getElementById('dataroom-contenido');

        if (!data.categorias || data.categorias.length === 0) {
            contenido.innerHTML = `
                <div style="text-align:center;padding:40px 0;color:var(--icon-color);">
                    <span class="material-symbols-rounded" style="font-size:40px;opacity:0.3;display:block;margin-bottom:8px;">folder_off</span>
                    <p style="font-size:13px;">Nessuna categoria trovata</p>
                </div>`;
            return;
        }

        let totalFiles = 0;
        let html = '';
        let documentosNuevos = [];

        data.categorias.forEach((cat, idx) => {
            const count = cat.documentos.length;
            totalFiles += count;
            const tieneNuevos = documentosNuevos.some(id =>

                cat.documentos.some(doc => doc.id === id)
            );
            const isOpen = (idx === 0 && count > 0) || tieneNuevos;

            html += `
                <div class="dataroom-categoria" style="margin-bottom:8px;">
                    <button onclick="toggleCategoria(${cat.id})"
                        style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;border:none;background:var(--outgoing-chat-bg);cursor:pointer;color:var(--text-color);font-family:'Poppins',sans-serif;font-size:13px;transition:all 0.2s;"
                        onmouseover="this.style.background='var(--icon-hover-bg)'"
                        onmouseout="this.style.background='var(--outgoing-chat-bg)'">
                        <span class="material-symbols-rounded" style="font-size:22px;color:#f59e0b;flex-shrink:0;">${isOpen ? 'folder_open' : 'folder'}</span>
                        <div style="flex:1;text-align:left;">
                            <p style="margin:0;font-weight:600;font-size:13px;">${cat.nombre}</p>
                            <small style="color:var(--icon-color);font-size:11px;">${count} file${count !== 1 ? 's' : ''}</small>
                        </div>
                        <span id="chevron-${cat.id}" class="material-symbols-rounded"
                            style="font-size:18px;color:var(--icon-color);transition:transform 0.2s;${isOpen ? 'transform:rotate(180deg)' : ''}">expand_more</span>
                    </button>

                    <div id="cat-files-${cat.id}"
                        style="overflow:hidden;transition:max-height 0.3s ease;max-height:${isOpen ? '9999px' : '0px'};">
                        ${count === 0
                            ? `<p style="font-size:12px;color:var(--icon-color);padding:10px 14px;margin:0;">Nessun file in questa categoria.</p>`
                            : cat.documentos.map(doc => renderDocumento(doc)).join('')
                        }
                    </div>
                </div>`;
        });

        contenido.innerHTML = html;
        document.getElementById('dataroom-subtitulo').textContent = `${data.categorias.length} categorie`;
        document.getElementById('dataroom-stats').textContent = `${totalFiles} file totali`;
    }

    function renderDocumento(doc, isNew = false) {
        const fecha = doc.fecha_documento ?
            new Date(doc.fecha_documento).toLocaleDateString('it-IT') :
            '—';

        return `
            <div class="dataroom-file-item"
                id="doc-item-${doc.id}"
                data-nombre="${(doc.nombre || '').toLowerCase()}"
                data-desc="${(doc.descripcion || '').toLowerCase()}"
                style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px 10px 14px;border-left:2px solid ${isNew ? '#6366f1' : 'var(--incoming-chat-border)'};margin:2px 0 2px 12px;border-radius:0 8px 8px 0;transition:all 0.4s;${isNew ? 'background:#6366f108;' : ''}position:relative;">

               <!-- Icono + Checkbox -->
                <div style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;gap:4px;">
                    <div style="cursor:pointer;" onclick="abrirVisorDocumento(${doc.id}, '${doc.nombre.replace(/'/g, "\\'")}', '${doc.mime_type}')">
                        ${getFileIcon(doc.mime_type)}
                    </div>
                    <label onclick="event.stopPropagation();toggleCheckbox(this);" style="cursor:pointer;margin-top:4px;">
                        <input type="checkbox"
                            class="doc-checkbox"
                            data-id="${doc.id}"
                            style="display:none;">
                        <div class="doc-check-box" style="
                            width:18px;height:18px;
                            border-radius:6px;
                            border:2px solid var(--incoming-chat-border);
                            background:transparent;
                            display:flex;align-items:center;justify-content:center;
                            transition:all 0.2s ease;
                            cursor:pointer;">
                            <span class="material-symbols-rounded" style="font-size:13px;color:#fff;opacity:0;transition:opacity 0.15s;">check</span>
                        </div>
                    </label>
                </div>

                <!-- Info clickeable -->
                <div style="flex:1;min-width:0;cursor:pointer;" onclick="abrirVisorDocumento(${doc.id}, '${doc.nombre.replace(/'/g, "\\'")}', '${doc.mime_type}')">
                    <p style="margin:0;font-size:12px;font-weight:600;color:var(--text-color);word-break:break-word;">${doc.nombre}</p>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:3px;flex-wrap:wrap;">
                        ${doc.tipologia
                            ? `<span style="font-size:10px;color:var(--icon-color);display:flex;align-items:center;gap:3px;">
                                <span class="material-symbols-rounded" style="font-size:12px;">style</span>${doc.tipologia}
                               </span>`
                            : ''}
                        <span style="font-size:10px;color:var(--icon-color);display:flex;align-items:center;gap:3px;">
                            <span class="material-symbols-rounded" style="font-size:12px;">calendar_today</span>${fecha}
                        </span>
                    </div>
                    ${doc.descripcion
                        ? `<p style="margin:5px 0 0;font-size:11px;color:var(--icon-color);line-height:1.5;word-break:break-word;white-space:normal;">${doc.descripcion}</p>`
                        : ''}
                </div>

                <!-- Tres puntos -->
                <div style="position:relative;flex-shrink:0;">
                    <button onclick="event.stopPropagation();toggleDropdownDoc(${doc.id})"
                        style="width:26px;height:26px;border:none;background:transparent;cursor:pointer;color:var(--icon-color);border-radius:6px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
                        onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='var(--text-color)'"
                        onmouseout="this.style.background='transparent';this.style.color='var(--icon-color)'">
                        <span class="material-symbols-rounded" style="font-size:18px;">more_vert</span>
                    </button>

                    <!-- Dropdown menu -->
                    <div id="dropdown-doc-${doc.id}"
                        style="display:none;position:absolute;right:0;top:30px;background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:10px;min-width:150px;box-shadow:0 8px 24px rgba(0,0,0,0.3);z-index:999;overflow:hidden;">

                        <button onclick="event.stopPropagation();abrirVisorDocumento(${doc.id}, '${doc.nombre.replace(/'/g, "\\'")}', '${doc.mime_type}');cerrarDropdownActivo()"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 14px;color:var(--text-color);background:none;border:none;font-family:'Poppins',sans-serif;font-size:12px;cursor:pointer;transition:background 0.2s;text-align:left;"
                            onmouseover="this.style.background='var(--icon-hover-bg)'"
                            onmouseout="this.style.background='transparent'">
                            <span class="material-symbols-rounded" style="font-size:16px;color:#6366f1;">visibility</span>
                            Visualizza
                        </button>

                        <button onclick="event.stopPropagation();downloadDocumento(${doc.id});cerrarDropdownActivo()"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 14px;color:var(--text-color);background:none;border:none;font-family:'Poppins',sans-serif;font-size:12px;cursor:pointer;transition:background 0.2s;text-align:left;"
                            onmouseover="this.style.background='var(--icon-hover-bg)'"
                            onmouseout="this.style.background='transparent'">
                            <span class="material-symbols-rounded" style="font-size:16px;color:#10b981;">download</span>
                            Scarica
                        </button>

                        <button onclick="event.stopPropagation();abrirEditarDoc(${doc.id});cerrarDropdownActivo()"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 14px;color:var(--text-color);background:none;border:none;font-family:'Poppins',sans-serif;font-size:12px;cursor:pointer;transition:background 0.2s;text-align:left;"
                            onmouseover="this.style.background='var(--icon-hover-bg)'"
                            onmouseout="this.style.background='transparent'">
                            <span class="material-symbols-rounded" style="font-size:16px;color:#f59e0b;">edit</span>
                            Modifica
                        </button>

                        <hr style="margin:4px 0;border:none;border-top:1px solid var(--incoming-chat-border);">

                        <button onclick="event.stopPropagation();eliminarDocumento(${doc.id}, '${doc.nombre.replace(/'/g, "\\'")}', this);cerrarDropdownActivo()"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 14px;color:#ef4444;background:none;border:none;font-family:'Poppins',sans-serif;font-size:12px;cursor:pointer;transition:background 0.2s;text-align:left;"
                            onmouseover="this.style.background='#ef444415'"
                            onmouseout="this.style.background='transparent'">
                            <span class="material-symbols-rounded" style="font-size:16px;">delete</span>
                            Elimina
                        </button>
                    </div>
                </div>
            </div>`;
    }

    // ---- Dropdown tres puntos ----
    function toggleDropdownDoc(docId) {
        const dd = document.getElementById(`dropdown-doc-${docId}`);
        if (!dd) return;

        if (dropdownAbierto && dropdownAbierto !== docId) {
            cerrarDropdownActivo();
        }

        const isVisible = dd.style.display === 'block';
        dd.style.display = isVisible ? 'none' : 'block';
        dropdownAbierto = isVisible ? null : docId;
    }

    function cerrarDropdownActivo() {
        if (dropdownAbierto) {
            const dd = document.getElementById(`dropdown-doc-${dropdownAbierto}`);
            if (dd) dd.style.display = 'none';
            dropdownAbierto = null;
        }
    }

    // Cerrar dropdown al click fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('[id^="dropdown-doc-"]') && !e.target.closest(
                'button[onclick*="toggleDropdownDoc"]')) {
            cerrarDropdownActivo();
        }
    });

    // ---- Visor ----
    async function abrirVisorDocumento(id, nombre, mimeType) {
        if (typeof window.openFileViewer === 'function') {
            window.openFileViewer(id, nombre, mimeType);
        }
    }

    function getExtFromMime(mimeType) {
        if (!mimeType) return 'file';
        if (mimeType.includes('pdf')) return 'pdf';
        if (mimeType.includes('jpeg') || mimeType.includes('jpg')) return 'jpg';
        if (mimeType.includes('png')) return 'png';
        if (mimeType.includes('gif')) return 'gif';
        if (mimeType.includes('webp')) return 'webp';
        if (mimeType.includes('word')) return 'docx';
        if (mimeType.includes('excel')) return 'xlsx';
        return 'file';
    }

    // ---- Editar documento ----
    async function abrirEditarDoc(id) {
        document.getElementById('edit-doc-id').value = id;

        // Cargar datos del documento
        const res = await fetch(`/documentos/${id}/info`);
        const data = await res.json();

        document.getElementById('edit-doc-nombre').value = data.nombre || '';
        document.getElementById('edit-doc-fecha').value = data.fecha_documento || '';
        document.getElementById('edit-doc-descripcion').value = data.descripcion || '';

        // Cargar categorías
        const resCats = await fetch(`/proyectos/${dataroomProyectoId}/categorias`);
        const cats = await resCats.json();
        const selCat = document.getElementById('edit-doc-categoria');
        selCat.innerHTML = '<option value="">Seleziona...</option>';
        cats.forEach(c => {
            selCat.innerHTML +=
                `<option value="${c.id}" ${c.id == data.categoria_id ? 'selected' : ''}>${c.nombre}</option>`;
        });

        // Cargar tipologías de la categoría actual
        await cargarTipologiasEdit(data.categoria_id, data.tipologia_id);

        document.getElementById('modal-editar-doc').style.display = 'flex';
    }

    async function cargarTipologiasEdit(categoriaId, selectedId = null) {
        const sel = document.getElementById('edit-doc-tipologia');
        sel.innerHTML = '<option value="">Caricamento...</option>';
        if (!categoriaId) {
            sel.innerHTML = '<option value="">Prima categoria...</option>';
            return;
        }

        const res = await fetch(`/categorias/${categoriaId}/tipologias`);
        const tipologias = await res.json();
        sel.innerHTML = '<option value="">Seleziona...</option>';
        tipologias.forEach(t => {
            sel.innerHTML +=
                `<option value="${t.id}" ${t.id == selectedId ? 'selected' : ''}>${t.nombre}</option>`;
        });
    }

    async function guardarEditarDoc() {
        const id = document.getElementById('edit-doc-id').value;
        const nombre = document.getElementById('edit-doc-nombre').value.trim();
        const categoriaId = document.getElementById('edit-doc-categoria').value;
        const tipologiaId = document.getElementById('edit-doc-tipologia').value;
        const fecha = document.getElementById('edit-doc-fecha').value;
        const descripcion = document.getElementById('edit-doc-descripcion').value;

        if (!nombre || !categoriaId || !tipologiaId) return;

        const res = await fetch(`/documentos/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                nombre,
                categoria_id: categoriaId,
                tipologia_id: tipologiaId,
                fecha_documento: fecha,
                descripcion
            })
        });

        const data = await res.json();
        if (data.success) {
            chiudiEditarDoc();
            cargarDataRoom(dataroomProyectoId);
        }
    }

    function chiudiEditarDoc() {
        document.getElementById('modal-editar-doc').style.display = 'none';
    }

    // ---- Resaltado nuevo ----
    function resaltarDocumentoNuevo(docId) {
        const el = document.getElementById(`doc-item-${docId}`);
        if (!el) return;

        // Aplicar resaltado
        el.style.borderLeftColor = '#6366f1';
        el.style.background = '#6366f110';
        el.style.transition = 'all 0.5s ease';

        // Scroll al elemento
        el.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        // Quitar resaltado después de 4 segundos
        setTimeout(() => {
            el.style.borderLeftColor = 'var(--incoming-chat-border)';
            el.style.background = 'transparent';
        }, 4000);
    }

    // ---- Helpers ----
    function getFileIcon(mimeType) {
        const base =
            'width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0;';
        if (!mimeType) return `<div style="${base}background:#6b728020;color:#6b7280;">?</div>`;
        if (mimeType.includes('pdf')) return `<div style="${base}background:#ef444420;color:#ef4444;">PDF</div>`;
        if (mimeType.includes('word') || mimeType.includes('document'))
            return `<div style="${base}background:#3b82f620;color:#3b82f6;">DOC</div>`;
        if (mimeType.includes('excel') || mimeType.includes('spreadsheet'))
            return `<div style="${base}background:#10b98120;color:#10b981;">XLS</div>`;
        if (mimeType.includes('powerpoint') || mimeType.includes('presentation'))
            return `<div style="${base}background:#f59e0b20;color:#f59e0b;">PPT</div>`;
        if (mimeType.includes('jpeg') || mimeType.includes('jpg'))
            return `<div style="${base}background:#ec489920;color:#ec4899;">JPG</div>`;
        if (mimeType.includes('png')) return `<div style="${base}background:#8b5cf620;color:#8b5cf6;">PNG</div>`;
        if (mimeType.includes('gif')) return `<div style="${base}background:#06b6d420;color:#06b6d4;">GIF</div>`;
        if (mimeType.includes('zip') || mimeType.includes('rar'))
            return `<div style="${base}background:#f59e0b20;color:#f59e0b;">ZIP</div>`;
        if (mimeType.includes('text')) return `<div style="${base}background:#6366f120;color:#6366f1;">TXT</div>`;
        return `<div style="${base}background:#6b728020;color:#6b7280;">FILE</div>`;
    }

    function toggleCategoria(catId) {
        const files = document.getElementById(`cat-files-${catId}`);
        const chevron = document.getElementById(`chevron-${catId}`);
        const btn = files.previousElementSibling;
        const folderIcon = btn.querySelector('.material-symbols-rounded');
        const isOpen = files.style.maxHeight !== '0px';

        files.style.maxHeight = isOpen ? '0px' : '9999px';
        chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        folderIcon.textContent = isOpen ? 'folder' : 'folder_open';
    }

    function filtrarDataRoom(query) {
        const q = query.toLowerCase();
        document.querySelectorAll('.dataroom-file-item').forEach(item => {
            const nombre = item.dataset.nombre || '';
            const desc = item.dataset.desc || '';
            item.style.display = (nombre.includes(q) || desc.includes(q)) ? 'flex' : 'none';
        });
    }

    function downloadDocumento(id) {
        window.open(`/documentos/${id}/download`, '_blank');
    }

    async function eliminarDocumento(id, nombre, btn) {
        const result = await Swal.fire({
            title: 'Eliminare il documento?',
            html: `<strong>${nombre}</strong> verrà eliminato definitivamente.`,
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

        const res = await fetch(`/documentos/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById(`doc-item-${id}`)?.remove();
            cargarDataRoom(dataroomProyectoId);
        }
    }

    function mostrarFilesNuevos(resultados) {
        const contenido = document.getElementById('dataroom-contenido');
        const stats = document.getElementById('dataroom-stats');

        const completati = resultados.filter(r => r.success);
        const errori = resultados.filter(r => !r.success);

        let html = `
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <p style="font-size:12px;font-weight:600;color:var(--text-color);margin:0;">
                <span class="material-symbols-rounded" style="font-size:14px;vertical-align:middle;color:#10b981;">check_circle</span>
                ${completati.length} file appena caricati
            </p>
            <button onclick="cargarDataRoom(dataroomProyectoId)"
                style="display:flex;align-items:center;gap:6px;background:var(--outgoing-chat-bg);color:var(--icon-color);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;transition:all 0.2s;"
                onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)'">
                <span class="material-symbols-rounded" style="font-size:14px;">refresh</span>
                Aggiorna DataRoom
            </button>
        </div>`;

        completati.forEach(r => {
            html += `
            <div class="dataroom-file-item"
                id="doc-item-${r.id}"
                style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px 10px 14px;border-left:2px solid #10b981;margin:2px 0 2px 0;border-radius:8px;background:#10b98108;transition:all 0.4s;position:relative;">
                <div style="flex-shrink:0;margin-top:2px;cursor:pointer;"
                    onclick="abrirVisorDocumento(${r.id}, '${r.nombre.replace(/'/g, "\\'")}', '${r.mime_type || ''}')">
                    ${getFileIcon(r.mime_type || '')}
                </div>
                <div style="flex:1;min-width:0;cursor:pointer;"
                    onclick="abrirVisorDocumento(${r.id}, '${r.nombre.replace(/'/g, "\\'")}', '${r.mime_type || ''}')">
                    <p style="margin:0;font-size:12px;font-weight:600;color:var(--text-color);word-break:break-word;">${r.nombre}</p>
                    <div style="display:flex;align-items:center;gap:8px;margin-top:3px;flex-wrap:wrap;">
                        ${r.categoria ? `<span style="font-size:10px;color:#10b981;display:flex;align-items:center;gap:3px;"><span class="material-symbols-rounded" style="font-size:12px;">folder</span>${r.categoria}</span>` : ''}
                        ${r.tipologia ? `<span style="font-size:10px;color:var(--icon-color);display:flex;align-items:center;gap:3px;"><span class="material-symbols-rounded" style="font-size:12px;">style</span>${r.tipologia}</span>` : ''}
                    </div>
                </div>
                <div style="position:relative;flex-shrink:0;">
                    <button onclick="event.stopPropagation();toggleDropdownDoc(${r.id})"
                        style="width:26px;height:26px;border:none;background:transparent;cursor:pointer;color:var(--icon-color);border-radius:6px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
                        onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='var(--text-color)'"
                        onmouseout="this.style.background='transparent';this.style.color='var(--icon-color)'">
                        <span class="material-symbols-rounded" style="font-size:18px;">more_vert</span>
                    </button>
                    <div id="dropdown-doc-${r.id}"
                        style="display:none;position:absolute;right:0;top:30px;background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:10px;min-width:150px;box-shadow:0 8px 24px rgba(0,0,0,0.3);z-index:999;overflow:hidden;">
                        <button onclick="event.stopPropagation();abrirVisorDocumento(${r.id}, '${r.nombre.replace(/'/g, "\\'")}', '${r.mime_type || ''}');cerrarDropdownActivo()"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 14px;color:var(--text-color);background:none;border:none;font-family:'Poppins',sans-serif;font-size:12px;cursor:pointer;transition:background 0.2s;text-align:left;"
                            onmouseover="this.style.background='var(--icon-hover-bg)'"
                            onmouseout="this.style.background='transparent'">
                            <span class="material-symbols-rounded" style="font-size:16px;color:#6366f1;">visibility</span>
                            Visualizza
                        </button>
                        <button onclick="event.stopPropagation();downloadDocumento(${r.id});cerrarDropdownActivo()"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 14px;color:var(--text-color);background:none;border:none;font-family:'Poppins',sans-serif;font-size:12px;cursor:pointer;transition:background 0.2s;text-align:left;"
                            onmouseover="this.style.background='var(--icon-hover-bg)'"
                            onmouseout="this.style.background='transparent'">
                            <span class="material-symbols-rounded" style="font-size:16px;color:#10b981;">download</span>
                            Scarica
                        </button>
                        <button onclick="event.stopPropagation();abrirEditarDoc(${r.id});cerrarDropdownActivo()"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 14px;color:var(--text-color);background:none;border:none;font-family:'Poppins',sans-serif;font-size:12px;cursor:pointer;transition:background 0.2s;text-align:left;"
                            onmouseover="this.style.background='var(--icon-hover-bg)'"
                            onmouseout="this.style.background='transparent'">
                            <span class="material-symbols-rounded" style="font-size:16px;color:#f59e0b;">edit</span>
                            Modifica
                        </button>
                        <hr style="margin:4px 0;border:none;border-top:1px solid var(--incoming-chat-border);">
                        <button onclick="event.stopPropagation();eliminarDocumento(${r.id}, '${r.nombre.replace(/'/g, "\\'")}', this);cerrarDropdownActivo()"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:9px 14px;color:#ef4444;background:none;border:none;font-family:'Poppins',sans-serif;font-size:12px;cursor:pointer;transition:background 0.2s;text-align:left;"
                            onmouseover="this.style.background='#ef444415'"
                            onmouseout="this.style.background='transparent'">
                            <span class="material-symbols-rounded" style="font-size:16px;">delete</span>
                            Elimina
                        </button>
                    </div>
                </div>
            </div>`;
        });

        if (errori.length > 0) {
            html += `<div style="margin-top:8px;padding:10px 12px;border-radius:8px;background:#ef444410;border:1px solid #ef444430;">
            <p style="font-size:11px;color:#ef4444;margin:0;">
                <span class="material-symbols-rounded" style="font-size:13px;vertical-align:middle;">warning</span>
                ${errori.length} file con errore → salvati in Senza Categoria
            </p>
        </div>`;
        }

        contenido.innerHTML = html;
        stats.textContent = `${completati.length} file caricati`;
    }

    function toggleCheckbox(label) {
        const input = label.querySelector('.doc-checkbox');
        const box = label.querySelector('.doc-check-box');
        const checkIcon = box.querySelector('span');

        input.checked = !input.checked;

        if (input.checked) {
            box.style.background = '#6366f1';
            box.style.borderColor = '#6366f1';
            checkIcon.style.opacity = '1';
        } else {
            box.style.background = 'transparent';
            box.style.borderColor = 'var(--incoming-chat-border)';
            checkIcon.style.opacity = '0';
        }

        onCheckboxChange();
    }

    function onCheckboxChange() {
        const checked = document.querySelectorAll('.doc-checkbox:checked');
        const total = document.querySelectorAll('.doc-checkbox').length;
        const toolbar = document.getElementById('floating-doc-toolbar');
        const counter = document.getElementById('contatore-selezionati');

        // Toolbar
        toolbar.style.display = checked.length > 0 ? 'flex' : 'none';

        // Contador
        if (checked.length > 0) {
            counter.style.display = 'inline';
            counter.textContent = `${checked.length} selezionati`;
        } else {
            counter.style.display = 'none';
        }

        // Validar btn chat
        validarBtnChat();

        // Si la card ya está visible → actualizarla automáticamente
        if (document.getElementById('chat-doc-confirm-card')) {
            toolbarChat();
        }

        // Sincronizar "seleziona tutti"
        const master = document.getElementById('checkbox-seleziona-tutti');
        const masterBox = document.getElementById('box-seleziona-tutti');
        const masterIcon = masterBox?.querySelector('span');
        if (!master) return;

        const allChecked = checked.length === total && total > 0;
        master.checked = allChecked;

        if (allChecked) {
            masterBox.style.background = '#6366f1';
            masterBox.style.borderColor = '#6366f1';
            if (masterIcon) masterIcon.style.opacity = '1';
        } else {
            masterBox.style.background = 'transparent';
            masterBox.style.borderColor = 'var(--incoming-chat-border)';
            if (masterIcon) masterIcon.style.opacity = '0';
        }
    }

    async function validarBtnChat() {
        const btnChat = document.getElementById('toolbar-btn-chat');
        const tooltip = document.getElementById('toolbar-chat-tooltip');
        if (!btnChat) return;

        const checked = document.querySelectorAll('.doc-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.dataset.id);

        if (ids.length === 0) return;

        try {
            const res = await fetch('/documentos/check-size', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    ids
                })
            });
            const data = await res.json();

            if (data.con_texto === 0) {
                // Ninguno tiene texto
                setBtnChatState(btnChat, tooltip, false, 'Nessun file leggibile');
            } else if (!data.dentro_limite) {
                // Demasiado texto
                setBtnChatState(btnChat, tooltip, false, 'Contenuto troppo lungo');
            } else if (data.sin_texto > 0) {
                // Algunos sin texto — activar igual pero avisar
                setBtnChatState(btnChat, tooltip, true,
                    `${data.con_texto} leggibili · ${data.sin_texto} ignorati`);
                // Guardar info para mostrar en la card
                window._chatSinTexto = data.sin_texto_nombres;
                window._chatSinTextoIds = data.sin_texto_ids;
            } else {
                // Todo OK
                setBtnChatState(btnChat, tooltip, true,
                    `${data.con_texto} file pronti`);
                window._chatSinTexto = [];
            }
        } catch (e) {
            setBtnChatState(btnChat, tooltip, true, `${ids.length} file`);
        }
    }


    function setBtnChatState(btn, tooltip, active, message) {
        if (active) {
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
            btn.disabled = false;
            btn.style.color = '#6366f1';
        } else {
            btn.style.opacity = '0.35';
            btn.style.cursor = 'not-allowed';
            btn.disabled = true;
            btn.style.color = 'var(--icon-color)';
        }

        // Tooltip
        if (tooltip) {
            tooltip.textContent = message;
        }
    }

    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.doc-checkbox:checked'))
            .map(cb => cb.dataset.id);
    }

    async function toolbarDownload() {
        const ids = getSelectedIds();
        if (!ids.length) return;

        if (ids.length === 1) {
            window.open(`/documentos/${ids[0]}/download`, '_blank');
            return;
        }

        // Múltiples → ZIP
        const btn = document.querySelector('[onclick="toolbarDownload()"]');
        const originalHTML = btn.innerHTML;
        btn.innerHTML =
            '<span class="material-symbols-rounded" style="font-size:22px;animation:spin 1s linear infinite;">autorenew</span>';
        btn.disabled = true;

        try {
            const res = await fetch('/documentos/download-multiple', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    ids
                })
            });

            if (!res.ok) throw new Error();

            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `documenti_${Date.now()}.zip`;
            a.click();
            URL.revokeObjectURL(url);

        } catch (e) {
            agregarRespuestaBot('❌ Errore durante il download. Riprova.');
        } finally {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    }

    async function toolbarElimina() {
        const ids = getSelectedIds();
        if (!ids.length) return;

        const result = await Swal.fire({
            title: `Eliminare ${ids.length} documento${ids.length > 1 ? 'i' : ''}?`,
            text: 'Questa azione non può essere annullata.',
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

        for (const id of ids) {
            await fetch(`/documentos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            document.getElementById(`doc-item-${id}`)?.remove();
        }

        document.getElementById('floating-doc-toolbar').style.display = 'none';
        cargarDataRoom(dataroomProyectoId);
    }

    function toolbarChat() {
        const checked = document.querySelectorAll('.doc-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.dataset.id);
        const nombres = Array.from(checked).map(cb => {
            const item = document.getElementById(`doc-item-${cb.dataset.id}`);
            return item?.querySelector('p')?.textContent?.trim() || `Doc ${cb.dataset.id}`;
        });

        window._chatDocIds = ids;
        window._chatDocNombri = nombres;

        const defaultText = chatContainer.querySelector('.default-text');
        if (defaultText) defaultText.remove();

        const sinTextoAvviso = window._chatSinTexto?.length > 0 ? `
    <div style="display:flex;align-items:flex-start;gap:8px;padding:8px 10px;
                background:#f59e0b10;border:1px solid #f59e0b30;border-radius:8px;">
        <span class="material-symbols-rounded" style="font-size:15px;color:#f59e0b;flex-shrink:0;margin-top:1px;">warning</span>
        <div style="flex:1;">
            <p style="margin:0 0 6px;font-size:11px;color:var(--icon-color);line-height:1.5;">
                <strong style="color:#f59e0b;">${window._chatSinTexto.length} file ignorati</strong>
                — non leggibili (PDF scansionati o formato non supportato)
            </p>
            <button onclick="rimuovereTuttiIgnorati()"
                style="display:flex;align-items:center;gap:4px;background:#f59e0b;color:#fff;
                       border:none;border-radius:6px;padding:4px 10px;font-size:11px;
                       font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;
                       transition:background 0.2s;"
                onmouseover="this.style.background='#d97706'"
                onmouseout="this.style.background='#f59e0b'">
                <span class="material-symbols-rounded" style="font-size:14px;">remove_selection</span>
                Rimuovi tutti i non leggibili
            </button>
        </div>
    </div>` : '';

        const innerHTML = `
        <div class="chat-content">
            <div class="chat-icon">
                <span class="material-symbols-rounded">robot_2</span>
            </div>
            <div class="chat-text">
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:10px;background:#6366f120;
                                    border:1px solid #6366f130;display:flex;align-items:center;
                                    justify-content:center;flex-shrink:0;">
                            <span class="material-symbols-rounded" style="font-size:20px;color:#6366f1;">description</span>
                        </div>
                        <div>
                            <p style="margin:0;font-size:13px;font-weight:700;color:var(--text-color);">
                                Modalità Chat Documenti
                            </p>
                            <p style="margin:0;font-size:11px;color:var(--icon-color);">
                                ${ids.length} file selezionati
                            </p>
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:4px;">
                       ${nombres.map((n, idx) => {
                            const id         = ids[idx];
                            const esIgnorado = window._chatSinTextoIds?.includes(parseInt(id)) ||
                                            window._chatSinTextoIds?.includes(id);
                            return `
                                <div style="display:flex;align-items:center;gap:8px;padding:6px 10px;
                                            background:${esIgnorado ? '#f59e0b08' : 'var(--outgoing-chat-bg)'};
                                            border-radius:8px;
                                            border:1px solid ${esIgnorado ? '#f59e0b40' : 'var(--incoming-chat-border)'};">
                                    <span class="material-symbols-rounded"
                                        style="font-size:14px;color:${esIgnorado ? '#f59e0b' : '#6366f1'};flex-shrink:0;">
                                        ${esIgnorado ? 'warning' : 'draft'}
                                    </span>
                                    <span style="font-size:12px;color:${esIgnorado ? '#f59e0b' : 'var(--text-color)'};
                                                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;">
                                        ${n}
                                        ${esIgnorado ? '<span style="font-size:10px;opacity:0.7;"> · non leggibile</span>' : ''}
                                    </span>
                                    ${esIgnorado ? `
                                    <button onclick="rimuovereDocDaLista('${id}')"
                                        style="border:none;background:transparent;cursor:pointer;
                                            color:#f59e0b;padding:2px;border-radius:4px;display:flex;
                                            flex-shrink:0;transition:all 0.2s;"
                                        onmouseover="this.style.background='#f59e0b20'"
                                        onmouseout="this.style.background='transparent'"
                                        title="Rimuovi dalla selezione">
                                        <span class="material-symbols-rounded" style="font-size:16px;">close</span>
                                    </button>` : ''}
                                </div>`;
                        }).join('')}
                    </div>

                    ${sinTextoAvviso}

                    <p style="margin:0;font-size:12px;color:var(--icon-color);">
                        Vuoi attivare la modalità chat con questi documenti?
                    </p>

                    <div style="display:flex;gap:8px;">
                        <button onclick="attivareChatDocumenti()" id="btn-attiva-modalita"
                            style="flex:1;background:${(window._chatSinTexto?.length > 0) ? '#6366f160' : '#6366f1'};color:#fff;border:none;border-radius:8px;
                                padding:9px;font-size:12px;font-weight:600;
                                cursor:${(window._chatSinTexto?.length > 0) ? 'not-allowed' : 'pointer'};
                                font-family:'Poppins',sans-serif;transition:background 0.2s;
                                display:flex;align-items:center;justify-content:center;gap:6px;
                                opacity:${(window._chatSinTexto?.length > 0) ? '0.5' : '1'};"
                            ${(window._chatSinTexto?.length > 0) ? 'disabled' : ''}
                            onmouseover="if(!this.disabled) this.style.background='#4f46e5'"
                            onmouseout="if(!this.disabled) this.style.background='#6366f1'">
                            <span class="material-symbols-rounded" style="font-size:16px;">chat</span>
                            Sì, attiva modalità
                        </button>
                        <button onclick="document.getElementById('chat-doc-confirm-card').remove();window._chatDocIds=null;"
                            style="background:transparent;color:var(--icon-color);
                                   border:1px solid var(--incoming-chat-border);border-radius:8px;
                                   padding:9px 14px;font-size:12px;cursor:pointer;
                                   font-family:'Poppins',sans-serif;">
                            Annulla
                        </button>
                    </div>
                </div>
            </div>
        </div>`;

        // Si ya existe la card → actualizar contenido con flash
        const existing = document.getElementById('chat-doc-confirm-card');
        if (existing) {
            existing.style.transition = 'opacity 0.15s ease';
            existing.style.opacity = '0.3';
            setTimeout(() => {
                existing.innerHTML = innerHTML;
                existing.style.opacity = '1';
                scrollToBottom();
            }, 150);
            return;
        }

        // Si no existe → crear con fade
        const div = document.createElement('div');
        div.className = 'chat incoming';
        div.id = 'chat-doc-confirm-card';
        div.innerHTML = innerHTML;
        div.style.opacity = '0';
        div.style.transform = 'translateY(12px)';
        div.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
        chatContainer.appendChild(div);
        scrollToBottom();
        setTimeout(() => {
            div.style.opacity = '1';
            div.style.transform = 'translateY(0)';
        }, 30);
    }

    function toggleSelezionaTutti(label) {
        const input = document.getElementById('checkbox-seleziona-tutti');
        const box = document.getElementById('box-seleziona-tutti');
        const icon = box.querySelector('span');

        input.checked = !input.checked;

        if (input.checked) {
            box.style.background = '#6366f1';
            box.style.borderColor = '#6366f1';
            icon.style.opacity = '1';
        } else {
            box.style.background = 'transparent';
            box.style.borderColor = 'var(--incoming-chat-border)';
            icon.style.opacity = '0';
        }

        // Aplicar a todos los checkboxes visibles
        document.querySelectorAll('.doc-checkbox').forEach(cb => {
            const itemVisible = document.getElementById(`doc-item-${cb.dataset.id}`)?.style.display !== 'none';
            if (!itemVisible) return;

            cb.checked = input.checked;
            const lbl = cb.closest('label');
            const b = lbl?.querySelector('.doc-check-box');
            const s = b?.querySelector('span');
            if (!b) return;

            if (input.checked) {
                b.style.background = '#6366f1';
                b.style.borderColor = '#6366f1';
                if (s) s.style.opacity = '1';
            } else {
                b.style.background = 'transparent';
                b.style.borderColor = 'var(--incoming-chat-border)';
                if (s) s.style.opacity = '0';
            }
        });

        onCheckboxChange();
    }

    function attivareChatDocumenti() {
        document.getElementById('chat-doc-confirm-card')?.remove();

        // Bloquear btn chat toolbar
        const btnChat = document.getElementById('toolbar-btn-chat');
        if (btnChat) {
            btnChat.disabled = true;
            btnChat.style.opacity = '0.35';
            btnChat.style.cursor = 'not-allowed';
        }

        window._modoChatDoc = true;

        // Bloquear checkboxes
        document.querySelectorAll('.doc-checkbox').forEach(cb => {
            cb.closest('label').style.pointerEvents = 'none';
            cb.closest('label').style.opacity = '0.4';
        });
        const masterLabel = document.querySelector('[onclick="toggleSelezionaTutti(this)"]');
        if (masterLabel) { masterLabel.style.pointerEvents = 'none'; masterLabel.style.opacity = '0.4'; }
        document.getElementById('floating-doc-toolbar').style.display = 'none';

        window._chatDocHistorial = [];

        // Cambiar el input
        const input = document.getElementById('chat-input');
        input.placeholder = `Modalità Chat Documenti · ${window._chatDocIds.length} file`;
        input.style.borderColor = '#6366f1';
        input.style.outline = '2px solid #6366f155';

        // Cambiar btn + a X para salir
        const btnPlus = document.getElementById('btn-upload-plus');
        if (btnPlus) {
            btnPlus.innerHTML = '<span class="material-symbols-rounded" style="font-size:20px;">close</span>';
            btnPlus.style.color = '#6366f1';
            btnPlus.style.opacity = '1';
            btnPlus.style.cursor = 'pointer';
            btnPlus.disabled = false;
            btnPlus.onclick = disattivareChatDocumenti;
        }

        agregarRespuestaBot(`
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
        <span class="material-symbols-rounded" style="font-size:18px;color:#6366f1;">check_circle</span>
        <strong style="font-size:13px;">Modalità attiva!</strong>
    </div>
    <p style="margin:0;font-size:12px;color:var(--icon-color);">
        Ora puoi farmi domande sul contenuto dei documenti selezionati.<br>
        Es: <em>"Riassumi il documento"</em>, <em>"Quali sono le date importanti?"</em>,
        <em>"C'è menzione di importi?"</em>
    </p>`);
    }

    function rimuovereDocDaLista(docId) {
        // Desmarcar el checkbox
        const cb = document.querySelector(`.doc-checkbox[data-id="${docId}"]`);
        if (cb) {
            cb.checked = false;
            const lbl = cb.closest('label');
            const box = lbl?.querySelector('.doc-check-box');
            const icon = box?.querySelector('span');
            if (box) {
                box.style.background = 'transparent';
                box.style.borderColor = 'var(--incoming-chat-border)';
            }
            if (icon) icon.style.opacity = '0';
        }
        // Disparar el update
        onCheckboxChange();
    }

    function disattivareChatDocumenti() {
        window._modoChatDoc = false;
        window._chatDocIds = null;
        window._chatDocNombri = null;
        window._chatDocHistorial = [];
        document.getElementById('banner-modo-doc')?.remove();

        // Deseleccionar todos los checkboxes
        document.querySelectorAll('.doc-checkbox').forEach(cb => {
            cb.checked = false;
            const box = cb.closest('label')?.querySelector('.doc-check-box');
            const icon = box?.querySelector('span');
            if (box) { box.style.background = 'transparent'; box.style.borderColor = 'var(--incoming-chat-border)'; }
            if (icon) icon.style.opacity = '0';
        });

        // Limpiar contador "selezionati"
        const contatore = document.getElementById('contatore-selezionati');
        if (contatore) contatore.style.display = 'none';

        // Restaurar "seleziona tutti"
        const masterBox = document.getElementById('box-seleziona-tutti');
        const masterIcon = masterBox?.querySelector('span');
        if (masterBox) { masterBox.style.background = 'transparent'; masterBox.style.borderColor = 'var(--incoming-chat-border)'; }
        if (masterIcon) masterIcon.style.opacity = '0';

        // Restaurar label "seleziona tutti"
        const masterLabel = document.querySelector('[onclick="toggleSelezionaTutti(this)"]');
        if (masterLabel) {
            masterLabel.style.pointerEvents = '';
            masterLabel.style.opacity = '';
        }

        // Ocultar toolbar flotante
        document.getElementById('floating-doc-toolbar').style.display = 'none';
        // Restaurar labels
        document.querySelectorAll('.doc-checkbox').forEach(cb => {
            cb.closest('label').style.pointerEvents = '';
            cb.closest('label').style.opacity = '';
        });

        // Restaurar input
        const input = document.getElementById('chat-input');
        input.placeholder = 'Scrivi il tuo messaggio qui...';
        input.style.borderColor = '';
        input.style.outline = '';

        // Restaurar btn +
        const btnPlus = document.getElementById('btn-upload-plus');
        if (btnPlus) {
            btnPlus.innerHTML = '<span class="material-symbols-rounded" style="font-size:20px;">add_2</span>';
            btnPlus.style.color = '';
            btnPlus.onclick = abrirSelectorFiles;
            activarBtnUpload(dataroomProyectoId !== null);
        }

        // Reactivar btn chat toolbar
        const btnChat = document.getElementById('toolbar-btn-chat');
        if (btnChat) {
            btnChat.disabled = false;
            btnChat.style.opacity = '1';
            btnChat.style.cursor = 'pointer';
        }

        agregarRespuestaBot('✅ Modalità Chat Documenti disattivata.');
    }

    async function rimuovereTuttiIgnorati() {
        const ids = window._chatSinTextoIds || [];

        // Limpiar estado
        window._chatSinTexto = [];
        window._chatSinTextoIds = [];

        // Desmarcar checkboxes sin disparar onCheckboxChange todavía
        ids.forEach(id => {
            const cb = document.querySelector(`.doc-checkbox[data-id="${id}"]`);
            if (!cb) return;
            cb.checked = false;
            const box = cb.closest('label')?.querySelector('.doc-check-box');
            const icon = box?.querySelector('span');
            if (box) { box.style.background = 'transparent'; box.style.borderColor = 'var(--incoming-chat-border)'; }
            if (icon) icon.style.opacity = '0';
        });

        // Ahora validar y esperar resultado antes de re-renderizar
        await validarBtnChat();

        // Solo después re-renderizar la card
        onCheckboxChange();
    }
</script>

<style>
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .doc-checkbox:checked+.doc-check-box {
        background: #6366f1;
        border-color: #6366f1;
    }

    .doc-checkbox:checked+.doc-check-box span {
        opacity: 1 !important;
    }

    .doc-check-box:hover {
        border-color: #6366f1;
    }
</style>
