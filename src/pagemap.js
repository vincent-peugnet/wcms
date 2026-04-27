import 'leaflet/dist/leaflet.css';
import icon from 'leaflet/dist/images/marker-icon.png';
import icon_2x from 'leaflet/dist/images/marker-icon-2x.png';
import shadow from 'leaflet/dist/images/marker-shadow.png';
import * as L from 'leaflet';

L.Icon.Default.prototype.options.iconUrl = icon;
L.Icon.Default.prototype.options.iconRetinaUrl = icon_2x;
L.Icon.Default.prototype.options.shadowUrl = shadow;

for (let [id, mapdata] of Object.entries(w_maps_data)) {
    var map = L.map(id).setView([0, 0], 1);

    var pageGroup = L.featureGroup();
    pageGroup.addTo(map);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    for (const page of mapdata.pages) {
        var marker = L.marker([page.latitude, page.longitude]).addTo(pageGroup);
        if (page.read) {
            marker.bindPopup(`<a href="${page.read}">${page.title}</a>`);
        } else {
            marker.bindPopup(page.title);
        }
    }
    map.fitBounds(pageGroup.getBounds());
    if (mapdata.hasOwnProperty('zoom')) {
        map.setZoom(mapdata.zoom);
    }
}
