<!-- Botón flotante para abrir nav -->
<button id="nav-toggle" onclick="toggleNav()"
    style="position:fixed;left:0;top:50%;transform:translateY(-50%);z-index:400;background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-left:none;color:var(--icon-color);cursor:pointer;padding:12px 6px;border-radius:0 8px 8px 0;transition:all 0.3s;box-shadow:2px 0 10px rgba(0,0,0,0.2);"
    onmouseover="this.style.color='var(--text-color)';this.style.background='var(--icon-hover-bg)'"
    onmouseout="this.style.color='var(--icon-color)';this.style.background='var(--incoming-chat-bg)'">
    <span class="material-symbols-rounded" style="font-size:20px;display:block;">chevron_right</span>
</button>

<!-- Overlay -->
<div id="nav-overlay" onclick="toggleNav()"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:450;backdrop-filter:blur(2px);"></div>

<!-- Nav lateral -->
<div id="nav-lateral"
    style="position:fixed;top:0;left:-420px;width:400px;height:100vh;background:var(--incoming-chat-bg);border-right:1px solid var(--incoming-chat-border);z-index:500;transition:left 0.3s ease;display:flex;flex-direction:column;box-shadow:4px 0 20px rgba(0,0,0,0.3);">

    <!-- Header nav -->
    <div
        style="padding:16px 20px;border-bottom:1px solid var(--incoming-chat-border);display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:10px;">
            <span class="material-symbols-rounded" style="color:#f59e0b;font-size:22px;">folder_managed</span>
            <h2 style="font-size:15px;font-weight:700;color:var(--text-color);margin:0;">I miei Progetti</h2>
        </div>
        <button onclick="toggleNav()"
            style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;align-items:center;transition:all 0.2s;"
            onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='var(--text-color)'"
            onmouseout="this.style.background='none';this.style.color='var(--icon-color)'">
            <span class="material-symbols-rounded" style="font-size:20px;">close</span>
        </button>
    </div>

    <!-- Buscador -->
    <div style="padding:12px 16px;border-bottom:1px solid var(--incoming-chat-border);">
        <div
            style="display:flex;align-items:center;gap:8px;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:8px 12px;">
            <span class="material-symbols-rounded" style="font-size:16px;color:var(--icon-color);">search</span>
            <input type="text" id="search-proyectos" placeholder="Cerca progetti..."
                oninput="filtrarProyectos(this.value)"
                style="background:transparent;border:none;outline:none;color:var(--text-color);font-size:13px;width:100%;font-family:'Poppins',sans-serif;">
        </div>
    </div>

    <!-- Lista proyectos -->
    <div style="flex:1;overflow-y:auto;padding:12px 16px;" id="lista-proyectos-nav">

        @forelse(auth()->user()->proyectos ?? [] as $proyecto)
            <div class="nav-proyecto-item" data-id="{{ $proyecto->id }}" data-nombre="{{ strtolower($proyecto->nombre) }}">
                <button onclick="seleccionarProyectoNav({{ $proyecto->id }}, '{{ $proyecto->nombre }}')"
                    style="width:100%;text-align:left;padding:10px 12px;border-radius:10px;border:none;background:transparent;cursor:pointer;display:flex;align-items:center;gap:10px;color:var(--text-color);font-family:'Poppins',sans-serif;font-size:13px;margin-bottom:4px;transition:background 0.2s;"
                    onmouseover="this.style.background='var(--icon-hover-bg)'"
                    onmouseout="this.style.background='transparent'">
                    <span class="material-symbols-rounded"
                        style="font-size:20px;color:#f59e0b;flex-shrink:0;">folder</span>
                    <div style="flex:1;min-width:0;">
                        <p style="margin:0;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $proyecto->nombre }}</p>
                        <small style="color:var(--icon-color);font-size:11px;">{{ $proyecto->categorias->count() ?? 0 }}
                            categorie</small>
                    </div>
                    <span class="material-symbols-rounded"
                        style="font-size:16px;color:var(--icon-color);">chevron_right</span>
                </button>
            </div>
        @empty
            <div id="nav-empty" style="text-align:center;padding:30px 0;color:var(--icon-color);">
                <span class="material-symbols-rounded"
                    style="font-size:40px;opacity:0.3;display:block;margin-bottom:8px;">folder_off</span>
                <p style="font-size:13px;">Nessun progetto ancora</p>
            </div>
        @endforelse

    </div>

    <!-- Footer usuario -->
    <div
        style="padding:14px 16px;border-top:1px solid var(--incoming-chat-border);display:flex;align-items:center;gap:10px;">
        <div
            style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#60a5fa,#6366f1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;flex-shrink:0;">
            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
        </div>
        <div style="flex:1;min-width:0;">
            <p
                style="font-size:12px;font-weight:600;color:var(--text-color);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                {{ auth()->user()->name }}</p>
            <p
                style="font-size:11px;color:var(--icon-color);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                {{ auth()->user()->email }}</p>
        </div>
    </div>
