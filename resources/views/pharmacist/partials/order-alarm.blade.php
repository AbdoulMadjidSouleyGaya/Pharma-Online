{{-- resources/views/pharmacist/partials/order-alarm.blade.php --}}
{{-- 🔔 Alarme commandes en attente (partagée sur tout l’espace pharmacien) --}}

<audio id="newOrderSound" preload="auto" loop>
    <source src="{{ asset('sounds/new-order.mp3') }}" type="audio/mpeg">
</audio>

{{-- Petit toast d’info en bas à droite --}}
<div id="new-order-toast"
     style="position:fixed;right:16px;bottom:16px;z-index:9999;
            display:none;align-items:center;gap:8px;
            background:#1f2937;color:white;
            padding:10px 14px;border-radius:9999px;
            box-shadow:0 10px 15px rgba(0,0,0,0.25);
            font-size:12px;">
    <span style="margin-top:2px;font-size:16px;">🛎</span>
    <div class="flex-1">
        <div style="font-weight:600;font-size:13px;">Nouvelle commande reçue</div>
        <div style="font-size:12px;" data-order-number>
            Une nouvelle commande vient d’arriver pour cette pharmacie.
        </div>
    </div>
</div>

@php
    // On essaie d’avoir un "pending" initial intelligent :
    // - dashboard : $pendingCount
    // - autres pages : $counts['pending']
    $___pendingCount = isset($pendingCount)
        ? (int) $pendingCount
        : (isset($counts['pending']) ? (int) $counts['pending'] : 0);

    // Pharmacie courante (pour le channel privé)
    $___pharmacyId = isset($pharmacy) && $pharmacy?->id ? (int) $pharmacy->id : null;
@endphp

<script>
    // ----- Gestion son / alarme SANS bouton -----
    let alarmActive   = false;
    let audioUnlocked = false;
    const audio       = document.getElementById('newOrderSound');

    function unlockAudioOnce() {
        if (!audio) return;

        console.log('🔓 Tentative de déblocage de l’audio…');

        audio.muted = true;
        audio.play().then(() => {
            audio.pause();
            audio.currentTime = 0;
            audio.muted = false;
            audioUnlocked = true;
            console.log('✅ Audio débloqué');

            if (typeof window.currentPending === 'number' && window.currentPending > 0) {
                window.startOrderAlarm();
            }
        }).catch(err => {
            console.warn('⚠️ Échec du déblocage audio :', err);
        });
    }

    window.addEventListener('click',   unlockAudioOnce, { once: true });
    window.addEventListener('keydown', unlockAudioOnce, { once: true });

    window.startOrderAlarm = function () {
        if (!audio) return;
        if (!audioUnlocked) {
            console.warn('🔇 startOrderAlarm appelé mais audio pas encore débloqué');
            return;
        }
        if (alarmActive) {
            console.log('🔁 Alarme déjà active');
            return;
        }

        alarmActive       = true;
        audio.loop        = true;
        audio.currentTime = 0;

        console.log('🔊 Démarrage de l’alarme…');

        audio.play().catch((err) => {
            console.warn('⚠️ Impossible de jouer le son :', err);
        });
    };

    window.stopOrderAlarm = function () {
        if (!audio) return;
        if (!alarmActive) return;

        alarmActive = false;
        audio.pause();
        audio.currentTime = 0;
    };

    // 🧪 Test manuel possible en console
    window.__testBeep = function () {
        if (!audio) return;
        audioUnlocked     = true;
        alarmActive       = true;
        audio.loop        = false;
        audio.currentTime = 0;
        audio.play().catch(console.error);
    };
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof window.Echo === 'undefined') {
            console.warn('⚠️ window.Echo est indisponible. Vérifie l’inclusion de resources/js/app.js et ta config Echo.');
        }

        const PHARM_ID = {{ $___pharmacyId !== null ? $___pharmacyId : 'null' }};

        if (PHARM_ID === null) {
            console.warn('⚠️ Aucune pharmacie associée à ce compte pharmacien. Écoute des commandes désactivée.');
        }

        const PENDING_AT_LOAD   = {{ $___pendingCount }};
        const pendingBadge      = document.querySelector('[data-pending-count]');
        // ⚠️ Cette route doit exister dans ton projet, on l’avait déjà mise en place côté controller
        const PENDING_COUNT_URL = "{{ route('pharmacist.orders.pendingCount') }}";

        // Valeur globale du nombre de commandes en attente
        window.currentPending = PENDING_AT_LOAD;
        console.log('📊 PENDING_AT_LOAD =', window.currentPending);

        function syncAlarmWithPending() {
            if (typeof window.currentPending === 'undefined') return;

            console.log('🔁 syncAlarmWithPending →', window.currentPending);

            if (window.currentPending > 0) {
                if (typeof window.startOrderAlarm === 'function') {
                    window.startOrderAlarm();
                }
            } else {
                if (typeof window.stopOrderAlarm === 'function') {
                    window.stopOrderAlarm();
                }
            }
        }

        function refreshPendingFromServer() {
            console.log('🌐 Rafraîchissement du nombre de commandes en attente…');

            fetch(PENDING_COUNT_URL, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            })
                .then(response => {
                    if (!response.ok) throw new Error('Erreur HTTP ' + response.status);
                    return response.json();
                })
                .then(data => {
                    const value = typeof data.pending === 'number' ? data.pending : 0;
                    window.currentPending = value;

                    console.log('✅ pendingCount depuis serveur =', value);

                    if (pendingBadge) {
                        pendingBadge.textContent = value;
                    }

                    syncAlarmWithPending();
                })
                .catch(err => {
                    console.warn('⚠️ Erreur lors de la récupération du pendingCount :', err);
                });
        }

        // 🔁 sync avec la valeur initiale
        syncAlarmWithPending();
        // Puis on rafraîchit immédiatement depuis le serveur
        refreshPendingFromServer();
        // Et toutes les 10 secondes
        setInterval(refreshPendingFromServer, 10000);

        // ----- Gestion du toast -----
        const toast     = document.getElementById('new-order-toast');
        const toastText = toast ? toast.querySelector('[data-order-number]') : null;

        function showNewOrderToast(orderNumber) {
            if (!toast) return;

            if (toastText && orderNumber) {
                toastText.textContent = `Commande #${orderNumber} reçue pour votre pharmacie.`;
            }

            toast.style.display = 'flex';

            setTimeout(() => {
                toast.style.display = 'none';
            }, 4000);
        }

        if (window.Echo && PHARM_ID !== null) {
            const channel = window.Echo.private(`pharmacies.${PHARM_ID}.orders`);

            const handleOrderEvent = (e) => {
                console.log('📨 Événement OrderCreated reçu :', e);

                // Quand une nouvelle commande arrive, on force un refresh du compteur
                refreshPendingFromServer();

                // et on affiche le toast
                syncAlarmWithPending();
                showNewOrderToast(e.number ?? null);
            };

            channel
                .listen('.order.created', handleOrderEvent)
                .listen('OrderCreated', handleOrderEvent);
        }

        // Optionnel : petit effet "busy" sur des boutons de navigation vers les commandes
        const ordersButtons = document.querySelectorAll('[data-orders-link]');
        ordersButtons.forEach((btn) => {
            btn.addEventListener('click', function () {
                this.classList.add('is-busy');
            });
        });
    });
</script>
