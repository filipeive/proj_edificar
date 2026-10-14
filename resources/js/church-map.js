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

function initializeNationalCongregationMap(element) {
    const payloadElement = document.getElementById(element.dataset.mapData);
    const payload = payloadElement ? JSON.parse(payloadElement.textContent) : { congregations: [] };
    const map = createMap(element, [-18.6, 35.5], 5);
    const bounds = [];

    payload.congregations.forEach((congregation) => {
        const color = congregation.is_active ? '#15803d' : '#6b7280';
        const marker = L.circleMarker([congregation.latitude, congregation.longitude], {
            radius: congregation.is_primary ? 10 : 8,
            color,
            fillColor: color,
            fillOpacity: 0.88,
            weight: congregation.is_primary ? 3 : 2,
        }).addTo(map);

        const statusBadge = congregation.is_active
            ? '<span style="display:inline-block;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:bold;background:#dcfce7;color:#166534;">Ativa</span>'
            : '<span style="display:inline-block;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:bold;background:#f3f4f6;color:#374151;">Inativa</span>';
        const primaryBadge = congregation.is_primary ? ' <span style="display:inline-block;padding:1px 6px;border-radius:4px;font-size:9px;font-weight:900;text-transform:uppercase;background:#ffedd5;color:#9a3412;">Sede</span>' : '';
        const edificarBadge = congregation.has_edificar ? ' <span style="display:inline-block;padding:1px 6px;border-radius:4px;font-size:9px;font-weight:900;text-transform:uppercase;background:#e0f2fe;color:#0369a1;">Projeto Edificar</span>' : '';
        const actionLinks = [];
        if (congregation.show_url) {
            actionLinks.push(`<a href="${congregation.show_url}" style="font-size:11px;font-weight:bold;color:#c2410c;text-decoration:underline;">Ver detalhes &rarr;</a>`);
        }
        if (congregation.edit_url) {
            actionLinks.push(`<a href="${congregation.edit_url}" style="font-size:11px;font-weight:bold;color:#4b5563;text-decoration:underline;">Editar</a>`);
        }
        const actionsHtml = actionLinks.length > 0
            ? `<div style="margin-top:6px;padding-top:4px;border-top:1px solid #f3f4f6;display:flex;gap:8px;">${actionLinks.join(' · ')}</div>`
            : '';

        const safeName = L.Util.escapeHTML(congregation.name);
        marker.bindPopup(
            `<div style="min-width:180px;font-family:sans-serif;line-height:1.4;">
                <div style="font-weight:bold;font-size:13px;color:#111827;">${safeName}</div>
                <div style="margin:4px 0;">${statusBadge}${primaryBadge}${edificarBadge}</div>
                <div style="font-size:11px;color:#4b5563;">
                    ${congregation.users_count} membros/utilizadores<br>
                    ${congregation.zones_count} zonas · ${congregation.cells_count} células
                </div>
                ${actionsHtml}
            </div>`
        );
        bounds.push(marker.getLatLng());
    });

    if (bounds.length > 0) {
        map.fitBounds(L.latLngBounds(bounds), { padding: [32, 32], maxZoom: 10 });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-location-editor]').forEach((element) => {
        if (element.dataset.mode === 'area') initializeAreaEditor(element);
        else initializePointEditor(element);
    });

    document.querySelectorAll('[data-church-map]').forEach(initializeDisplayMap);
    document.querySelectorAll('[data-national-congregation-map]').forEach(initializeNationalCongregationMap);
});