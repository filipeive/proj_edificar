import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet-draw';
import 'leaflet-draw/dist/leaflet.draw.css';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerIconRetina from 'leaflet/dist/images/marker-icon-2x.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconUrl: markerIcon,
    iconRetinaUrl: markerIconRetina,
    shadowUrl: markerShadow,
});

const defaultCenter = [-17.8786, 36.8883];
const tileLayerUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
const tileAttribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';

function createMap(element, center, zoom) {
    const map = L.map(element).setView(center, zoom);
    L.tileLayer(tileLayerUrl, {
        maxZoom: 19,
        attribution: tileAttribution,
    }).addTo(map);
    return map;
}

function parseBoundary(value) {
    if (!value) return null;
    try {
        const geometry = JSON.parse(value);
        return geometry?.type === 'Polygon' || geometry?.type === 'MultiPolygon' ? geometry : null;
    } catch {
        return null;
    }
}

function initializePointEditor(element) {
    const component = element.closest('[data-location-map]');
    const latitudeInput = component.querySelector('[name="latitude"]');
    const longitudeInput = component.querySelector('[name="longitude"]');
    const status = component.querySelector('[data-map-coordinate-status]');
    const latitude = Number(latitudeInput.value);
    const longitude = Number(longitudeInput.value);
    const hasLocation = latitudeInput.value !== '' && longitudeInput.value !== '';
    const map = createMap(element, hasLocation ? [latitude, longitude] : defaultCenter, hasLocation ? 14 : 11);
    let marker = null;

    const setPoint = (latlng) => {
        latitudeInput.value = latlng.lat.toFixed(7);
        longitudeInput.value = latlng.lng.toFixed(7);
        status.textContent = `${latitudeInput.value}, ${longitudeInput.value}`;
        if (!marker) {
            marker = L.marker(latlng, { draggable: true }).addTo(map);
            marker.on('dragend', () => setPoint(marker.getLatLng()));
        } else {
            marker.setLatLng(latlng);
        }
    };

    if (hasLocation) setPoint({ lat: latitude, lng: longitude });
    map.on('click', (event) => setPoint(event.latlng));
}

function initializeAreaEditor(element) {
    const component = element.closest('[data-location-map]');
    const latitudeInput = component.querySelector('[name="latitude"]');
    const longitudeInput = component.querySelector('[name="longitude"]');
    const boundaryInput = component.querySelector('[name="boundary"]');
    const status = component.querySelector('[data-map-coordinate-status]');
    const boundary = parseBoundary(element.dataset.boundary);
    const latitude = Number(latitudeInput.value);
    const longitude = Number(longitudeInput.value);
    const hasLocation = latitudeInput.value !== '' && longitudeInput.value !== '';
    const map = createMap(element, hasLocation ? [latitude, longitude] : defaultCenter, hasLocation ? 13 : 10);
    const drawnItems = new L.FeatureGroup().addTo(map);

    if (boundary) {
        const boundaryLayer = L.geoJSON(boundary);
        boundaryLayer.eachLayer((layer) => drawnItems.addLayer(layer));
        map.fitBounds(boundaryLayer.getBounds(), { padding: [20, 20] });
    }

    map.addControl(new L.Control.Draw({
        draw: {
            polygon: { allowIntersection: false, showArea: true },
            polyline: false,
            rectangle: false,
            circle: false,
            circlemarker: false,
            marker: false,
        },
        edit: { featureGroup: drawnItems },
    }));

    const storeBoundary = () => {
        const layer = drawnItems.getLayers()[0];
        if (!layer) {
            boundaryInput.value = '';
            return;
        }

        const geometry = layer.toGeoJSON().geometry;
        boundaryInput.value = JSON.stringify(geometry);
        const center = layer.getBounds().getCenter();
        latitudeInput.value = center.lat.toFixed(7);
        longitudeInput.value = center.lng.toFixed(7);
        status.textContent = `${latitudeInput.value}, ${longitudeInput.value}`;
    };

    map.on(L.Draw.Event.CREATED, (event) => {
        drawnItems.clearLayers();
        drawnItems.addLayer(event.layer);
        storeBoundary();
    });
    map.on(L.Draw.Event.EDITED, storeBoundary);
    map.on(L.Draw.Event.DELETED, storeBoundary);
}

