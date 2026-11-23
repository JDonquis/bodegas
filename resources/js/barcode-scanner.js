let activeScanner = null;
let activeModal = null;
let activeCleanup = null;

function createModal() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-[9999] flex items-center justify-center bg-brand-navy/80 p-4 backdrop-blur-sm';
    modal.innerHTML = `
        <div class="w-full max-w-md rounded-3xl bg-white p-4 shadow-2xl">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-secondary-container/40 text-secondary">
                        <span class="material-symbols-outlined text-[22px]">barcode_scanner</span>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-extrabold text-brand-navy">Escanear código</h3>
                        <p class="text-[10px] text-slate-400">Apunta la cámara al código de barras</p>
                    </div>
                </div>
                <button type="button" data-scanner-close aria-label="Cerrar escáner"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 active:scale-90 transition">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div id="barcode-scanner-reader" class="mt-3 min-h-[240px] overflow-hidden rounded-2xl bg-black"></div>

            <p data-scanner-status class="mt-2 text-center text-[11px] font-medium text-slate-500">
                Iniciando cámara...
            </p>
            <p class="mt-1 text-center text-[10px] text-slate-400">
                Si no abre la cámara, revisa los permisos y que la página use HTTPS.
            </p>
        </div>
    `;

    document.body.appendChild(modal);

    return modal;
}

function setStatus(modal, message, isError = false) {
    const status = modal.querySelector('[data-scanner-status]');

    if (!status) return;

    status.textContent = message;
    status.className = 'mt-2 text-center text-[11px] font-medium ' + (isError ? 'text-rose-600' : 'text-slate-500');
}

async function cleanupScanner() {
    const scanner = activeScanner;
    activeScanner = null;

    if (scanner) {
        try {
            await scanner.stop();
        } catch (error) {
            // La cámara pudo no haber iniciado.
        }

        try {
            scanner.clear();
        } catch (error) {
            // Ignorar.
        }
    }

    if (activeCleanup) {
        activeCleanup();
        activeCleanup = null;
    }

    if (activeModal) {
        activeModal.remove();
        activeModal = null;
    }

    document.body.style.overflow = '';
}

function applyResult(target, text, options) {
    const value = String(text || '').trim();

    if (!value) return;

    if (typeof options.onScan === 'function') {
        options.onScan(value);
    }

    if (target instanceof HTMLInputElement || target instanceof HTMLTextAreaElement) {
        target.value = value;
        target.dispatchEvent(new Event('input', { bubbles: true }));
        target.dispatchEvent(new Event('change', { bubbles: true }));
        target.focus();

        try {
            target.setSelectionRange(value.length, value.length);
        } catch (error) {
            // Algunos tipos de input no lo admiten.
        }
    }
}


async function ensureCameraPermission() {
    if (navigator.permissions && navigator.permissions.query) {
        try {
            const permission = await navigator.permissions.query({ name: 'camera' });

            if (permission.state === 'denied') {
                throw new Error('permission-denied');
            }

            if (permission.state === 'granted') {
                return;
            }
        } catch (error) {
            if (error.message === 'permission-denied') {
                throw error;
            }

            // El navegador no soporta consultar el permiso de cámara.
        }
    }

    let stream = null;

    try {
        stream = await navigator.mediaDevices.getUserMedia({
            audio: false,
            video: {
                facingMode: { ideal: 'environment' },
            },
        });
    } finally {
        if (stream) {
            stream.getTracks().forEach(function(track) {
                track.stop();
            });
        }
    }
}

function cameraPermissionMessage(error) {
    if (!error) {
        return 'No se pudo acceder a la cámara.';
    }

    if (error.message === 'permission-denied' || error.name === 'NotAllowedError' || error.name === 'SecurityError') {
        return 'No se otorgó permiso para usar la cámara. Habilita el permiso de cámara para este sitio en tu navegador e intenta nuevamente.';
    }

    if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
        return 'No se encontró una cámara disponible en este dispositivo.';
    }

    if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
        return 'La cámara está siendo usada por otra aplicación. Cierra esa aplicación e intenta nuevamente.';
    }

    return 'No se pudo acceder a la cámara. Verifica los permisos del navegador.';
}

