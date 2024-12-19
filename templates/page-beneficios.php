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
  // Script para las tabs del primer bloque
  const tabLinks1 = document.querySelectorAll(".wp-block-buttons.tab1 a, .wp-block-buttons.tab2 a, .wp-block-buttons.tab3 a");
  const allContents = document.querySelectorAll(".contenido-tab, .contenido-tab3");

  const activateTab1 = function (parentTab, tabClass) {
    allContents.forEach(content => content.classList.remove("active"));
    document.querySelectorAll(".wp-block-buttons").forEach(tab => tab.classList.remove("active"));

    if (parentTab) parentTab.classList.add("active");
    const tabContent = document.querySelector(`.contenido-tab.${tabClass}`);
    if (tabContent) tabContent.classList.add("active");
  };

  // Activar por defecto la primera tab
  const defaultTab1 = document.querySelector(".wp-block-buttons.tab1");
  activateTab1(defaultTab1, "tab1-content");

  tabLinks1.forEach(tabLink => {
    tabLink.addEventListener("click", function (event) {
      event.preventDefault();
      const parentTab = this.closest(".wp-block-buttons");
      const tabClass = parentTab.classList.contains("tab1")
        ? "tab1-content"
        : parentTab.classList.contains("tab2")
        ? "tab2-content"
        : "tab3-content";
      activateTab1(parentTab, tabClass);
    });
  });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  // Script para las tabs del segundo bloque
  const tabLinks2 = document.querySelectorAll(
    ".wp-block-buttons.tab1-tab3 a, .wp-block-buttons.tab2-tab3 a, .wp-block-buttons.tab3-tab3 a, .wp-block-buttons.tab4-tab3 a, .wp-block-buttons.tab5-tab3 a"
  );
  const allContents = document.querySelectorAll(".contenido-tab, .contenido-tab3");

  const activateTab2 = function (parentTab, tabClass) {
    allContents.forEach(content => content.classList.remove("active"));
    document.querySelectorAll(".wp-block-buttons").forEach(tab => tab.classList.remove("active"));

    if (parentTab) parentTab.classList.add("active");
    const tabContent = document.querySelector(`.contenido-tab3.${tabClass}`);
    if (tabContent) tabContent.classList.add("active");

    // Asegurar que el tercer tab del primer bloque esté activo
    const thirdTab1 = document.querySelector(".wp-block-buttons.tab3");
    const thirdContent1 = document.querySelector(".contenido-tab.tab3-content");
    if (thirdTab1 && thirdContent1) {
      thirdTab1.classList.add("active");
      thirdContent1.classList.add("active");
    }
  };

  // Activar por defecto la primera tab del segundo bloque
  const defaultTab2 = document.querySelector(".wp-block-buttons.tab1-tab3");
  activateTab2(defaultTab2, "tab1-content-tab3");

  tabLinks2.forEach(tabLink => {
    tabLink.addEventListener("click", function (event) {
      event.preventDefault();
      const parentTab = this.closest(".wp-block-buttons");
      const tabClass = parentTab.classList.contains("tab1-tab3")
        ? "tab1-content-tab3"
        : parentTab.classList.contains("tab2-tab3")
        ? "tab2-content-tab3"
        : parentTab.classList.contains("tab3-tab3")
        ? "tab3-content-tab3"
        : parentTab.classList.contains("tab4-tab3")
        ? "tab4-content-tab3"
        : "tab5-content-tab3";
      activateTab2(parentTab, tabClass);
    });
  });
});
</script>