function initializeDisplayMap(element) {
    const payloadElement = document.getElementById(element.dataset.mapData);
    const payload = payloadElement ? JSON.parse(payloadElement.textContent) : { zones: [] };
    const map = createMap(element, defaultCenter, 10);
    const bounds = [];

    payload.zones.forEach((zone) => {
        const boundary = zone.boundary;
        if (boundary) {
            const polygon = L.geoJSON(boundary, {
                style: { color: '#0f766e', weight: 2, fillColor: '#14b8a6', fillOpacity: 0.14 },
            }).addTo(map);
            polygon.bindPopup(`<strong>${L.Util.escapeHTML(zone.name)}</strong>`);
            bounds.push(polygon.getBounds());
        } else if (zone.latitude !== null && zone.longitude !== null) {
            const marker = L.circleMarker([zone.latitude, zone.longitude], {
                radius: 8, color: '#0f766e', fillColor: '#14b8a6', fillOpacity: 0.9,
            }).addTo(map).bindPopup(`<strong>Zona: ${L.Util.escapeHTML(zone.name)}</strong>`);
            bounds.push(marker.getLatLng());
        }

        (zone.cells || []).forEach((cell) => {
            const marker = L.circleMarker([cell.latitude, cell.longitude], {
                radius: 6, color: '#1d4ed8', fillColor: '#60a5fa', fillOpacity: 0.95,
            }).addTo(map).bindPopup(`<strong>Célula: ${L.Util.escapeHTML(cell.name)}</strong><br>${L.Util.escapeHTML(zone.name)}`);
            bounds.push(marker.getLatLng());
        });
    });

    if (bounds.length > 0) map.fitBounds(L.latLngBounds(bounds), { padding: [28, 28], maxZoom: 14 });
}

function createCongregationPin(congregation) {
    const isPrimary = Boolean(congregation.is_primary);
    const color = congregation.color || (isPrimary ? '#d97706' : '#059669');
    const label = isPrimary ? 'SEDE' : (congregation.city || 'CONG');

    return L.divIcon({
        className: 'custom-congregation-pin',
        html: `
            <div style="position:relative;display:flex;flex-direction:column;align-items:center;cursor:pointer;filter:drop-shadow(0 4px 6px rgba(0,0,0,0.35));transition:transform 0.18s ease-in-out;" onmouseenter="this.style.transform='scale(1.12)'" onmouseleave="this.style.transform='scale(1)'">
                ${isPrimary ? `<div style="position:absolute;top:-4px;width:42px;height:42px;border-radius:50%;background:${color};opacity:0.4;animation:ping 2s cubic-bezier(0,0,0.2,1) infinite;"></div>` : ''}
                <div style="position:relative;display:flex;align-items:center;justify-content:center;width:${isPrimary ? '38px' : '32px'};height:${isPrimary ? '38px' : '32px'};border-radius:50%;background:${color};color:#ffffff;border:2.5px solid #ffffff;box-shadow:0 3px 10px rgba(0,0,0,0.25);">
                    ${isPrimary 
                        ? '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M8 0a.5.5 0 0 1 .5.5v.5h1a.5.5 0 0 1 0 1h-1v1.5a.5.5 0 0 1-1 0V2h-1a.5.5 0 0 1 0-1h1V.5A.5.5 0 0 1 8 0z"/><path d="M4.5 5A1.5 1.5 0 0 0 3 6.5V14h10V6.5A1.5 1.5 0 0 0 11.5 5h-7zM8 7a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0V8a1 1 0 0 1 1-1z"/></svg>'
                        : '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 0-2 2v1.5H4a1 1 0 0 0-1 1V14a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V5.5a1 1 0 0 0-1-1h-2V3a2 2 0 0 0-2-2zm0 1a1 1 0 0 1 1 1v1.5H7V3a1 1 0 0 1 1-1z"/></svg>'}
                </div>
                <div style="width:0;height:0;border-left:6px solid transparent;border-right:6px solid transparent;border-top:7px solid ${color};margin-top:-2px;"></div>
                <div style="margin-top:2px;background:#0f172a;color:#ffffff;font-size:9px;font-weight:800;padding:1px 6px;border-radius:4px;white-space:nowrap;letter-spacing:0.3px;box-shadow:0 1px 3px rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.25);">
                    ${isPrimary ? '★ SEDE' : L.Util.escapeHTML(label)}
                </div>
            </div>
        `,
        iconSize: [40, 52],
        iconAnchor: [20, 52],
        popupAnchor: [0, -54]
    });
}

