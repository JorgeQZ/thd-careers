let map = L.map('map').setView([24.5, -102.552784], 5); // Coordenadas y zoom inicial para México

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
                color: "rgba(249, 99, 2, 0.8)",
                radius: 15,
                fillColor: "#f96302",
                fillOpacity: 1,
                weight: 10
            };
    }
}

jQuery.ajax({
    url: map_vars.ajax_url,  // URL del servidor
    type: 'POST',  // Método de la solicitud
    dataType: 'json',  // Esperamos una respuesta en formato JSON
    data: {
        action: 'get_stores_locations'  // Acción que se envía al servidor
    },
    success: function(data) {
        // Si la respuesta es exitosa
        // console.log('Datos recibidos:', data);  // Aquí manejamos los datos
        add_markers(data);  // Por ejemplo, pasarlos a una función que maneje los markers
    },
    error: function(xhr, status, error) {
        // Si ocurre un error en la solicitud
        console.error('Hubo un error en la solicitud:', error);
    }
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

            circle.on('click', function(event) {
                loadMoreVacantes(numeroDeTienda, currentPage, nombre, ubicacion, circle._leaflet_id);
            });


        }
    });
    map.addLayer(markers);
}

let currentPage = 1;
const vacantesPerPage = 5;  // Número de vacantes por página

// Almacenamiento de las vacantes cargadas por ubicación
let loadedVacanciesByCircle = {};

function loadMoreVacantes(numeroDeTienda, currentPage, nombre, ubicacion, circleId) {
    let circle = markers.getLayer(circleId);

    if (!circle) {
        console.error('No se encontró el círculo con el ID:', circleId);
        return;
    }

    if (!loadedVacanciesByCircle[circleId]) {
        loadedVacanciesByCircle[circleId] = new Set();
    }

    jQuery.ajax({
        url: map_vars.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'get_related_vacantes',
            numero_de_tienda: numeroDeTienda,
            page: currentPage
        },
        success: function (responseData) {
            if (responseData.success) {
                let existingPopupContent = circle.getPopup().getContent() || '';
                let existingLinksMatch = existingPopupContent.match(/<ul>(.*?)<\/ul>/s);
                let existingLinks = existingLinksMatch ? existingLinksMatch[1] : '';

                let newLinks = responseData.data
                    .filter(vacante => !loadedVacanciesByCircle[circleId].has(vacante.title))
                    .map(vacante => {
                        loadedVacanciesByCircle[circleId].add(vacante.title);
                        return `<li><a href="${vacante.url}" target="_blank">${vacante.title} <span>+</span></a></li>`;
                    }).join('');

                // Si no hay nuevos links, mostramos un mensaje y eliminamos el botón
                if (!newLinks && responseData.data.length === 0) {
                    let popupContent = `
                        <div class="header">
                            <div class="title"><strong>${nombre}</strong></div>
                            ${ubicacion}<br>
                        </div>
                        <ul>${existingLinks}</ul>
                        <p style="padding: 5px;">No hay más vacantes disponibles.</p>
                    `;
                    circle.setPopupContent(popupContent);
                    return;
                }

                let combinedLinks = existingLinks + newLinks;

                // Construimos el contenido del popup con o sin botón
                let popupContent = `
                    <div class="header">
                        <div class="title"><strong>${nombre}</strong></div>
                        ${ubicacion}<br>
                    </div>
                    <ul>${combinedLinks}</ul>
                    ${responseData.data.length > 0 ?
                        `<button class="load-more" onclick="loadMoreVacantes('${numeroDeTienda}', ${currentPage + 1}, '${nombre}', '${ubicacion}', ${circleId})">Cargar más vacantes</button>` :
                        `<p style="padding: 5px;">No hay más vacantes disponibles.</p>`}
                `;
                circle.setPopupContent(popupContent);
            } else {
                console.error('No hay más vacantes para cargar.');
            }
        },
        error: function () {
            console.error('Error en la solicitud para cargar más vacantes.');
        }
    });
}
