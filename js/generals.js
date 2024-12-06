document.addEventListener('DOMContentLoaded', function () {
    const inputSearches = document.querySelectorAll('.input-search');

    inputSearches.forEach((searchContainer) => {
        const input = searchContainer.querySelector('.search-input');
        const suggestionsList = searchContainer.querySelector('.suggestions-list');

        searchContainer.addEventListener('click', function () {
            searchContainer.classList.add('active');
            suggestionsList.classList.remove('hidden'); // Mostrar la lista

        });
        // Al hacer clic en el input
        // input.addEventListener('focus', function () {
        //     input.classList.add('focused'); // Añadir la clase para colorear el borde
        //     suggestionsList.classList.remove('hidden'); // Mostrar la lista

        // });

        // Al hacer clic fuera del input
        input.addEventListener('blur', function () {
            setTimeout(() => {
                input.classList.remove('focused'); // Quitar el color del borde
                suggestionsList.classList.add('hidden'); // Ocultar la lista
            }, 200); // Retraso para permitir seleccionar un elemento de la lista
        });

        // Manejador de clics en los <li>
        suggestionsList.addEventListener('click', function (e) {
            console.log('click');

            if (e.target.tagName === 'LI') {
                input.value = e.target.textContent; // Poner el texto seleccionado en el input
                suggestionsList.classList.add('hidden'); // Ocultar la lista
            }
        });

    });

    const checkboxes = document.querySelectorAll('input[name="ubicacion[]"]');
    const items = document.querySelectorAll('ul.list .item');

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const selectedValues = Array.from(checkboxes)
                .filter(cb => cb.checked) // Obtener los checkboxes seleccionados
                .map(cb => cb.value);     // Extraer sus valores

            console.log(selectedValues);

            // // Mostrar/ocultar elementos según los valores seleccionados
            items.forEach(item => {
                if (selectedValues.length === 0 || selectedValues.includes(item.dataset.id)) {
                    item.style.display = ''; // Mostrar si coincide o no hay filtros
                } else {
                    item.style.display = 'none';  // Ocultar si no coincide
                }
            });
        });
    });


    const checkboxes_titles = document.querySelectorAll('input[name="title[]"]');

    checkboxes_titles.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const selectedValues = Array.from(checkboxes_titles)
                .filter(cb => cb.checked) // Obtener los checkboxes seleccionados
                .map(cb => cb.value);     // Extraer sus valores

            console.log(selectedValues);

            // // Mostrar/ocultar elementos según los valores seleccionados
            items.forEach(item => {
                if (selectedValues.length === 0 || selectedValues.includes(item.dataset.title)) {
                    item.style.display = ''; // Mostrar si coincide o no hay filtros
                } else {
                    item.style.display = 'none';  // Ocultar si no coincide
                }
            });
        });
    });
});
