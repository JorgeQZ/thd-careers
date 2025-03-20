
let map = L.map('map').setView([24.5, -102.552784], 5); // Coordenadas y zoom inicial para México

// Agregar capa de mapa base
L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    maxZoom: 19,
    minZoom: 5,
}).addTo(map);

// Agregar controles de zoom
document.getElementById('map').insertAdjacentHTML('beforeend', `
    <div class="search-container">
    <div class="search-container-title">
        <img src='${map_vars.theme_uri}/imgs/logo-thd.jpg' alt='The Home Depot' class='logo'>
        <div class="title">Encuentra tu tienda más cercana</div>
    </div>
    <input type="text" id="searchBox" class="search-box" placeholder="Buscar tienda o ubicación">
    <ul id="searchResults" class="list" onmouseover="map.scrollWheelZoom.disable()" onmouseleave="map.scrollWheelZoom.enable()">
    </ul>
    </div>
`);

// Deshabilitar el zoom con la rueda del mouse al hacer clic en el cuadro de búsqueda
let searchBox = document.getElementById('searchBox');
searchBox.addEventListener('focus', function() {
    map.scrollWheelZoom.disable();
});

// Habilitar el zoom con la rueda del mouse al salir del cuadro de búsqueda
searchBox.addEventListener('blur', function() {
    map.scrollWheelZoom.enable();
});

// Evitar que se seleccione el texto al hacer doble clic en el cuadro de búsqueda
searchBox.addEventListener('dblclick', function(event) {
    event.preventDefault();
    event.stopPropagation();
});

// Mostrar los resultados de la búsqueda
let searchResults = document.getElementById('searchResults');

// Filtrar las tiendas al escribir en el cuadro de búsqueda
searchBox.addEventListener('input', function() {
    let query = searchBox.value.toLowerCase();
    searchResults.innerHTML = '';
    searchResults.style.display = 'none';
    console.log(markers);

    if (query.length > 3) { // Solo buscar si hay al menos 3 caracteres

        // Filtrar las tiendas que coincidan con la búsqueda
        let matches = markers.filter(({nombre, ubicacion }) =>
            nombre.toLowerCase().includes(query) || ubicacion.toLowerCase().includes(query)
        );

        // Mostrar los resultados en la lista si hay coincidencias
        if (matches.length > 0) {
            searchResults.style.display = 'block';
            matches.forEach(({ circle, nombre, ubicacion, numeroDeTienda}) => {
                // Crear un elemento de lista para cada coincidencia
                let li = document.createElement('li');

                // contenido de la lista
                li.innerHTML = `<div class="li-title"><img src='${map_vars.theme_uri}/imgs/logo-thd.jpg' alt='${nombre}'> ${nombre.toLowerCase().replace(/\b\w/g, c => c.toUpperCase())}</div><div class="small">${ubicacion.toLowerCase().replace(/\b\w/g, c => c.toUpperCase())}</div>`;

                // Centrar el mapa en la tienda seleccionada
                li.addEventListener('click', function() {
                    let targetLatLng = circle.getLatLng();

                    if (isMobile()) {
                        let offsetLat = -7; // Aumenta este valor para bajar más el mapa (puedes ajustar según prueba)
                        let newZoom = 5; // Ajusta el zoom para móviles

                        let newLatLng = L.latLng(targetLatLng.lat - offsetLat, targetLatLng.lng);

                        map.setView(newLatLng, newZoom, { animate: true }); // Cambia la vista con animación
                    } else {
                        let newZoom = Math.max(map.getZoom(), 10);
                        map.setView(targetLatLng, newZoom, { animate: true });
                    }

                    // Esperar un momento antes de abrir el popup para mejorar la UX
                    setTimeout(() => {
                        circle.openPopup();
                    }, 300);

                    loadMoreVacantes(numeroDeTienda, 1, nombre, ubicacion, circle._leaflet_id);
                    let clickedLi = this;
                    searchResults.innerHTML = '';
                    searchResults.appendChild(clickedLi);
                    searchResults.style.display = 'block';
                });

                // Agregar el elemento de lista al contenedor de resultados
                searchResults.appendChild(li);
            });
        }
    }
});

// Estilo de los círculos en el mapa
function getCircleStyle(tipo, zoomLevel) {
    let baseRadius = 15;
    let scaleFactor = zoomLevel / 10; // Ajusta el tamaño según el nivel de zoom
    let radius = Math.max(5, baseRadius * scaleFactor);

    let styles = {
        "Centros Logísticos": { color: "rgba(153, 153, 153, 0.8)", fillColor: "rgba(153, 153, 153, 1)" },
        "Tienda": { color: "rgba(249, 99, 2, 0.8)", fillColor: "#f96302" },
        "Oficina de Apoyo a tiendas": { color: "rgba(39, 39, 39, 0.8)", fillColor: "#272727" },
        "default": { color: "rgba(249, 99, 2, 0.8)", fillColor: "#f96302" }
    };

    // Obtener el estilo del círculo según el tipo de tienda
    let style = styles[tipo] || styles["default"];
    return { ...style, radius, fillOpacity: 1, weight: 10 };
}

