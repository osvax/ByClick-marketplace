/**
 * Delivery point map.
 *
 * Leaflet is loaded lazily from a CDN only on pages that actually contain a
 * map. No extra npm dependency is required; if the CDN is unavailable the
 * map container falls back to a status message and the regular point list.
 */

const LEAFLET_VERSION = '1.9.4';
const LEAFLET_CSS = `https://unpkg.com/leaflet@${LEAFLET_VERSION}/dist/leaflet.css`;
const LEAFLET_JS = `https://unpkg.com/leaflet@${LEAFLET_VERSION}/dist/leaflet.js`;

let leafletPromise = null;

function loadLeaflet() {
    if (window.L) {
        return Promise.resolve(window.L);
    }

    if (leafletPromise) {
        return leafletPromise;
    }

    leafletPromise = new Promise((resolve, reject) => {
        if (!document.querySelector('link[data-va-leaflet]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = LEAFLET_CSS;
            link.dataset.vaLeaflet = 'true';
            document.head.appendChild(link);
        }

        const existing = document.querySelector('script[data-va-leaflet]');

        if (existing) {
            existing.addEventListener('load', () => resolve(window.L), { once: true });
            existing.addEventListener('error', () => reject(new Error('Leaflet failed to load')), { once: true });

            return;
        }

        const script = document.createElement('script');
        script.src = LEAFLET_JS;
        script.async = true;
        script.dataset.vaLeaflet = 'true';
        script.addEventListener('load', () => resolve(window.L), { once: true });
        script.addEventListener('error', () => reject(new Error('Leaflet failed to load')), { once: true });
        document.head.appendChild(script);
    });

    return leafletPromise;
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (character) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    })[character]);
}

function pointPopup(point) {
    return [
        `<strong>${escapeHtml(point.city)}, ${escapeHtml(point.address)}</strong>`,
        point.schedule ? `<br><span>${escapeHtml(point.schedule)}</span>` : '',
        `<br><span>${escapeHtml(point.type)}</span>`,
    ].join('');
}

function pinIcon(L) {
    return L.divIcon({
        className: 'va_map-pin',
        html: '<span class="va_map-pin__dot"></span>',
        iconSize: [30, 38],
        iconAnchor: [15, 36],
        popupAnchor: [0, -30],
    });
}

function initMap(element, L) {
    const signature = `${element.dataset.points}|${element.dataset.selectedId}`;

    if (element.dataset.vaMapSignature === signature) {
        return;
    }

    if (element._vaMap) {
        element._vaMap.remove();
        element._vaMap = null;
    }

    const points = JSON.parse(element.dataset.points || '[]')
        .filter((point) => Number.isFinite(point.lat) && Number.isFinite(point.lng));

    const status = element.querySelector('[data-va-map-status]');

    if (points.length === 0) {
        if (status) {
            status.textContent = 'Для пунктов выдачи пока не заданы координаты.';
        }

        element.dataset.vaMapSignature = signature;

        return;
    }

    status?.remove();

    const map = L.map(element, {
        scrollWheelZoom: false,
        zoomControl: true,
    }).setView([53.7098, 27.9534], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>',
    }).addTo(map);

    const icon = pinIcon(L);
    const markers = [];
    const bounds = [];

    points.forEach((point) => {
        const marker = L.marker([point.lat, point.lng], {
            icon,
            title: `${point.city}, ${point.address}`,
        }).addTo(map);

        marker.bindPopup(pointPopup(point), { closeButton: true });
        marker.on('click', () => {
            const selector = element.dataset.selectSelector;
            const select = selector ? document.querySelector(selector) : null;

            if (select) {
                select.value = String(point.id);
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }

            window.dispatchEvent(new CustomEvent('va:delivery-point-selected', { detail: point }));
        });

        markers.push({ point, marker });
        bounds.push([point.lat, point.lng]);
    });

    const selectedId = String(element.dataset.selectedId || '');
    const selected = markers.find(({ point }) => String(point.id) === selectedId);

    if (selected) {
        selected.marker.openPopup();
        map.setView([selected.point.lat, selected.point.lng], 12);
    } else if (bounds.length > 1) {
        map.fitBounds(bounds, { padding: [32, 32], maxZoom: 11 });
    } else {
        map.setView(bounds[0], 12);
    }

    element._vaMap = map;
    element.dataset.vaMapSignature = signature;
}

export function initDeliveryMaps(root = document) {
    const elements = root.querySelectorAll('[data-va-delivery-map]');

    if (elements.length === 0) {
        return;
    }

    loadLeaflet()
        .then((L) => {
            elements.forEach((element) => {
                if (element.isConnected) {
                    initMap(element, L);
                }
            });
        })
        .catch(() => {
            elements.forEach((element) => {
                const status = element.querySelector('[data-va-map-status]');

                if (status) {
                    status.textContent = 'Не удалось загрузить карту. Список пунктов доступен ниже.';
                }
            });
        });
}

document.addEventListener('livewire:init', () => {
    window.Livewire?.hook?.('morph.updated', () => {
        window.requestAnimationFrame(() => initDeliveryMaps());
    });
});

document.addEventListener('livewire:navigated', () => {
    window.requestAnimationFrame(() => initDeliveryMaps());
});
