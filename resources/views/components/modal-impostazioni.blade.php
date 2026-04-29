<!-- Modal Impostazioni -->
<div id="modal-impostazioni"
    style="display:none;position:fixed;inset:0;z-index:600;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:20px;padding:28px;width:100%;max-width:480px;margin:0 16px;box-shadow:0 20px 60px rgba(0,0,0,0.4);animation:fadeIn 0.2s ease;">

        <!-- Header -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <span class="material-symbols-rounded" style="color:#6366f1;font-size:22px;">settings</span>
                <h3 style="font-size:15px;font-weight:700;color:var(--text-color);margin:0;">Impostazioni</h3>
            </div>
            <button onclick="chiudiImpostazioni()"
                style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;transition:all 0.2s;"
                onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='#ef4444'"
                onmouseout="this.style.background='none';this.style.color='var(--icon-color)'">
                <span class="material-symbols-rounded" style="font-size:20px;">close</span>
            </button>
        </div>

        
        <!-- Dati Azienda -->
        <p style="font-size:11px;font-weight:600;color:var(--icon-color);text-transform:uppercase;letter-spacing:0.5px;margin:20px 0 14px;">Azienda</p>

        <button onclick="chiudiImpostazioni(); abrirModalEmpresa();"
            style="width:100%;display:flex;align-items:center;gap:14px;padding:14px 16px;
                   background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);
                   border-radius:12px;cursor:pointer;font-family:'Poppins',sans-serif;text-align:left;
                   transition:all 0.2s;"
            onmouseover="this.style.borderColor='#6366f1';this.style.background='#6366f108'"
            onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.background='var(--outgoing-chat-bg)'">
            <div style="width:38px;height:38px;border-radius:10px;background:#6366f120;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span class="material-symbols-rounded" style="font-size:20px;color:#6366f1;">business</span>
            </div>
            <div style="flex:1;">
                <p style="margin:0;font-size:13px;font-weight:600;color:var(--text-color);">Dati Azienda</p>
                <p style="margin:2px 0 0;font-size:11px;color:var(--icon-color);">Nome, logo, indirizzo, telefono, P.IVA</p>
            </div>
            <span class="material-symbols-rounded" style="font-size:18px;color:var(--icon-color);">chevron_right</span>
        </button>

    </div>
</div>

