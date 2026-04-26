(() => {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        return;
    }

    const meta = document.querySelector('meta[name="vapid-public-key"]');
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');

    if (!meta || !csrfMeta) {
        return;
    }

    const vapidPublicKey = meta.getAttribute('content');
    const csrfToken = csrfMeta.getAttribute('content');

    if (!vapidPublicKey) {
        return;
    }

    const urlB64ToUint8Array = (b64) => {
        const padding = '='.repeat((4 - (b64.length % 4)) % 4);
        const safe = (b64 + padding).replace(/-/g, '+').replace(/_/g, '/');
        const raw = atob(safe);
        const out = new Uint8Array(raw.length);
        for (let i = 0; i < raw.length; ++i) {
            out[i] = raw.charCodeAt(i);
        }
        return out;
    };

    const sendSubscription = async (subscription) => {
        const payload = subscription.toJSON();
        const res = await fetch('/push/subscribe', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                endpoint: payload.endpoint,
                keys: payload.keys,
                content_encoding: 'aesgcm',
            }),
        });
        return res.ok;
    };

    const subscribe = async () => {
        const registration = await navigator.serviceWorker.ready;

        let subscription = await registration.pushManager.getSubscription();
        if (!subscription) {
            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlB64ToUint8Array(vapidPublicKey),
            });
        }
        await sendSubscription(subscription);
    };

    window.bebikEnablePush = async () => {
        if (!('Notification' in window)) {
            return 'unsupported';
        }
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            return permission;
        }
        try {
            await subscribe();
            return 'granted';
        } catch (e) {
            console.error('[bebik push]', e);
            return 'error';
        }
    };

    // Auto-resubscribe if permission already granted (keeps endpoint fresh)
    if (Notification.permission === 'granted') {
        navigator.serviceWorker.ready.then(() => subscribe().catch(() => {}));
    }
})();
