<style>
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0,0,0,.5);
    backdrop-filter: blur(4px);
    overflow-y: auto;
    animation: modalFadeIn .2s ease;
}

.modal-box {
    max-width: 700px;
    margin: 3rem auto 5rem;
    background: #fff;
    border-radius: 14px;
    padding: 2.5rem;
    position: relative;
    font-family: 'Instrument Sans', sans-serif;
    animation: modalSlideUp .25s ease;
    max-height: 85vh;
    overflow-y: auto;
}

.modal-box.modal-sm {
    max-width: 560px;
}

.modal-close {
    position: absolute;
    top: 1.2rem;
    right: 1.2rem;
    background: none;
    border: none;
    font-size: 1.3rem;
    cursor: pointer;
    color: #706f6c;
    line-height: 1;
    transition: color .15s;
}

.modal-close:hover {
    color: #1b1b18;
}

.modal-header {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: 1.5rem;
}

.modal-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #F53003;
    flex-shrink: 0;
}

.modal-title {
    font-size: 1.4rem;
    font-weight: 600;
    letter-spacing: -.02em;
    margin: 0;
}

.modal-date {
    font-size: 12px;
    color: #a8a7a3;
    margin-bottom: 1.5rem;
}

.modal-body {
    font-size: 13px;
    line-height: 1.8;
    color: #3a3a36;
}

.modal-body h3 {
    font-size: 14px;
    font-weight: 600;
    margin: 1.2rem 0 .5rem;
    color: #1b1b18;
}

.modal-body p { margin-bottom: .5rem; }

.modal-body ul {
    padding-left: 1.2rem;
    margin-top: .4rem;
}

.modal-body a { color: #F53003; text-decoration: none; }
.modal-body a:hover { text-decoration: underline; }

.modal-footer {
    margin-top: 2rem;
    text-align: right;
}

.modal-btn-close {
    background: #1b1b18;
    color: #fff;
    border: none;
    padding: .5rem 1.5rem;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
    font-family: 'Instrument Sans', sans-serif;
    transition: background .15s;
}

.modal-btn-close:hover { background: #2e2e2a; }

@keyframes modalFadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

@keyframes modalSlideUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>

<script>
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.style.display = 'none';
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(m => m.style.display = 'none');
    }
});

function openModal(id) {
    document.getElementById(id).style.display = 'block';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
</script>