export async function startBarcodeScanner(target, options = {}) {
    if (!target) return;

    const hostname = window.location.hostname;
    const isLocal = hostname === 'localhost' || hostname === '127.0.0.1' || hostname === '::1';

    if (!window.isSecureContext && !isLocal) {
        alert('La cámara requiere que la página use HTTPS.');
        return;
    }

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Tu navegador no permite acceder a la cámara.');
        return;
    }

    try {
        await ensureCameraPermission();
    } catch (error) {
        alert(cameraPermissionMessage(error));
        return;
    }

    await cleanupScanner();

    const modal = createModal();
    activeModal = modal;

    const readerId = 'barcode-scanner-reader';
    const reader = document.getElementById(readerId);

    if (!reader) {
        setStatus(modal, 'No se pudo preparar el escáner.', true);
        return;
    }

    document.body.style.overflow = 'hidden';

    const close = async () => {
        await cleanupScanner();
    };

    const closeButton = modal.querySelector('[data-scanner-close]');

    if (closeButton) {
        closeButton.addEventListener('click', close);
    }

    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            close();
        }
    });

    const onKeyDown = function(event) {
        if (event.key === 'Escape') {
            close();
        }
    };

    document.addEventListener('keydown', onKeyDown);
    activeCleanup = function() {
        document.removeEventListener('keydown', onKeyDown);
    };

    let Html5Qrcode;
    let Html5QrcodeSupportedFormats;

    try {
        const module = await import('html5-qrcode');
        Html5Qrcode = module.Html5Qrcode;
        Html5QrcodeSupportedFormats = module.Html5QrcodeSupportedFormats;
    } catch (error) {
        console.error('No se pudo cargar el escáner:', error);
        setStatus(modal, 'No se pudo cargar el escáner de códigos.', true);
        return;
    }

    const formats = [
        Html5QrcodeSupportedFormats.QR_CODE,
        Html5QrcodeSupportedFormats.DATA_MATRIX,
        Html5QrcodeSupportedFormats.CODE_128,
        Html5QrcodeSupportedFormats.CODE_39,
        Html5QrcodeSupportedFormats.CODE_93,
        Html5QrcodeSupportedFormats.CODABAR,
        Html5QrcodeSupportedFormats.EAN_13,
        Html5QrcodeSupportedFormats.EAN_8,
        Html5QrcodeSupportedFormats.ITF,
        Html5QrcodeSupportedFormats.UPC_A,
        Html5QrcodeSupportedFormats.UPC_E,
        Html5QrcodeSupportedFormats.PDF_417,
        Html5QrcodeSupportedFormats.AZTEC,
    ];

    const scanner = new Html5Qrcode(readerId, {
        verbose: false,
        formatsToSupport: formats,
        useBarCodeDetectorIfSupported: true,
    });

    activeScanner = scanner;

    let handled = false;

    const onSuccess = async function(decodedText) {
        if (handled) return;
        handled = true;

        applyResult(target, decodedText, options);

        if (navigator.vibrate) {
            navigator.vibrate(120);
        }

        await close();
    };

    const config = {
        fps: 10,
        aspectRatio: 1.0,
        disableFlip: false,
        qrbox: function(viewfinderWidth, viewfinderHeight) {
            const width = Math.min(Math.round(viewfinderWidth * 0.85), 300);
            const height = Math.min(Math.round(viewfinderHeight * 0.5), 180);

            return {
                width: Math.max(180, width),
                height: Math.max(100, height),
            };
        },
    };

    try {
        await scanner.start(
            { facingMode: { ideal: 'environment' } },
            config,
            onSuccess,
            function() {
                // Los errores de lectura se ignoran mientras no haya código.
            }
        );

        setStatus(modal, 'Apunta al código de barras.');
    } catch (error) {
        console.error('Error al iniciar la cámara:', error);
        setStatus(modal, 'No se pudo iniciar la cámara. Verifica los permisos del navegador.', true);
    }
}

window.startBarcodeScanner = startBarcodeScanner;