function initializeNationalCongregationMap(element) {
    const payloadElement = document.getElementById(element.dataset.mapData);
    const payload = payloadElement ? JSON.parse(payloadElement.textContent) : { congregations: [] };
    const map = createMap(element, [-18.6656, 35.5295], 6);
    const bounds = [];
    const markersMap = {};

    window.__nationalMapInstance = map;
    window.__nationalCongregations = payload.congregations || [];

    payload.congregations.forEach((congregation) => {
        const isPrimary = Boolean(congregation.is_primary);
        const color = congregation.color || (isPrimary ? '#d97706' : '#059669');
        const icon = createCongregationPin(congregation);

        const marker = L.marker([congregation.latitude, congregation.longitude], {
            icon,
            title: congregation.name,
            riseOnHover: true,
        }).addTo(map);

        markersMap[congregation.id] = marker;

        const safeName = L.Util.escapeHTML(congregation.name);
        const pastorName = L.Util.escapeHTML(congregation.pastor_name || 'Gabinete Pastoral');
        const pastorRole = L.Util.escapeHTML(congregation.pastor_role || 'Pastor');
        const pastorPhone = L.Util.escapeHTML(congregation.pastor_phone || 'Não registado');
        const pastorEmail = L.Util.escapeHTML(congregation.pastor_email || 'Não registado');
        const local = L.Util.escapeHTML(congregation.city || congregation.address || 'Moçambique');

        const statusBadge = congregation.is_active
            ? '<span style="background:#dcfce7;color:#166534;font-size:10px;font-weight:800;padding:2px 8px;border-radius:9999px;">Ativa</span>'
            : '<span style="background:#f3f4f6;color:#4b5563;font-size:10px;font-weight:800;padding:2px 8px;border-radius:9999px;">Inativa</span>';

        const primaryBadge = congregation.is_primary
            ? '<span style="background:#fef3c7;color:#92400e;font-size:10px;font-weight:900;padding:2px 8px;border-radius:9999px;border:1px solid #fde68a;">★ SEDE NACIONAL</span>'
            : '';

        const edificarBadge = congregation.has_edificar
            ? '<span style="background:#e0f2fe;color:#0369a1;font-size:10px;font-weight:800;padding:2px 8px;border-radius:9999px;">Projeto Edificar</span>'
            : '';

        const popupContent = `
            <div style="min-width:240px;max-width:280px;font-family:system-ui,-apple-system,BlinkMacSystemFont,sans-serif;color:#1e293b;line-height:1.45;">
                <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;margin-bottom:6px;">
                    ${primaryBadge}
                    ${statusBadge}
                    ${edificarBadge}
                </div>
                <h4 style="margin:2px 0 6px 0;font-size:14px;font-weight:800;color:#0f172a;letter-spacing:-0.01em;">${safeName}</h4>
                <div style="font-size:11px;color:#475569;margin-bottom:8px;background:#f8fafc;padding:8px 10px;border-radius:8px;border:1px solid #e2e8f0;">
                    <div><strong>${pastorRole}:</strong> ${pastorName}</div>
                    <div><strong>Contacto:</strong> ${pastorPhone}</div>
                    <div><strong>Email:</strong> ${pastorEmail}</div>
                    <div><strong>Local:</strong> ${local}</div>
                </div>
                <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:4px;text-align:center;margin-bottom:10px;background:#ffffff;border:1px solid #e2e8f0;border-radius:8px;padding:6px 4px;">
                    <div>
                        <div style="font-size:13px;font-weight:800;color:#0f172a;">${congregation.users_count}</div>
                        <div style="font-size:9px;text-transform:uppercase;color:#64748b;font-weight:700;">Membros</div>
                    </div>
                    <div style="border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;">
                        <div style="font-size:13px;font-weight:800;color:#0f172a;">${congregation.zones_count}</div>
                        <div style="font-size:9px;text-transform:uppercase;color:#64748b;font-weight:700;">Zonas</div>
                    </div>
                    <div>
                        <div style="font-size:13px;font-weight:800;color:#0f172a;">${congregation.cells_count}</div>
                        <div style="font-size:9px;text-transform:uppercase;color:#64748b;font-weight:700;">Células</div>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;gap:6px;">
                    <button type="button" onclick="window.__openCongregationModal(${congregation.id})" style="width:100%;text-align:center;background:${color};color:#ffffff;border:none;padding:7px 12px;border-radius:20px;font-weight:800;font-size:11px;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.15);display:flex;align-items:center;justify-content:center;gap:6px;">
                        <span>Ver Ficha Completa &rarr;</span>
                    </button>
                    <a href="${congregation.show_url}" style="width:100%;text-align:center;background:#059669;color:#ffffff;text-decoration:none;padding:7px 12px;border-radius:20px;font-weight:800;font-size:11px;display:block;">
                        Acessar Congregação &rarr;
                    </a>
                </div>
            </div>
        `;

        marker.bindPopup(popupContent);
        bounds.push(marker.getLatLng());
    });

    window.__focusCongregation = function(id) {
        const target = (payload.congregations || []).find((c) => c.id === id);
        if (!target) return;
        map.flyTo([target.latitude, target.longitude], 12, { animate: true, duration: 1 });
        const m = markersMap[id];
        if (m) {
            m.openPopup();
        }
    };

    window.__openCongregationModal = function(id) {
        const target = (payload.congregations || []).find((c) => c.id === id);
        if (target) {
            window.dispatchEvent(new CustomEvent('congregation-modal-open', { detail: target }));
        }
    };

    setTimeout(() => {
        map.invalidateSize();
        if (bounds.length > 0) {
            map.fitBounds(L.latLngBounds(bounds), { padding: [40, 40], maxZoom: 9 });
        }
    }, 250);

    window.addEventListener('resize', () => {
        map.invalidateSize();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-location-editor]').forEach((element) => {
        if (element.dataset.mode === 'area') initializeAreaEditor(element);
        else initializePointEditor(element);
    });

    document.querySelectorAll('[data-church-map]').forEach(initializeDisplayMap);
    document.querySelectorAll('[data-national-congregation-map]').forEach(initializeNationalCongregationMap);
});