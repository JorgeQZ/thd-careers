<?php
/*
Template Name: Beneficios
*/

get_header();
?>

<?php the_content();?>

<?php get_footer(); ?>

<script>
document.querySelectorAll('.botones-tabs').forEach((boton) => {
  boton.addEventListener('click', (event) => {
    event.preventDefault();
    const tabClass = boton.classList[1];
    document.querySelectorAll('.contenido-tab').forEach((div) => {
      div.style.display = 'none';
    });
    const divToShow = document.querySelector(`.contenido-tab.${tabClass}-content`);
    if (divToShow) {
      divToShow.style.display = 'block';
    }
  });
});
</script>