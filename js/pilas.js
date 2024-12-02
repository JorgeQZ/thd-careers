document.querySelectorAll('.botones-tabs').forEach((boton) => {
  boton.addEventListener('click', (event) => {
    event.preventDefault(); // Evita que el enlace recargue la página

    // Obtener la clase específica del botón (e.g., 'tab1', 'tab2', etc.)
    const tabClass = boton.classList[1];

    // Ocultar todos los divs
    document.querySelectorAll('.contenido-tab').forEach((div) => {
      div.style.display = 'none !important';
    });

    // Mostrar el div correspondiente
    const divToShow = document.querySelector(`.contenido-tab.${tabClass}-content`);
    if (divToShow) {
      divToShow.style.display = 'block !important';
    }
  });
});