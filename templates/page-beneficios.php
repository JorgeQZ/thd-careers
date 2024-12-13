<?php
/*
Template Name: Beneficios
*/

get_header();
?>

<?php the_content();?>

<?php get_footer(); ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
  // Seleccionar todos los enlaces de los tabs
  const tabLinks = document.querySelectorAll(".wp-block-buttons.tab1 a, .wp-block-buttons.tab2 a, .wp-block-buttons.tab3 a");

  // Por defecto, activar tab1
  const defaultTab = document.querySelector(".wp-block-buttons.tab1");
  const defaultContent = document.querySelector(".contenido-tab.tab1-content");

  if (defaultTab && defaultContent) {
    defaultTab.classList.add("active");
    defaultContent.classList.add("active");
  }

  // Agregar evento a cada enlace
  tabLinks.forEach(tabLink => {
    tabLink.addEventListener("click", function (event) {
      event.preventDefault();

      // Remover 'active' de todos los botones y contenidos
      document.querySelectorAll(".wp-block-buttons").forEach(tab => {
        tab.classList.remove("active");
      });
      document.querySelectorAll(".contenido-tab").forEach(content => {
        content.classList.remove("active");
      });

      // Agregar 'active' al botón contenedor actual
      const parentTab = this.closest(".wp-block-buttons");
      parentTab.classList.add("active");

      // Agregar 'active' al contenido correspondiente
      const tabClass = parentTab.classList.contains("tab1")
        ? "tab1-content"
        : parentTab.classList.contains("tab2")
        ? "tab2-content"
        : "tab3-content";

      const tabContent = document.querySelector(`.contenido-tab.${tabClass}`);
      if (tabContent) {
        tabContent.classList.add("active");
      }
    });
  });
});
</script>