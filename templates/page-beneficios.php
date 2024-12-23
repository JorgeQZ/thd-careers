<?php

/*
Template Name: Beneficios
*/

get_header();
?>

<div class="contenedor-principal">
  <p class="p1-s1"> <?php echo get_field('titulo-beneficios'); ?> </p>
  <p class="p2-s1"> <?php echo get_field('descripcion-beneficios'); ?> </p>

  <div class="contenedor-s2">

    <?php
    $tabs_beneficios = get_field('tabs-beneficios');
    ?>

    <a class="tab-s2">
      <p class="p1-s2"> <?php echo wp_kses_post($tabs_beneficios['texto-tab1-beneficios']); ?> </p>
    </a>

    <a class="tab-s2">
      <p class="p1-s2"> <?php echo wp_kses_post($tabs_beneficios['texto-tab2-beneficios']); ?> </p>
    </a>

    <a class="tab-s2">
      <p class="p1-s2"> <?php echo wp_kses_post($tabs_beneficios['texto-tab3-beneficios']); ?> </p>
    </a>

  </div>
</div>

<?php get_footer(); ?>