{{-- Modal Dati Azienda --}}
<div id="modal-empresa" style="display:none;position:fixed;inset:0;z-index:700;background:rgba(0,0,0,0.5);backdrop-filter:blur(4px);align-items:center;justify-content:center;">
    <div style="background:var(--incoming-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:20px;width:100%;max-width:480px;margin:0 16px;box-shadow:0 20px 60px rgba(0,0,0,0.4);display:flex;flex-direction:column;max-height:90vh;overflow:hidden;animation:fadeIn 0.2s ease;">

        <!-- Header -->
        <div style="padding:22px 24px 16px;border-bottom:1px solid var(--incoming-chat-border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:10px;">
                <span class="material-symbols-rounded" style="color:#6366f1;font-size:22px;">business</span>
                <h3 style="font-size:15px;font-weight:700;color:var(--text-color);margin:0;">Dati Azienda</h3>
            </div>
            <button onclick="chiudiModalEmpresa()"
                style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;transition:all 0.2s;"
                onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='#ef4444'"
                onmouseout="this.style.background='none';this.style.color='var(--icon-color)'">
                <span class="material-symbols-rounded" style="font-size:20px;">close</span>
            </button>
        </div>

        <!-- Body -->
        <div style="overflow-y:auto;flex:1;padding:20px 24px;display:flex;flex-direction:column;gap:14px;">

            <!-- Logo -->
            <div>
                <p style="font-size:11px;font-weight:600;color:var(--icon-color);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px;">Logo</p>
                <div style="display:flex;align-items:center;gap:14px;">
                    <div id="empresa-logo-preview" style="width:64px;height:64px;border-radius:12px;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">
                        <span class="material-symbols-rounded" style="font-size:28px;color:var(--icon-color);">business</span>
                    </div>
                    <div>
                        <button onclick="document.getElementById('empresa-logo-input').click()"
                            style="display:flex;align-items:center;gap:6px;background:transparent;border:1px solid var(--incoming-chat-border);border-radius:8px;padding:7px 12px;font-size:12px;font-weight:600;color:var(--text-color);cursor:pointer;font-family:'Poppins',sans-serif;transition:all 0.2s;"
                            onmouseover="this.style.borderColor='#6366f1';this.style.color='#6366f1'"
                            onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--text-color)'">
                            <span class="material-symbols-rounded" style="font-size:16px;">upload</span>
                            Carica logo
                        </button>
                        <p style="margin:5px 0 0;font-size:11px;color:var(--icon-color);">PNG, JPG — max 2MB</p>
                    </div>
                    <input type="file" id="empresa-logo-input" accept="image/*" style="display:none;" onchange="previewLogo(this)">
                </div>
            </div>

            <hr style="border:none;border-top:1px solid var(--incoming-chat-border);margin:0;">

            <!-- Campos -->
            <div style="display:flex;flex-direction:column;gap:12px;">

                <div>
                    <label style="font-size:11px;font-weight:600;color:var(--icon-color);text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px;">Nome Azienda</label>
                    <input id="emp-nombre" type="text" placeholder="Es. Studio Rossi & Associati"
                        style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                        onfocus="this.style.borderColor='#6366f1'"
                        onblur="this.style.borderColor='var(--incoming-chat-border)'">
                </div>

                <div>
                    <label style="font-size:11px;font-weight:600;color:var(--icon-color);text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px;">Indirizzo</label>
                    <input id="emp-direccion" type="text" placeholder="Es. Via Roma 10, Milano"
                        style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                        onfocus="this.style.borderColor='#6366f1'"
                        onblur="this.style.borderColor='var(--incoming-chat-border)'">
                </div>

                <div>
                    <label style="font-size:11px;font-weight:600;color:var(--icon-color);text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px;">Telefono</label>
                    <input id="emp-telefono" type="tel" placeholder="+39 02 1234567"
                        style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                        onfocus="this.style.borderColor='#6366f1'"
                        onblur="this.style.borderColor='var(--incoming-chat-border)'">
                </div>

                <div>
                    <label style="font-size:11px;font-weight:600;color:var(--icon-color);text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px;">P.IVA / Codice Fiscale</label>
                    <input id="emp-piva" type="text" placeholder="Es. IT01234567890"
                        style="width:100%;background:var(--outgoing-chat-bg);border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 12px;font-size:13px;color:var(--text-color);font-family:'Poppins',sans-serif;outline:none;box-sizing:border-box;transition:border-color 0.2s;"
                        onfocus="this.style.borderColor='#6366f1'"
                        onblur="this.style.borderColor='var(--incoming-chat-border)'">
                </div>

            </div>
        </div>

        <!-- Footer -->
        <div style="padding:16px 24px;border-top:1px solid var(--incoming-chat-border);display:flex;gap:8px;flex-shrink:0;">
            <button onclick="chiudiModalEmpresa()"
                style="background:transparent;border:1px solid var(--incoming-chat-border);border-radius:8px;padding:9px 16px;font-size:13px;color:var(--icon-color);cursor:pointer;font-family:'Poppins',sans-serif;transition:all 0.2s;"
                onmouseover="this.style.borderColor='#ef4444';this.style.color='#ef4444'"
                onmouseout="this.style.borderColor='var(--incoming-chat-border)';this.style.color='var(--icon-color)'">
                Annulla
            </button>
            <button onclick="salvarEmpresa()"
                style="flex:1;background:#6366f1;color:#fff;border:none;border-radius:8px;padding:9px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Poppins',sans-serif;display:flex;align-items:center;justify-content:center;gap:6px;transition:background 0.2s;"
                onmouseover="this.style.background='#4f46e5'"
                onmouseout="this.style.background='#6366f1'">
                <span class="material-symbols-rounded" style="font-size:16px;">save</span>
                Salva
            </button>
        </div>
    </div>
