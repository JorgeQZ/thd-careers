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


    /**
     * Se agrega logout el sub menu:
     */
    var isLoggedIn = document.body.classList.contains('logged-in');
    if (isLoggedIn) {
        // Selecciona el menú "Mi perfil" (ajusta la clase según tu estructura de menú)
        var perfilMenuItem = document.querySelector('.menu-item-mi-perfil');


        // Asegúrate de que el perfil del menú tenga un submenú
        var subMenu = perfilMenuItem ? perfilMenuItem.querySelector('.sub-menu') : null;

        if (subMenu) {
            // Crea el elemento <li> para el botón de "Cerrar sesión"
            var logoutItem = document.createElement('li');
            logoutItem.classList.add('menu-item', 'logout');

            // Crea el enlace de "Cerrar sesión"
            var logoutLink = document.createElement('a');

            logoutLink.href = ajax_query_vars.logoutUrl;  // Utiliza la URL de cierre de sesión de PHP
            logoutLink.textContent = 'Cerrar sesión';

            // Agrega el enlace dentro del <li>
            logoutItem.appendChild(logoutLink);

            // Agrega el nuevo <li> al submenú
            subMenu.appendChild(logoutItem);
        }
    }else{
        var perfilMenuItem = document.querySelector('.menu-item-mi-perfil');
        var subMenu = perfilMenuItem ? perfilMenuItem.querySelector('.sub-menu') : null;

        perfilMenuItem.classList.remove('menu-item-has-children');
        subMenu.remove();
    }
});

