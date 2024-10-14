let map = L.map('map').setView([25.683225574052216, -100.35669100732787], 16);
let markers = new L.markerClusterGroup(
    {
        animateAddingMarkers: true,
        polygonOptions: {
            color: 'orange'
        }
    }
);

L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    maxZoom: 19,
}).addTo(map);


let icon = L.icon({
    iconUrl: map_vars.theme_uri + '/img/maps-and-flags.png',
    iconSize: [30, 30], // size of the icon
    iconAnchor: [30, 30], // point of the icon which will correspond to marker's location
});


fetch(map_vars.ajax_url, {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: new URLSearchParams({
        'action': 'get_stores_locations'
    })
}).then(response => {
    if (!response.ok) {
        throw new Error('Error de conexión.');
    }
    return response.json();
}).then(data => {
    add_markers(data);
}).catch(error => {
    console.error('Existe un error en la petición AJAX');
});

function add_markers(data) {
    data.forEach(element => {
        let coord = element.coordenadas;
        markers.addLayer( // Grouping Function
            L.marker( // Store location
                coord.split(','), // Coordinates
                { icon: icon } // Custom Icon
            ).addTo(map)
        );
    });
    map.addLayer(markers);
}
