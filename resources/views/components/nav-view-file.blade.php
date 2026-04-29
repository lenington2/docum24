<!-- Visor de documentos - derecha -->
<div id="fileViewer"
    style="position:fixed;top:0;right:-520px;width:500px;height:100vh;background:var(--incoming-chat-bg);border-left:1px solid var(--incoming-chat-border);z-index:1001;display:flex;flex-direction:column;transition:right 0.3s ease;box-shadow:-4px 0 20px rgba(0,0,0,0.3);">

    <!-- Header -->
    <div style="padding:16px 20px;border-bottom:1px solid var(--incoming-chat-border);background:var(--outgoing-chat-bg);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:10px;min-width:0;">
            <span class="material-symbols-rounded" style="color:#6366f1;font-size:20px;flex-shrink:0;">description</span>
            <p id="viewer-filename" style="font-size:13px;font-weight:600;color:var(--text-color);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Nessun file aperto</p>
        </div>
        <button onclick="closeFileViewer()"
            style="border:none;background:none;cursor:pointer;color:var(--icon-color);padding:4px;border-radius:6px;display:flex;align-items:center;flex-shrink:0;transition:all 0.2s;"
            onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='var(--text-color)'"
            onmouseout="this.style.background='none';this.style.color='var(--icon-color)'">
            <span class="material-symbols-rounded" style="font-size:20px;">close</span>
        </button>
    </div>

    <!-- Toolbar -->
    <div style="padding:10px 16px;border-bottom:1px solid var(--incoming-chat-border);display:flex;align-items:center;gap:8px;flex-shrink:0;background:var(--incoming-chat-bg);">
        <button onclick="viewerZoom(-0.25)" title="Zoom -"
            style="width:32px;height:32px;border:1px solid var(--incoming-chat-border);background:transparent;color:var(--icon-color);border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
            onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='var(--text-color)'"
            onmouseout="this.style.background='transparent';this.style.color='var(--icon-color)'">
            <span class="material-symbols-rounded" style="font-size:18px;">remove</span>
        </button>
        <span id="zoom-level" style="font-size:12px;color:var(--icon-color);min-width:38px;text-align:center;">100%</span>
        <button onclick="viewerZoom(+0.25)" title="Zoom +"
            style="width:32px;height:32px;border:1px solid var(--incoming-chat-border);background:transparent;color:var(--icon-color);border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
            onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='var(--text-color)'"
            onmouseout="this.style.background='transparent';this.style.color='var(--icon-color)'">
            <span class="material-symbols-rounded" style="font-size:18px;">add</span>
        </button>

        <div style="width:1px;height:24px;background:var(--incoming-chat-border);margin:0 4px;"></div>

        <button onclick="viewerRotate(-90)" title="Ruota sx"
            style="width:32px;height:32px;border:1px solid var(--incoming-chat-border);background:transparent;color:var(--icon-color);border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
            onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='var(--text-color)'"
            onmouseout="this.style.background='transparent';this.style.color='var(--icon-color)'">
            <span class="material-symbols-rounded" style="font-size:18px;">rotate_left</span>
        </button>
        <button onclick="viewerRotate(+90)" title="Ruota dx"
            style="width:32px;height:32px;border:1px solid var(--incoming-chat-border);background:transparent;color:var(--icon-color);border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
            onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='var(--text-color)'"
            onmouseout="this.style.background='transparent';this.style.color='var(--icon-color)'">
            <span class="material-symbols-rounded" style="font-size:18px;">rotate_right</span>
        </button>
        <button onclick="viewerReset()" title="Reset"
            style="width:32px;height:32px;border:1px solid var(--incoming-chat-border);background:transparent;color:var(--icon-color);border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
            onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='var(--text-color)'"
            onmouseout="this.style.background='transparent';this.style.color='var(--icon-color)'">
            <span class="material-symbols-rounded" style="font-size:18px;">refresh</span>
        </button>

        <div style="width:1px;height:24px;background:var(--incoming-chat-border);margin:0 4px;"></div>

        <button onclick="viewerFullscreen()" title="Schermo intero"
            style="width:32px;height:32px;border:1px solid var(--incoming-chat-border);background:transparent;color:var(--icon-color);border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
            onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='var(--text-color)'"
            onmouseout="this.style.background='transparent';this.style.color='var(--icon-color)'">
            <span class="material-symbols-rounded" style="font-size:18px;">open_in_full</span>
        </button>
        <button onclick="viewerDownload()" title="Scarica"
            style="width:32px;height:32px;border:1px solid var(--incoming-chat-border);background:transparent;color:var(--icon-color);border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
            onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='var(--text-color)'"
            onmouseout="this.style.background='transparent';this.style.color='var(--icon-color)'">
            <span class="material-symbols-rounded" style="font-size:18px;">download</span>
        </button>
        <button onclick="viewerPrint()" title="Stampa"
            style="width:32px;height:32px;border:1px solid var(--incoming-chat-border);background:transparent;color:var(--icon-color);border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;"
            onmouseover="this.style.background='var(--icon-hover-bg)';this.style.color='var(--text-color)'"
            onmouseout="this.style.background='transparent';this.style.color='var(--icon-color)'">
            <span class="material-symbols-rounded" style="font-size:18px;">print</span>
        </button>
    </div>

    <!-- Body -->
    <div id="viewer-body" style="flex:1;overflow:hidden;position:relative;background:var(--outgoing-chat-bg);display:flex;align-items:center;justify-content:center;">
        <div id="viewer-empty" style="text-align:center;color:var(--icon-color);">
            <span class="material-symbols-rounded" style="font-size:60px;opacity:0.2;display:block;margin-bottom:12px;">draft</span>
            <p style="font-size:13px;">Nessun documento aperto</p>
        </div>
        <!-- Imagen con drag -->
        <div id="viewer-img-container" style="display:none;width:100%;height:100%;overflow:hidden;position:relative;cursor:grab;">
            <img id="viewer-img" style="position:absolute;top:50%;left:50%;transform-origin:center center;user-select:none;-webkit-user-drag:none;">
        </div>
        <!-- PDF embed -->
        <embed id="viewer-pdf" style="display:none;width:100%;height:100%;border:none;">
        <!-- Office iframe -->
        <iframe id="viewer-office" style="display:none;width:100%;height:100%;border:none;"></iframe>
        <!-- TXT -->
        <pre id="viewer-txt" style="display:none;width:100%;height:100%;overflow:auto;padding:20px;font-size:13px;line-height:1.6;color:var(--text-color);font-family:'Poppins',monospace;white-space:pre-wrap;word-break:break-word;box-sizing:border-box;margin:0;"></pre>
    </div>

    <!-- Footer -->
    <div style="padding:10px 16px;border-top:1px solid var(--incoming-chat-border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;background:var(--outgoing-chat-bg);">
        <span id="viewer-fileinfo" style="font-size:11px;color:var(--icon-color);">—</span>
        <span id="viewer-filetype" style="font-size:11px;color:var(--icon-color);background:var(--incoming-chat-bg);padding:2px 8px;border-radius:4px;border:1px solid var(--incoming-chat-border);">—</span>
    </div>
