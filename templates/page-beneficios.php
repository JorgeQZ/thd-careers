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
      <p class="p1-s2"> <?php echo wp_kses_post($tabs_beneficios['tab1-beneficios']['texto-tab1-beneficios']); ?> </p>
      <img src="<?php echo get_template_directory_uri(); ?>/imgs/mas.png" class="plus" alt="icono-mas">
      <img src="<?php echo get_template_directory_uri(); ?>/imgs/pico.png" class="pico" alt="pico">
    </a>

    <a class="tab-s2">
      <p class="p1-s2"> <?php echo wp_kses_post($tabs_beneficios['tab2-beneficios']['texto-tab2-beneficios']); ?> </p>
      <img src="<?php echo get_template_directory_uri(); ?>/imgs/mas.png" class="plus" alt="icono-mas">
      <img src="<?php echo get_template_directory_uri(); ?>/imgs/pico.png" class="pico" alt="pico">
    </a>

    <a class="tab-s2">
      <p class="p1-s2"> <?php echo wp_kses_post($tabs_beneficios['tab3-beneficios']['texto-tab3-beneficios']); ?> </p>
      <img src="<?php echo get_template_directory_uri(); ?>/imgs/mas.png" class="plus" alt="icono-mas">
      <img src="<?php echo get_template_directory_uri(); ?>/imgs/pico.png" class="pico" alt="pico">
    </a>

  </div>

  <div class="contenedor-s3">

    <div class="contenido-tabs-s3">
      <p>Tab 1</p>
    </div>

    <div class="contenido-tabs-s3">
      <p>Tab 2</p>
    </div>

    <div class="contenido-tabs-s3">
      <p class="titulo-tab3"> <?php echo wp_kses_post($tabs_beneficios['tab3-beneficios']['titulo-beneficios-tab3']); ?> </p>
      <p class="subtitulo-tab3"> <?php echo wp_kses_post($tabs_beneficios['tab3-beneficios']['subtitulo-beneficios-tab3']); ?> </p>

      <?php if (have_rows('tabs-beneficios')) : ?>
      <?php while (have_rows('tabs-beneficios')) : the_row(); ?>
      <?php if (have_rows('tab3-beneficios')) : ?>
      <?php while (have_rows('tab3-beneficios')) : the_row(); ?>
      <?php if (have_rows('contenido-beneficios-tab3')) : ?>
      <?php while (have_rows('contenido-beneficios-tab3')) : the_row(); ?>
      <?php if (have_rows('bloque-beneficios-tab3')) : ?>

      <?php while (have_rows('bloque-beneficios-tab3')) : the_row();
        $imagen = get_sub_field('imagen-beneficios-tab3');
        $tamano_imagen = get_sub_field('tamano-imagen-beneficios-tab3');
        $titulo_contenido = get_sub_field('titulo-contenido-beneficios-tab3');
        $descripcion = get_sub_field('descripcion-beneficios-tab3');
      ?>

      <div class="contenedor1-tab3">
        <div class="contenedor-img-tab3">
          <?php if ($imagen) : ?>
              <img class="img-tab3" src="<?php echo esc_url($imagen['url']); ?>" alt="<?php echo esc_attr($imagen['alt']); ?>" style="width: <?php echo esc_attr($tamano_imagen); ?>px;">
          <?php endif; ?>
        </div>
        <div class="contenedor2-tab3">
            <p class="p1-tab3"> <?php echo wp_kses_post($titulo_contenido); ?> </p>
            <p class="p2-tab3"> <?php echo nl2br(esc_html($descripcion)); ?> </p>
        </div>
      </div>

      <?php endwhile; ?>
      <?php endif; ?>
      <?php endwhile; ?>
      <?php endif; ?>
      <?php endwhile; ?>
      <?php endif; ?>
      <?php endwhile; ?>
      <?php endif; ?>

    </div>

  </div>

</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Seleccionar los tabs y los contenidos
    const tabs = document.querySelectorAll(".tab-s2");
    const contents = document.querySelectorAll(".contenido-tabs-s3");

    // Mostrar el contenido del Tab 1 por defecto y marcarlo como activo
    contents.forEach((content, index) => {
      content.style.display = index === 0 ? "block" : "none";
    });
    tabs[0].classList.add("active");

    // Añadir eventos de clic a los tabs
    tabs.forEach((tab, index) => {
      tab.addEventListener("click", function (e) {
        e.preventDefault();

        // Remover la clase activa de todos los tabs
        tabs.forEach(t => t.classList.remove("active"));

        // Añadir la clase activa al tab actual
        tab.classList.add("active");

        // Ocultar todos los contenidos
        contents.forEach(content => {
          content.style.display = "none";
        });

        // Mostrar el contenido correspondiente
        contents[index].style.display = "block";
      });
    });
  });
</script>

<?php get_footer(); ?>