</div>

<script>
let _empresaLogoFile = null;

async function abrirModalEmpresa() {
    const modal = document.getElementById('modal-empresa');
    modal.style.display = 'flex';

    // Cargar datos existentes
    const res  = await fetch('/empresa');
    const data = await res.json();

    document.getElementById('emp-nombre').value   = data.nombre   || '';
    document.getElementById('emp-direccion').value = data.direccion || '';
    document.getElementById('emp-telefono').value  = data.telefono  || '';
    document.getElementById('emp-piva').value      = data.piva      || '';

    // Logo preview
    if (data.logo) {
        const preview = document.getElementById('empresa-logo-preview');
        preview.innerHTML = `<img src="/storage/${data.logo}" style="width:100%;height:100%;object-fit:cover;">`;
    }
}

function chiudiModalEmpresa() {
    const modal = document.getElementById('modal-empresa');
    modal.style.opacity = '0';
    modal.style.transition = 'opacity 0.2s ease';
    setTimeout(() => {
        modal.style.display = 'none';
        modal.style.opacity = '1';
        _empresaLogoFile = null;
    }, 200);
}

function previewLogo(input) {
    const file = input.files[0];
    if (!file) return;
    _empresaLogoFile = file;
    const url = URL.createObjectURL(file);
    document.getElementById('empresa-logo-preview').innerHTML =
        `<img src="${url}" style="width:100%;height:100%;object-fit:cover;">`;
}

async function salvarEmpresa() {
    const btn = event.currentTarget;
    btn.disabled = true;
    btn.innerHTML = `<span class="material-symbols-rounded" style="font-size:16px;animation:spin 1s linear infinite;">autorenew</span> Salvataggio...`;

    const formData = new FormData();
    formData.append('_method', 'POST');
    formData.append('nombre',    document.getElementById('emp-nombre').value);
    formData.append('direccion', document.getElementById('emp-direccion').value);
    formData.append('telefono',  document.getElementById('emp-telefono').value);
    formData.append('piva',      document.getElementById('emp-piva').value);
    if (_empresaLogoFile) formData.append('logo', _empresaLogoFile);

    const res  = await fetch('/empresa', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: formData
    });
    const data = await res.json();

    btn.disabled = false;
    btn.innerHTML = `<span class="material-symbols-rounded" style="font-size:16px;">save</span> Salva`;

    if (data.success) {
        chiudiModalEmpresa();
        // Toast
        const toast = document.createElement('div');
        toast.style.cssText = `position:fixed;bottom:24px;right:24px;z-index:9999;
            background:#10b981;color:#fff;padding:10px 18px;border-radius:10px;
            font-size:13px;font-weight:600;font-family:'Poppins',sans-serif;
            box-shadow:0 8px 24px rgba(0,0,0,0.2);animation:fadeIn 0.3s ease;`;
        toast.textContent = '✅ Dati azienda salvati';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
}

document.getElementById('modal-empresa').addEventListener('click', function(e) {
    if (e.target === this) chiudiModalEmpresa();
});
</script>

<script>
function abrirImpostazioni() {
    const modal = document.getElementById('modal-impostazioni');
    modal.style.display = 'flex';
}

function chiudiImpostazioni() {
    const modal = document.getElementById('modal-impostazioni');
    modal.style.opacity = '0';
    modal.style.transition = 'opacity 0.2s ease';
    setTimeout(() => {
        modal.style.display = 'none';
        modal.style.opacity = '1';
    }, 200);
}

// Cerrar al hacer click fuera del modal
document.getElementById('modal-impostazioni').addEventListener('click', function(e) {
    if (e.target === this) chiudiImpostazioni();
});
</script>