</div>

<script>
    let vZoom        = 1;
    let vRotation    = 0;
    let vTranslateX  = 0;
    let vTranslateY  = 0;
    let vDragging    = false;
    let vDragStartX, vDragStartY;
    let vCurrentDocId   = null;
    let vCurrentUrl     = null;
    let vCurrentName    = null;

    window.openFileViewer = async function(docId, filename, mimeType) {
        vCurrentDocId = docId;
        vCurrentName  = filename;
        vZoom = 1; vRotation = 0; vTranslateX = 0; vTranslateY = 0;

        document.getElementById('viewer-filename').textContent  = filename || 'Documento';
        document.getElementById('viewer-fileinfo').textContent  = '—';
        document.getElementById('viewer-filetype').textContent  = (getExtFromMime(mimeType) || '—').toUpperCase();
        document.getElementById('zoom-level').textContent       = '100%';

        // Ocultar todo
        ['viewer-empty','viewer-img-container','viewer-pdf','viewer-office','viewer-txt']
            .forEach(id => {
                const el = document.getElementById(id);
                el.style.display = 'none';
                if (id === 'viewer-pdf' || id === 'viewer-office') el.src = '';
            });

        // Mostrar loading
        const empty = document.getElementById('viewer-empty');
        empty.innerHTML = `
            <span class="material-symbols-rounded" style="font-size:36px;opacity:0.5;display:block;margin-bottom:8px;animation:spin 1s linear infinite;">progress_activity</span>
            <p style="font-size:13px;">Caricamento...</p>`;
        empty.style.display = 'block';

        // Obtener token temporal
        const tokenRes  = await fetch(`/documentos/${docId}/preview-token`);
        const tokenData = await tokenRes.json();
        const url       = `/preview-public?token=${encodeURIComponent(tokenData.token)}`;
        vCurrentUrl     = `/documentos/${docId}/download`;

        empty.style.display = 'none';

        const ext = getExtFromMime(mimeType);

        if (ext === 'pdf') {
            const pdf = document.getElementById('viewer-pdf');
            pdf.src   = url;
            pdf.style.display = 'block';
            setupWheelZoomViewer('viewer-body');

        } else if (['jpg','jpeg','png','gif','webp','bmp'].includes(ext)) {
            const container = document.getElementById('viewer-img-container');
            const img       = document.getElementById('viewer-img');
            img.src         = url;
            img.onload      = () => {
                updateViewerTransform();
            };
            container.style.display = 'block';
            setupWheelZoomViewer('viewer-body');
            setupImageDragViewer(container, img);

        } else if (['doc','docx','xls','xlsx','ppt','pptx'].includes(ext)) {
            const iframe    = document.getElementById('viewer-office');
            const publicUrl = window.location.origin + url;
            const msUrl     = `https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(publicUrl)}`;
            const gUrl      = `https://docs.google.com/viewer?url=${encodeURIComponent(publicUrl)}&embedded=true`;

            iframe.src          = msUrl;
            iframe.style.display = 'block';

            let fallback = false;
            iframe.onerror = () => {
                if (!fallback) { fallback = true; iframe.src = gUrl; }
            };
            // Timeout fallback 8s
            setTimeout(() => {
                if (!fallback) {
                    try {
                        const h = iframe.contentWindow?.document?.body?.scrollHeight;
                        if (h !== undefined && h < 10) { fallback = true; iframe.src = gUrl; }
                    } catch(e) {}
                }
            }, 8000);

        } else if (ext === 'txt') {
            const txt = document.getElementById('viewer-txt');
            try {
                const r       = await fetch(url);
                txt.textContent = await r.text();
            } catch(e) {
                txt.textContent = 'Impossibile leggere il file.';
            }
            txt.style.display = 'block';

        } else {
            empty.innerHTML = `
                <span class="material-symbols-rounded" style="font-size:60px;opacity:0.2;display:block;margin-bottom:12px;">description</span>
                <p style="font-size:13px;">Anteprima non disponibile</p>
                <a href="${vCurrentUrl}" download="${filename}"
                    style="display:inline-flex;align-items:center;gap:6px;margin-top:12px;background:#6366f1;color:#fff;padding:8px 16px;border-radius:8px;text-decoration:none;font-size:12px;font-weight:600;">
                    <span class="material-symbols-rounded" style="font-size:16px;">download</span>Scarica file
                </a>`;
            empty.style.display = 'block';
        }

        document.getElementById('fileViewer').style.right = '0px';

        document.getElementById('chatContainer').style.marginRight = '500px';
document.querySelector('.typing-container').style.paddingRight = '510px';
document.getElementById('upload-panel').style.paddingRight = '510px';
document.querySelector('header').style.paddingRight = '510px';
    }

    function closeFileViewer() {
        document.getElementById('fileViewer').style.right = '-520px';

        document.getElementById('chatContainer').style.marginRight = '0px';
document.querySelector('.typing-container').style.paddingRight = '10px';
document.getElementById('upload-panel').style.paddingRight = '10px';
document.querySelector('header').style.paddingRight = '2rem';
        // Reset src
        document.getElementById('viewer-pdf').src    = '';
        document.getElementById('viewer-office').src = '';
    }

    // ---- Transform ----
    function updateViewerTransform() {
        const img = document.getElementById('viewer-img');
        if (!img) return;
        img.style.transform = `translate(-50%, -50%) translate(${vTranslateX}px, ${vTranslateY}px) scale(${vZoom}) rotate(${vRotation}deg)`;
        document.getElementById('zoom-level').textContent = Math.round(vZoom * 100) + '%';
    }

    function viewerZoom(delta) {
        vZoom = Math.min(Math.max(vZoom + delta, 0.1), 5);
        updateViewerTransform();
        document.getElementById('zoom-level').textContent = Math.round(vZoom * 100) + '%';
    }

    function viewerRotate(deg) {
        vRotation += deg;
        updateViewerTransform();
    }

    function viewerReset() {
        vZoom = 1; vRotation = 0; vTranslateX = 0; vTranslateY = 0;
        updateViewerTransform();
        document.getElementById('zoom-level').textContent = '100%';
    }

    function viewerFullscreen() {
        if (vCurrentUrl) window.open(vCurrentUrl, '_blank');
    }

    function viewerDownload() {
        if (!vCurrentUrl) return;
        const a = document.createElement('a');
        a.href = vCurrentUrl;
        a.download = vCurrentName || 'file';
        a.click();
    }

    function viewerPrint() {
        const pdf    = document.getElementById('viewer-pdf');
        const office = document.getElementById('viewer-office');
        const img    = document.getElementById('viewer-img');

        if (pdf.style.display !== 'none' && pdf.src) {
            const w = window.open(pdf.src);
            w?.print();
        } else if (img.style.display !== 'none') {
            const w = window.open('');
            w.document.write(`<img src="${img.src}" style="max-width:100%">`);
            w.document.close();
            w.print();
        } else {
            viewerDownload();
        }
    }

    // ---- Wheel zoom ----
    function setupWheelZoomViewer(containerId) {
        const el = document.getElementById(containerId);
        if (!el) return;
        el.addEventListener('wheel', e => {
            e.preventDefault();
            viewerZoom(e.deltaY > 0 ? -0.1 : 0.1);
        }, { passive: false });
    }

    // ---- Drag imagen ----
    function setupImageDragViewer(container, img) {
        container.addEventListener('mousedown', e => {
            vDragging  = true;
            vDragStartX = e.clientX - vTranslateX;
            vDragStartY = e.clientY - vTranslateY;
            container.style.cursor = 'grabbing';
            e.preventDefault();
        });
    }

    document.addEventListener('mousemove', e => {
        if (!vDragging) return;
        vTranslateX = e.clientX - vDragStartX;
        vTranslateY = e.clientY - vDragStartY;
        updateViewerTransform();
    });

    document.addEventListener('mouseup', () => {
        if (!vDragging) return;
        vDragging = false;
        const c = document.getElementById('viewer-img-container');
        if (c) c.style.cursor = 'grab';
    });

    function getExtFromMime(mimeType) {
        if (!mimeType) return 'file';
        if (mimeType.includes('pdf'))        return 'pdf';
        if (mimeType.includes('jpeg') || mimeType.includes('jpg')) return 'jpg';
        if (mimeType.includes('png'))        return 'png';
        if (mimeType.includes('gif'))        return 'gif';
        if (mimeType.includes('webp'))       return 'webp';
        if (mimeType.includes('msword') || mimeType.includes('wordprocessingml')) return 'docx';
        if (mimeType.includes('ms-excel') || mimeType.includes('spreadsheetml'))  return 'xlsx';
        if (mimeType.includes('powerpoint') || mimeType.includes('presentationml')) return 'pptx';
        if (mimeType.includes('text/plain')) return 'txt';
        return 'file';
    }
</script>
