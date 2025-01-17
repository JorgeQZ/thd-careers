document.addEventListener('DOMContentLoaded', function () {

    const header = document.getElementById("header");
    const banner = document.querySelector(".banner");
    const adminBar = document.getElementById("wpadminbar");

    // Calcular la altura de la barra de administración si existe
    const getAdminBarHeight = () => (adminBar ? adminBar.offsetHeight : 0);

    let isHeaderSticky = false;
    let isBannerSticky = false;

    // Función para actualizar variables dinámicas
    const updateDynamicHeights = () => {
        const headerHeight = header.offsetHeight;
        const adminBarHeight = getAdminBarHeight();

        document.documentElement.style.setProperty("--header-height", `40px`);
        document.documentElement.style.setProperty("--sticky-offset", `${adminBarHeight || 0}px`); // Si no hay wpadminbar, usar 40px como top fijo
    };

    // Actualizar variables al cargar la página
    updateDynamicHeights();

    window.addEventListener("scroll", () => {
        const scrollTop = window.scrollY;
        const adminBarHeight = getAdminBarHeight();

        // Sticky para el header
        if (scrollTop > header.offsetHeight - adminBarHeight && !isHeaderSticky) {
            header.classList.add("sticky");
            isHeaderSticky = true;
            updateDynamicHeights(); // Actualizar variables dinámicas
        } else if (scrollTop <= header.offsetHeight - adminBarHeight && isHeaderSticky) {
            header.classList.remove("sticky");
            isHeaderSticky = false;
            updateDynamicHeights(); // Actualizar variables dinámicas
        }

        // Sticky para el banner (si existe)
        if (banner) {
            if (scrollTop > banner.offsetHeight + header.offsetHeight - adminBarHeight && !isBannerSticky) {
                banner.classList.add("sticky");
                isBannerSticky = true;
            } else if (scrollTop <= banner.offsetHeight + header.offsetHeight - adminBarHeight && isBannerSticky) {
                banner.classList.remove("sticky");
                isBannerSticky = false;
            }
        }
    });

    // Recalcular alturas dinámicas si la ventana cambia de tamaño
    window.addEventListener("resize", updateDynamicHeights);

    // Funciones para el header

    // Funciones de filtros en la secciones vacantes
    const inputSearches = document.querySelectorAll('.input-search');
    const items = document.querySelectorAll('ul.list .item');
    const checkboxesUbicacion = document.querySelectorAll('input[name="ubicacion[]"]');
    const checkboxesTitle = document.querySelectorAll('input[name="title[]"]');


    // Inputs de filtros de vacantes
    inputSearches.forEach((searchContainer) => {
        const input = searchContainer.querySelector('.search-input');
        const suggestionsList = searchContainer.querySelector('.suggestions-list');
        const suggestionItems = suggestionsList.querySelectorAll('li');

        // searchContainer.addEventListener('click', function () {
        //     searchContainer.classList.add('active');
        //     suggestionsList.classList.remove('hidden'); // Mostrar la lista
        // });

        // Al hacer clic en el input
        input.addEventListener('focus', function () {
            input.classList.add('focused'); // Añadir la clase para colorear el borde
            suggestionsList.classList.remove('hidden'); // Mostrar la lista

        });

        // Al hacer clic fuera del input
        input.addEventListener('blur', function () {
            setTimeout(() => {
                input.classList.remove('focused'); // Quitar el color del borde
                suggestionsList.classList.add('hidden'); // Ocultar la lista
            }, 200); // Retraso para permitir seleccionar un elemento de la lista
        });

        // Manejador de clics en los <li>
        suggestionsList.addEventListener('click', function (e) {
            if (e.target.tagName === 'LI') {
                input.value = e.target.textContent; // Poner el texto seleccionado en el input
                suggestionsList.classList.add('hidden'); // Ocultar la lista
            }
        });

        input.addEventListener('input', function () {
            const searchValue = input.value.toLowerCase();
            suggestionItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(searchValue)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });

    });

    // Filtros aplicados a la pagina de vacantes
    function applyFilters() {
        // Asegurarse de que los checkboxes existen antes de procesar
        const selectedUbicaciones = checkboxesUbicacion.length
            ? Array.from(checkboxesUbicacion)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value)
            : []; // Si no hay checkboxes, usar un array vacío

        const selectedTitles = checkboxesTitle.length
            ? Array.from(checkboxesTitle)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value)
            : []; // Si no hay checkboxes, usar un array vacío

        // Filtrar los elementos de la lista
        items.forEach(item => {
            const matchesUbicacion = selectedUbicaciones.length === 0 || selectedUbicaciones.includes(item.dataset.tienda);
            const matchesTitle = selectedTitles.length === 0 || selectedTitles.includes(item.dataset.title);

            // Mostrar el elemento si cumple ambos criterios
            if (matchesUbicacion && matchesTitle) {
                item.style.display = ''; // Mostrar
            } else {
                item.style.display = 'none'; // Ocultar
            }
        });
    }

    // Añadir listeners a ambos conjuntos de checkboxes
    if (checkboxesUbicacion.length > 0) {
        checkboxesUbicacion.forEach(checkbox => {
            checkbox.addEventListener('change', applyFilters);
        });
    }

    if (checkboxesTitle.length > 0) {
        checkboxesTitle.forEach(checkbox => {
            checkbox.addEventListener('change', applyFilters);
        });
    }

    function updateFavorites(id) {
        // Obtener el array de favoritos desde localStorage, o inicializar uno vacío si no existe
        let favorites = JSON.parse(localStorage.getItem('favorites')) || [];

        const index = favorites.indexOf(id);

        if (index === -1) {
            // Si no está, agregarlo al array
            favorites.push(id);
        } else {
            // Si ya está, eliminarlo del array
            favorites.splice(index, 1);
        }

        // Guardar el array actualizado en localStorage
        localStorage.setItem('favorites', JSON.stringify(favorites));
    }

    // Añadir el evento de clic a cada .fav
    document.querySelectorAll('.fav').forEach(function (favButton) {
        favButton.addEventListener('click', function () {
            // Obtener el id del li contenedor



            const listItem = favButton.closest('li');
            const itemId = listItem.getAttribute('data-id');

            // Llamar a la función para actualizar los favoritos
            updateFavorites(itemId);

            // Cambiar el color del ícono según si está o no en favoritos
            toggleFavIcon(favButton, itemId);

            // Agregar o quitar la clase 'active' en el .item
            toggleItemActiveState(listItem, itemId);
        });
    });


    // Funciones de la página de saved jobs
    const textElement = document.querySelector('#count-jobs'); // Contenedor de "Visualizando X de Y vacantes"
    // Actualizar el texto de "Visualizando X de Y vacantes"

    function updateVacantesCount() {
        const visibleCount = ulElement.querySelectorAll('li').length; // Elementos visibles
        const totalCount = JSON.parse(localStorage.getItem('favorites'))?.length || 0; // Total de favoritos
        textElement.textContent = `Visualizando ${visibleCount} de ${totalCount} vacantes`;
    }

    const ulElement = document.querySelector('#favorites-list'); // Contenedor principal

    if (ulElement) {
        // Event delegation: Escuchar clics en el contenedor principal
        ulElement.addEventListener('click', function (event) {
            // Verificar si el clic ocurrió en un botón .fav
            const favButton = event.target.closest('.fav');
            if (favButton) {
                // Obtener el elemento <li> contenedor
                const listItem = favButton.closest('li');
                const itemId = listItem.getAttribute('data-id');

                // Llamar a las funciones necesarias
                updateFavorites(itemId);
                toggleFavIcon(favButton, itemId);
                toggleItemActiveState(listItem, itemId);
                listItem.remove();

                updateVacantesCount();
            }
        });



    }

    // Función para cambiar el ícono de favorito
    function toggleFavIcon(favButton, id) {
        // Verificar si el id ya está en el array de favoritos
        let favorites = JSON.parse(localStorage.getItem('favorites')) || [];

        if (favorites.includes(id)) {
            // Si está en favoritos, agregar clase 'active' al ícono
            favButton.querySelector('svg').classList.add('active');
        } else {
            // Si no está, quitar la clase 'active'
            favButton.querySelector('svg').classList.remove('active');
        }
    }

    // Función para agregar o quitar la clase 'active' al contenedor .item
    function toggleItemActiveState(listItem, id) {
        let favorites = JSON.parse(localStorage.getItem('favorites')) || [];

        if (favorites.includes(id)) {
            // Si el id está en los favoritos, agregar la clase 'active' al .item
            listItem.classList.add('active');
        } else {
            // Si no está, eliminar la clase 'active'
            listItem.classList.remove('active');
        }
    }

    // Revisa el estado de favoritos al cargar la página
    document.querySelectorAll('.fav').forEach(function (favButton) {
        const listItem = favButton.closest('li');
        const itemId = listItem.getAttribute('data-id');

        toggleFavIcon(favButton, itemId);
        toggleItemActiveState(listItem, itemId);
    });

    // Marcar los elementos como 'active' si están en favoritos al cargar la página
    function markItemsAsActive() {
        let favorites = JSON.parse(localStorage.getItem('favorites')) || [];

        document.querySelectorAll('.item').forEach(function (listItem) {
            const itemId = listItem.getAttribute('data-id');
            if (favorites.includes(itemId)) {
                // Si el id está en favoritos, agregar la clase 'active' al .item
                listItem.classList.add('active');
                // También asegurarse de que el ícono de favorito esté activo
                const favButton = listItem.querySelector('.fav');
                toggleFavIcon(favButton, itemId);
            }
        });
    }

    // Ejecutar la función para marcar los ítems como 'active' al cargar la página
    markItemsAsActive();



    // Carga los primeros 5 saved jobs
    const existPageTemplate = !!document.querySelector('.page-template-saved-jobs');

    if (existPageTemplate) {
        const favorites = JSON.parse(localStorage.getItem('favorites')) || []; // Obtiene los favoritos de localStorage
        if (favorites.length > 0) {
            let currentIndex = 0; // Índice inicial para paginar

            const ulElement = document.querySelector('#favorites-list'); // Contenedor de los elementos
            const loadMoreButton = document.querySelector('#load-more'); // Botón "Cargar más"

            const loadFavorites = () => {
                if (currentIndex >= favorites.length) return; // No hay más favoritos que cargar

                const nextBatch = favorites.slice(currentIndex, currentIndex + 5);
                currentIndex += 5; // Aumenta el índice para la siguiente carga

                jQuery.ajax({
                    url: ajax_query_vars.ajax_url,
                    method: 'POST',
                    data: {
                        action: 'get_favorites',
                        favorites: JSON.stringify(nextBatch),
                    },
                    dataType: 'json', // Esperamos una respuesta en formato JSON
                    success: function (posts) {
                        posts.forEach(post => {
                            const li = document.createElement('li');
                            li.className = 'item';
                            li.setAttribute('data-id', post.id);
                            li.innerHTML = `
                            <div class="img">
                                <img src="${post.image}" alt="">
                            </div>
                            <div class="desc">
                                <a href="${post.permalink}">${post.title}</a>
                                <div class="icon-cont">
                                    <div class="img">${post.location_icon}</div>
                                    <div class="text">${post.location}</div>
                                </div>
                                <div class="icon-cont">
                                    <div class="img">${post.time_icon}</div>
                                    <div class="text">${post.time_text}</div>
                                </div>
                            </div>
                            <div class="fav">
                                <div class="img">${post.like_icon}</div>
                            </div>
                        `;
                            ulElement.appendChild(li);
                        });

                        // Si no hay más elementos, ocultamos el botón
                        if (currentIndex >= favorites.length) {
                            jQuery('#load-more').hide(); // Usando jQuery para ocultar el botón
                        }
                    },
                    error: function (error) {
                        console.error('Error al cargar favoritos:', error);
                    }
                });
            };

            // Cargar los primeros 5 elementos al cargar la página
            loadFavorites();

            // Verifica si el botón "Cargar más" existe antes de agregarle el evento
            if (loadMoreButton) {
                loadMoreButton.addEventListener('click', loadFavorites);
            }
        }
    }


    // Funciones de la rueda de valores
    const items_svg = document.querySelectorAll(".g-item");
    const rueda_desc = document.querySelectorAll(".rueda-desc");
    items_svg.forEach((item) => {
        item.addEventListener('click', function () {

            let selected_id = item.id;
            items_svg.forEach((el) => {
                el.classList.remove("active");
                el.classList.add("unactive");
            });

            rueda_desc.forEach((el) => el.style.display = "none");

            item.classList.remove('unactive');
            item.classList.add('active');

            let descToShow = document.querySelector(`.rueda-desc[data-id-item="${selected_id}"]`);
            if (descToShow) {
                descToShow.style.display = "block";
            }
        });
    });

    // Notificaciones
    // Ver mensaje:
    document.querySelectorAll('.plus').forEach(plusButton => {
        plusButton.addEventListener('click', function () {
            // Buscar el elemento más cercano con clase "item" (el LI padre)
            const listItem = this.closest('.item');
            // Encontrar el mensaje dentro de ese LI
            const mensaje = listItem.querySelector('.mensaje');
            // Alternar la clase en el mensaje
            mensaje.classList.toggle('visible');

            // Alternar la clase `rotated` en el botón plus
            this.classList.toggle('rotated');
        });
    });


});

