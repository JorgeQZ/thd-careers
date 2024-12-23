let map = L.map('map').setView([23.634501, -102.552784], 5); // Coordenadas y zoom inicial para México

let markers = new L.markerClusterGroup({
    animateAddingMarkers: true,
    polygonOptions: {
        color: 'orange'
    },
    iconCreateFunction: function (cluster) {
        let childCount = cluster.getChildCount(); // Número de elementos en el clúster

        // Personaliza el estilo del clúster
        return L.divIcon({
            html: `<div style="background-color: #f96302; color: #ffffff; border-radius: 50%; display: flex; justify-content: center; align-items: center; width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                        ${childCount}
                   </div>`,
            className: '', // Eliminamos la clase predeterminada
            iconSize: [40, 40] // Tamaño del ícono
        });
    }
});

L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    maxZoom: 19,
    minZoom: 5,


}).addTo(map);

function getCircleStyle(tipo) {
    switch (tipo) {
        case "Centros Logísticos":
            return {
                color: "rgba(153, 153, 153, 0.8)",
                radius: 15,
                fillColor: "rgba(153, 153, 153, 1)",
                fillOpacity: 1,
                weight: 10
            };
        case "Tienda":
            return {
                color: "rgba(249, 99, 2, 0.8)",
                radius: 15,
                fillColor: "#f96302",
                fillOpacity: 1,
                weight: 10
            };
        case "Oficina de Apoyo a tiendas":
            return {
                color: "rgba(39, 39, 39, 0.8)",
                radius: 15,
                fillColor: "#272727",
                fillOpacity: 1,
                weight: 10
            };
        default:
            return {
                color: "rgba(0, 0, 255, 0.8)",
                radius: 15,
                fillColor: "blue",
                fillOpacity: 1,
                weight: 10
            };
    }
}

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
        throw new Error(`HTTP error! Status: ${response.status}`);
    }
    return response.json();
}).then(data => {
    add_markers(data);
});

function add_markers(data) {
    data.forEach(element => {

        let coord = element.coordenadas ? element.coordenadas.split(',').map(Number) : null;
        if (!coord || coord.length !== 2 || isNaN(coord[0]) || isNaN(coord[1])) {
            console.error('Coordenadas inválidas:', element.coordenadas);
            return;
        }





        if (coord[0] != 0 && coord[1] != 0) {
            let tipo = element.tipo_de_negocio;
            let nombre = element.nombre_de_tienda;
            let ubicacion = element.ubicacion;
            let numeroDeTienda = element.numero_de_tienda; // Obtener número de tienda


            let style = getCircleStyle(tipo);
            let circle = L.circleMarker(coord, style).bindPopup('Cargando información...');

            markers.addLayer(circle);


            fetch(map_vars.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'action': 'get_related_vacantes',
                    'numero_de_tienda': numeroDeTienda
                })
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Error en la consulta de vacantes.');
                }
                return response.json();
            }).then(responseData => {
                if (responseData.success && responseData.data.length > 0) {

                    let totalItems = responseData.data.length;
                    let links = responseData.data.map(vacante =>
                        `<li><a href="${vacante.url}" target="_blank">${vacante.title} <span>+</span></a></li>`
                    ).join('');
                    circle.bindPopup(`
                        <div class="header">
                            <div class="title">  <strong>${nombre} (${totalItems} ${totalItems === 1 ? 'vacante' : 'vacantes'})</strong></div>
                        ${ubicacion}<br>
                        </div>
                        <ul>${links}</ul>
                    `).openPopup();
                } else {

                    circle.bindPopup(`
                        <div class="header">
                            <div class="title"><strong>${nombre}</strong></div>
                            ${ubicacion}<br>
                        </div>
                        <ul>
                            <em>No hay vacantes relacionadas.</em>
                        </ul>
                    `).openPopup();
                }
            }).catch(error => {
                console.error('Error al cargar las vacantes:', error);
            });
        }
    });
    map.addLayer(markers);
}