// Obtener las ubicaciones de las tiendas
jQuery.ajax({
    url: map_vars.ajax_url,
    type: 'POST',
    dataType: 'json',
    data: {
        action: 'get_stores_locations'
    },
    success: function(data) {
        add_markers(data);
    },
    error: function(xhr, status, error) {
        console.error('Hubo un error en la solicitud:', error);
    }
});

let markers = [];

// Agregar los marcadores al mapa
function add_markers(data) {
    data.forEach(element => {
        let coord = element.coordenadas ? element.coordenadas.split(',').map(Number) : null;
        if (!coord || coord.length !== 2 || isNaN(coord[0]) || isNaN(coord[1])) {
            console.error('Coordenadas inválidas:', element.coordenadas);
            return;
        }

        if (coord[0] !== 0 && coord[1] !== 0) {
            let {
                tipo_de_negocio: tipo,
                nombre_de_tienda: nombre,
                ubicacion,
                numero_de_tienda: numeroDeTienda,
                vacantes = 0
            } = element;

            let style = getCircleStyle(tipo, map.getZoom());
            let circle = L.circleMarker(coord, style).bindPopup('Cargando información...');

            // **Crear un marcador con número de vacantes**
            let icon = L.divIcon({
                className: 'vacantes-label',
                html: `<div class="vacantes-number">${vacantes}</div>`,
                iconSize: [25, 25],
                iconAnchor: [12, 12]
            });

            // Crea un marcador con el número de vacantes
            let labelMarker = L.marker(coord, {
                icon: icon,
                interactive: false,
                keyboard: false,
            });

            // Centrar el mapa en la tienda seleccionada al hacer clic en el círculo
            circle.on('click', function(event) {
                let targetLatLng = circle.getLatLng();

                if (isMobile()) {
                    let offsetLat = -6; // Aumenta este valor para bajar más el mapa (puedes ajustar según prueba)
                    let newZoom = 5; // Ajusta el zoom para móviles

                    let newLatLng = L.latLng(targetLatLng.lat - offsetLat, targetLatLng.lng);

                    map.setView(newLatLng, newZoom, { animate: true }); // Cambia la vista con animación
                } else {
                    // let newZoom = Math.max(map.getZoom(), 10);
                    map.setView(targetLatLng, 10, { animate: true });
                }

                // Esperar un momento antes de abrir el popup para mejorar la UX
                setTimeout(() => {
                    circle.openPopup();
                }, 300);

                loadMoreVacantes(numeroDeTienda, 1, nombre, ubicacion, circle._leaflet_id);
            });

            circle.addTo(map); // Agregar el círculo al mapa
            labelMarker.addTo(map); // Agregar el número sobre el círculo

            // Agregar el círculo y el marcador al arreglo de marcadores
            markers.push({ circle, labelMarker, tipo, nombre, ubicacion, numeroDeTienda });
        }
    });
}

map.on('zoomend', function() {
    let zoomLevel = map.getZoom();
    markers.forEach(({ circle, labelMarker, tipo }) => {
        circle.setStyle(getCircleStyle(tipo, zoomLevel));

        // Redimensionar el texto basado en el zoom
        let newSize = zoomLevel > 10 ? 20 : 15;
        let labelElement = labelMarker.getElement();
        if (labelElement) {
            labelElement.querySelector('.vacantes-number').style.fontSize = `${newSize}px`;
        }
    });
});

let loadedVacanciesByCircle = {};

function loadMoreVacantes(numeroDeTienda, currentPage, nombre, ubicacion, circleId) {
    let circle = markers.find(m => m.circle._leaflet_id === circleId)?.circle;

    if (!circle) {
        console.error('No se encontró el círculo para cargar más vacantes.');
        return;
    }

    if (!loadedVacanciesByCircle[circle._leaflet_id]) {
        loadedVacanciesByCircle[circle._leaflet_id] = new Set();
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
                    .filter(vacante => !loadedVacanciesByCircle[circle._leaflet_id].has(vacante.title))
                    .map(vacante => {
                        loadedVacanciesByCircle[circle._leaflet_id].add(vacante.title);
                        return `<li><a href="${vacante.url}" rel="noopener noreferrer" target="_blank">${vacante.title} <span>+</span></a></li>`;
                    }).join('');

                if (!newLinks && responseData.data.length === 0) {
                    let popupContent = `
                        <div class="header">
                            <div class="title"><strong>${nombre}</strong></div>
                            ${ubicacion}<br>
                        </div>
                        <ul>${existingLinks}</ul>
                        <p style="padding: 5px;">Por el momento, no hay vacantes disponibles</p>
                    `;
                    circle.setPopupContent(popupContent);
                    return;
                }

                let combinedLinks = existingLinks + newLinks;
                let popupContent = `
                    <div class="header">
                        <div class="title"><strong>${nombre}</strong></div>
                        ${ubicacion}<br>
                    </div>
                    <ul>${combinedLinks}</ul>
                    ${responseData.data.length > 0 ?
                        `<button class="load-more" onclick="loadMoreVacantes('${numeroDeTienda}', ${currentPage + 1}, '${nombre}', '${ubicacion}', ${circle._leaflet_id})">Cargar más vacantes</button>` :
                        `<p style="padding: 5px;">Por el momento, no más hay vacantes disponibles.</p>`}
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

function isMobile() {
    return /Mobi|Android/i.test(navigator.userAgent);
}