</div>

<script>
    let navAbierto = false;

    function toggleNav() {
        const nav = document.getElementById('nav-lateral');
        const overlay = document.getElementById('nav-overlay');
        const toggle = document.getElementById('nav-toggle');
        const icon = toggle.querySelector('.material-symbols-rounded');

        navAbierto = !navAbierto;
        nav.style.left = navAbierto ? '0px' : '-420px';
        overlay.style.display = navAbierto ? 'block' : 'none';
        icon.textContent = navAbierto ? 'chevron_left' : 'chevron_right';
    }

    function seleccionarProyectoNav(id, nombre) {
        toggleNav();
        if (typeof window.seleccionarProyectoChat === 'function') {
            window.seleccionarProyectoChat(id, nombre);
        }
    }

    function abrirNuevoProyecto() {
        if (typeof window.abrirNuevoProyectoChat === 'function') {
            window.abrirNuevoProyectoChat();
        }
    }

    function filtrarProyectos(query) {
        const items = document.querySelectorAll('.nav-proyecto-item');
        const empty = document.getElementById('nav-empty');
        let visibles = 0;

        items.forEach(item => {
            const nombre = item.dataset.nombre || '';
            const visible = nombre.includes(query.toLowerCase());
            item.style.display = visible ? 'block' : 'none';
            if (visible) visibles++;
        });

        if (empty) empty.style.display = visibles === 0 ? 'block' : 'none';
    }


    window.agregarProyectoNavLateral = function(proyecto) {
        const lista = document.getElementById('lista-proyectos-nav');
        const empty = document.getElementById('nav-empty');
        if (empty) empty.style.display = 'none';

        const div = document.createElement('div');
        div.className = 'nav-proyecto-item';
        div.dataset.nombre = proyecto.nombre.toLowerCase();
        div.innerHTML = `
            <button onclick="seleccionarProyectoNav(${proyecto.id}, '${proyecto.nombre}')"
                style="width:100%;text-align:left;padding:10px 12px;border-radius:10px;border:none;background:transparent;cursor:pointer;display:flex;align-items:center;gap:10px;color:var(--text-color);font-family:'Poppins',sans-serif;font-size:13px;margin-bottom:4px;transition:background 0.2s;"
                onmouseover="this.style.background='var(--icon-hover-bg)'"
                onmouseout="this.style.background='transparent'">
                <span class="material-symbols-rounded" style="font-size:20px;color:#f59e0b;flex-shrink:0;">folder</span>
                <div style="flex:1;min-width:0;">
                    <p style="margin:0;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${proyecto.nombre}</p>
                    <small style="color:var(--icon-color);font-size:11px;">0 categorie</small>
                </div>
                <span class="material-symbols-rounded" style="font-size:16px;color:var(--icon-color);">chevron_right</span>
            </button>`;
        lista.appendChild(div);
    }

    function abrirNuevaCategoria() {
    if (typeof window.abrirNuevaCategoriaChat === 'function') {
        window.abrirNuevaCategoriaChat();
    }
}

function abrirNuovaTipologia() {
    if (typeof window.abrirNuovaTipologiaChat === 'function') {
        window.abrirNuovaTipologiaChat();
    }
}
</script>
