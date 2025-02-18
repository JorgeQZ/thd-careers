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

    <div class="contenedor-s3-movil">
      <div class="contenedor-s3">
        <div class="contenido-tabs-s3-movil">
          <p class="p1-tab1"> <?php echo wp_kses_post($tabs_beneficios['tab1-beneficios']['titulo-beneficios-tab1']); ?> </p>

          <p class="p2-tab1">
            <?php echo wp_kses_post($tabs_beneficios['tab1-beneficios']['descripcion-beneficios-tab1']); ?>
          </p>

          <div class="contenedor-tabs-tab1">
            <a href="" class="tabs-tab1movil">
              <p class="p3-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab1']['titulotab-beneficios-tab1-tab1']); ?> </p>
              <img src="<?php echo get_template_directory_uri(); ?>/imgs/mas.png" class="plus-tab1" alt="icono-mas">
              <img src="<?php echo get_template_directory_uri(); ?>/imgs/pico.png" class="pico-tab1" alt="pico">
            </a>

            <div class="contenedor-tab1movil">
              <div class="contenedor-contenido-tabs-tab1-movil">
                <p class="titulo-tab1-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab1']['titulo-beneficios-tab1-tab1']); ?> </p>
                <p class="descripcion-tab1-tab1"> <?php echo nl2br(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab1']['descripcion-beneficios-tab1-tab1']); ?> </p>

                <div class="contenido-tab1-tab1">
                  <?php
                  if (have_rows('tabs-beneficios')) :
                      while (have_rows('tabs-beneficios')) : the_row();
                          if (have_rows('tab1-beneficios')) :
                              while (have_rows('tab1-beneficios')) : the_row();
                                  if (have_rows('grupo-1-beneficios-tab1')) :
                                      while (have_rows('grupo-1-beneficios-tab1')) : the_row();
                                          if (have_rows('grupo-1-beneficios-tab1-tab1')) :
                                              while (have_rows('grupo-1-beneficios-tab1-tab1')) : the_row();
                                                  if (have_rows('repetidor-beneficios-tab1-tab1')) :
                                                      while (have_rows('repetidor-beneficios-tab1-tab1')) : the_row();
                                                          $titulo = get_sub_field('grupo-2-beneficios-tab1-tab1')['titulo-contenedor-beneficios-tab1-tab1'];
                                                          $descripcion = get_sub_field('grupo-2-beneficios-tab1-tab1')['descripcion-contenedor-beneficios-tab1-tab1'];
                                                          ?>
                                                          <div class="subcontenido-tab1-tab1">
                                                              <p class="titulo-contenido-tab1-tab1"><?php echo esc_html($titulo); ?></p>
                                                              <p class="descripcion-contenido-tab1-tab1"><?php echo esc_html($descripcion); ?></p>
                                                          </div>
                                                          <?php
                                                      endwhile;
                                                  endif;
                                              endwhile;
                                          endif;
                                      endwhile;
                                  endif;
                              endwhile;
                          endif;
                      endwhile;
                  endif;
                  ?>
                </div>

              </div>
            </div>

            <a href="" class="tabs-tab1movil">
              <p class="p3-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab2']['titulotab-beneficios-tab1-tab2']); ?> </p>
              <img src="<?php echo get_template_directory_uri(); ?>/imgs/mas.png" class="plus-tab1" alt="icono-mas">
              <img src="<?php echo get_template_directory_uri(); ?>/imgs/pico.png" class="pico-tab1" alt="pico">
            </a>

            <div class="contenedor-tab1movil">
              <div class="contenedor-contenido-tabs-tab1-movil">
                <p class="titulo-tab1-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab2']['titulo-beneficios-tab1-tab2']); ?> </p>
                <p class="descripcion-tab1-tab1"> <?php echo nl2br(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab2']['descripcion-beneficios-tab1-tab2']); ?> </p>

                <div class="contenido-tab1-tab1">
                  <?php
                  if (have_rows('tabs-beneficios')) :
                      while (have_rows('tabs-beneficios')) : the_row();
                          if (have_rows('tab1-beneficios')) :
                              while (have_rows('tab1-beneficios')) : the_row();
                                  if (have_rows('grupo-1-beneficios-tab1')) :
                                      while (have_rows('grupo-1-beneficios-tab1')) : the_row();
                                          if (have_rows('grupo-1-beneficios-tab1-tab2')) :
                                              while (have_rows('grupo-1-beneficios-tab1-tab2')) : the_row();
                                                  if (have_rows('repetidor-beneficios-tab1-tab2')) :
                                                      while (have_rows('repetidor-beneficios-tab1-tab2')) : the_row();
                                                          $titulo = get_sub_field('grupo-2-beneficios-tab1-tab2')['titulo-contenedor-beneficios-tab1-tab2'];
                                                          $descripcion = get_sub_field('grupo-2-beneficios-tab1-tab2')['descripcion-contenedor-beneficios-tab1-tab2'];
                                                          ?>
                                                          <div class="subcontenido-tab1-tab1">
                                                              <p class="titulo-contenido-tab1-tab1"><?php echo esc_html($titulo); ?></p>
                                                              <p class="descripcion-contenido-tab1-tab1"><?php echo esc_html($descripcion); ?></p>
                                                          </div>
                                                          <?php
                                                      endwhile;
                                                  endif;
                                              endwhile;
                                          endif;
                                      endwhile;
                                  endif;
                              endwhile;
                          endif;
                      endwhile;
                  endif;
                  ?>
                </div>
              </div>
            </div>

            <a href="" class="tabs-tab1movil">
              <p class="p3-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab3']['titulotab-beneficios-tab1-tab3']); ?> </p>
              <img src="<?php echo get_template_directory_uri(); ?>/imgs/mas.png" class="plus-tab1" alt="icono-mas">
              <img src="<?php echo get_template_directory_uri(); ?>/imgs/pico.png" class="pico-tab1" alt="pico">
            </a>

            <div class="contenedor-tab1movil">
              <div class="contenedor-contenido-tabs-tab1-movil">
                <p class="titulo-tab1-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab3']['titulo-beneficios-tab1-tab3']); ?> </p>
                <p class="descripcion-tab1-tab1"> <?php echo nl2br(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab3']['descripcion-beneficios-tab1-tab3']); ?> </p>

                <div class="contenido-tab1-tab1">
                  <?php
                  if (have_rows('tabs-beneficios')) :
                      while (have_rows('tabs-beneficios')) : the_row();
                          if (have_rows('tab1-beneficios')) :
                              while (have_rows('tab1-beneficios')) : the_row();
                                  if (have_rows('grupo-1-beneficios-tab1')) :
                                      while (have_rows('grupo-1-beneficios-tab1')) : the_row();
                                          if (have_rows('grupo-1-beneficios-tab1-tab3')) :
                                              while (have_rows('grupo-1-beneficios-tab1-tab3')) : the_row();
                                                  if (have_rows('repetidor-beneficios-tab1-tab3')) :
                                                      while (have_rows('repetidor-beneficios-tab1-tab3')) : the_row();
                                                          $titulo = get_sub_field('grupo-2-beneficios-tab1-tab3')['titulo-contenedor-beneficios-tab1-tab3'];
                                                          $descripcion = get_sub_field('grupo-2-beneficios-tab1-tab3')['descripcion-contenedor-beneficios-tab1-tab3'];
                                                          ?>
                                                          <div class="subcontenido-tab1-tab1">
                                                              <p class="titulo-contenido-tab1-tab1"><?php echo esc_html($titulo); ?></p>
                                                              <p class="descripcion-contenido-tab1-tab1"><?php echo esc_html($descripcion); ?></p>
                                                          </div>
                                                          <?php
                                                      endwhile;
                                                  endif;
                                              endwhile;
                                          endif;
                                      endwhile;
                                  endif;
                              endwhile;
                          endif;
                      endwhile;
                  endif;
                  ?>
                </div>
              </div>
            </div>

          </div>

          <!-- <div class="contenedor-s3-desktop">

            <div class="contenedor-contenido-tabs-tab1">
              <p class="titulo-tab1-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab1']['titulo-beneficios-tab1-tab1']); ?> </p>
              <p class="descripcion-tab1-tab1"> <?php echo nl2br(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab1']['descripcion-beneficios-tab1-tab1']); ?> </p>

              <div class="contenido-tab1-tab1">
                <?php
                if (have_rows('tabs-beneficios')) :
                    while (have_rows('tabs-beneficios')) : the_row();
                        if (have_rows('tab1-beneficios')) :
                            while (have_rows('tab1-beneficios')) : the_row();
                                if (have_rows('grupo-1-beneficios-tab1')) :
                                    while (have_rows('grupo-1-beneficios-tab1')) : the_row();
                                        if (have_rows('grupo-1-beneficios-tab1-tab1')) :
                                            while (have_rows('grupo-1-beneficios-tab1-tab1')) : the_row();
                                                if (have_rows('repetidor-beneficios-tab1-tab1')) :
                                                    while (have_rows('repetidor-beneficios-tab1-tab1')) : the_row();
                                                        $titulo = get_sub_field('grupo-2-beneficios-tab1-tab1')['titulo-contenedor-beneficios-tab1-tab1'];
                                                        $descripcion = get_sub_field('grupo-2-beneficios-tab1-tab1')['descripcion-contenedor-beneficios-tab1-tab1'];
                                                        ?>
                                                        <div class="subcontenido-tab1-tab1">
                                                            <p class="titulo-contenido-tab1-tab1"><?php echo esc_html($titulo); ?></p>
                                                            <p class="descripcion-contenido-tab1-tab1"><?php echo esc_html($descripcion); ?></p>
                                                        </div>
                                                        <?php
                                                    endwhile;
                                                endif;
                                            endwhile;
                                        endif;
                                    endwhile;
                                endif;
                            endwhile;
                        endif;
                    endwhile;
                endif;
                ?>
              </div>

            </div>

            <div class="contenedor-contenido-tabs-tab1">
              <p class="titulo-tab1-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab2']['titulo-beneficios-tab1-tab2']); ?> </p>
              <p class="descripcion-tab1-tab1"> <?php echo nl2br(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab2']['descripcion-beneficios-tab1-tab2']); ?> </p>

              <div class="contenido-tab1-tab1">
                <?php
                if (have_rows('tabs-beneficios')) :
                    while (have_rows('tabs-beneficios')) : the_row();
                        if (have_rows('tab1-beneficios')) :
                            while (have_rows('tab1-beneficios')) : the_row();
                                if (have_rows('grupo-1-beneficios-tab1')) :
                                    while (have_rows('grupo-1-beneficios-tab1')) : the_row();
                                        if (have_rows('grupo-1-beneficios-tab1-tab2')) :
                                            while (have_rows('grupo-1-beneficios-tab1-tab2')) : the_row();
                                                if (have_rows('repetidor-beneficios-tab1-tab2')) :
                                                    while (have_rows('repetidor-beneficios-tab1-tab2')) : the_row();
                                                        $titulo = get_sub_field('grupo-2-beneficios-tab1-tab2')['titulo-contenedor-beneficios-tab1-tab2'];
                                                        $descripcion = get_sub_field('grupo-2-beneficios-tab1-tab2')['descripcion-contenedor-beneficios-tab1-tab2'];
                                                        ?>
                                                        <div class="subcontenido-tab1-tab1">
                                                            <p class="titulo-contenido-tab1-tab1"><?php echo esc_html($titulo); ?></p>
                                                            <p class="descripcion-contenido-tab1-tab1"><?php echo esc_html($descripcion); ?></p>
                                                        </div>
                                                        <?php
                                                    endwhile;
                                                endif;
                                            endwhile;
                                        endif;
                                    endwhile;
                                endif;
                            endwhile;
                        endif;
                    endwhile;
                endif;
                ?>
              </div>
            </div>

            <div class="contenedor-contenido-tabs-tab1">
              <p class="titulo-tab1-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab3']['titulo-beneficios-tab1-tab3']); ?> </p>
              <p class="descripcion-tab1-tab1"> <?php echo nl2br(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab3']['descripcion-beneficios-tab1-tab3']); ?> </p>

              <div class="contenido-tab1-tab1">
                <?php
                if (have_rows('tabs-beneficios')) :
                    while (have_rows('tabs-beneficios')) : the_row();
                        if (have_rows('tab1-beneficios')) :
                            while (have_rows('tab1-beneficios')) : the_row();
                                if (have_rows('grupo-1-beneficios-tab1')) :
                                    while (have_rows('grupo-1-beneficios-tab1')) : the_row();
                                        if (have_rows('grupo-1-beneficios-tab1-tab3')) :
                                            while (have_rows('grupo-1-beneficios-tab1-tab3')) : the_row();
                                                if (have_rows('repetidor-beneficios-tab1-tab3')) :
                                                    while (have_rows('repetidor-beneficios-tab1-tab3')) : the_row();
                                                        $titulo = get_sub_field('grupo-2-beneficios-tab1-tab3')['titulo-contenedor-beneficios-tab1-tab3'];
                                                        $descripcion = get_sub_field('grupo-2-beneficios-tab1-tab3')['descripcion-contenedor-beneficios-tab1-tab3'];
                                                        ?>
                                                        <div class="subcontenido-tab1-tab1">
                                                            <p class="titulo-contenido-tab1-tab1"><?php echo esc_html($titulo); ?></p>
                                                            <p class="descripcion-contenido-tab1-tab1"><?php echo esc_html($descripcion); ?></p>
                                                        </div>
                                                        <?php
                                                    endwhile;
                                                endif;
                                            endwhile;
                                        endif;
                                    endwhile;
                                endif;
                            endwhile;
                        endif;
                    endwhile;
                endif;
                ?>
              </div>
            </div>

          </div> -->
        </div>
      </div>
    </div>

    <a class="tab-s2">
      <p class="p1-s2"> <?php echo wp_kses_post($tabs_beneficios['tab2-beneficios']['texto-tab2-beneficios']); ?> </p>
      <img src="<?php echo get_template_directory_uri(); ?>/imgs/mas.png" class="plus" alt="icono-mas">
      <img src="<?php echo get_template_directory_uri(); ?>/imgs/pico.png" class="pico" alt="pico">
    </a>

    <div class="contenedor-s3-movil">
      <div class="contenedor-s3">
        <div class="contenido-tabs-s3-movil">

          <p class="titulo-tab2"> <?php echo wp_kses_post($tabs_beneficios['tab2-beneficios']['titulo-beneficios-tab2']); ?> </p>

          <?php if (have_rows('tabs-beneficios')): ?>
          <div class="subcontenedor-tab2">
              <?php
              while (have_rows('tabs-beneficios')): the_row();
                  if (have_rows('tab2-beneficios')):
                      while (have_rows('tab2-beneficios')): the_row();

                          if (have_rows('repetidor1-beneficios-tab2')):
                              while (have_rows('repetidor1-beneficios-tab2')): the_row();

                                  if (have_rows('grupo1-beneficios-tabs2')):
                                      while (have_rows('grupo1-beneficios-tabs2')): the_row();

                                          // Crear el enlace de la tab
                                          $titulo_subtab = get_sub_field('titulo-de-subtab-beneficios-tab2');
                                          ?>
                                          <a class="tab-tab2-movil">
                                              <p class="p1-tab2"><?php echo wp_kses_post($titulo_subtab); ?></p>
                                              <img src="<?php echo get_template_directory_uri(); ?>/imgs/mas.png" class="plus-tab2" alt="icono-mas">
                                              <img src="<?php echo get_template_directory_uri(); ?>/imgs/pico.png" class="pico-tab2" alt="pico">
                                          </a>
                                          <?php

                                      endwhile;
                                  endif;

                              endwhile;
                          endif;

                      endwhile;
                  endif;
              endwhile;
              ?>
          </div>

          <?php
          // Crear subcontenedores de los rectángulos
          while (have_rows('tabs-beneficios')): the_row();
              if (have_rows('tab2-beneficios')):
                  while (have_rows('tab2-beneficios')): the_row();

                      if (have_rows('repetidor1-beneficios-tab2')):
                          while (have_rows('repetidor1-beneficios-tab2')): the_row();

                              if (have_rows('grupo1-beneficios-tabs2')):
                                  while (have_rows('grupo1-beneficios-tabs2')): the_row();

                                      // Crear un nuevo subcontenido-tab2 por cada grupo1-beneficios-tabs2
                                      ?>
                                      <div class="subcontenido-tab2-movil">
                                          <?php
                                          if (have_rows('rectangulos-subtab-beneficios-tab2')):
                                              while (have_rows('rectangulos-subtab-beneficios-tab2')): the_row();

                                                  if (have_rows('grupo2-beneficios-tab2')):
                                                      while (have_rows('grupo2-beneficios-tab2')): the_row();

                                                          $icono_rectangulo = get_sub_field('icono-rectangulo-beneficios-tab2');
                                                          $icono_hover_rectangulo = get_sub_field('icono-hover-rectangulo-beneficios-tab2');
                                                          $tam_icono_hover_rectangulo_beneficios_tab2 = get_sub_field('tam-icono-hover-rectangulo-beneficios-tab2');
                                                          $titulo_rectangulo = get_sub_field('titulo-rectangulo-beneficios-tab2');
                                                          $descripcion_rectangulo = get_sub_field('descripcion-rectangulo-beneficios-tab2');
                                                          $tam_icono_rectangulo_beneficios_tab2 = get_sub_field('tam-icono-rectangulo-beneficios-tab2');
                                                          ?>
                                                          <div class="rectangulo-tab2">
                                                              <?php if ($icono_rectangulo): ?>
                                                                  <img src="<?php echo esc_url($icono_rectangulo['url']); ?>" alt="<?php echo esc_attr($icono_rectangulo['alt']); ?>" class="img-rectangulo-tab2" style="width: <?php echo esc_attr($tam_icono_rectangulo_beneficios_tab2 ?: '40px'); ?>;">
                                                              <?php endif; ?>
                                                              <p class="titulo-rectangulo-tab2"><?php echo esc_html($titulo_rectangulo); ?></p>
                                                              <p class="descripcion-rectangulo-tab2"><?php echo esc_html($descripcion_rectangulo); ?></p>
                                                              <?php if ($icono_hover_rectangulo): ?>
                                                              <img src="<?php echo esc_url($icono_hover_rectangulo['url']); ?>" alt="<?php echo esc_attr($icono_hover_rectangulo['alt']); ?>" class="img-hover-rectangulo-tab2" style="width: <?php echo esc_attr($tam_icono_hover_rectangulo_beneficios_tab2 ?: '75px'); ?>;">
                                                              <?php endif; ?>
                                                          </div>
                                                          <?php

                                                      endwhile;
                                                  endif;

                                              endwhile;
                                          endif;
                                          ?>
                                      </div>
                                      <?php

                                  endwhile;
                              endif;

                          endwhile;
                      endif;

                  endwhile;
              endif;
          endwhile;
          ?>
          <?php endif; ?>


        </div>
      </div>
    </div>

    <a class="tab-s2">
      <p class="p1-s2"> <?php echo wp_kses_post($tabs_beneficios['tab3-beneficios']['texto-tab3-beneficios']); ?> </p>
      <img src="<?php echo get_template_directory_uri(); ?>/imgs/mas.png" class="plus" alt="icono-mas">
      <img src="<?php echo get_template_directory_uri(); ?>/imgs/pico.png" class="pico" alt="pico">
    </a>

    <div class="contenedor-s3-movil">
      <div class="contenedor-s3">
        <div class="contenido-tabs-s3-movil">
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

  </div>

  <div class="contenedor-s3-desktop">
    <div class="contenedor-s3">

      <div class="contenido-tabs-s3">
        <p class="p1-tab1"> <?php echo wp_kses_post($tabs_beneficios['tab1-beneficios']['titulo-beneficios-tab1']); ?> </p>

        <p class="p2-tab1">
          <?php echo wp_kses_post($tabs_beneficios['tab1-beneficios']['descripcion-beneficios-tab1']); ?>
        </p>

        <div class="contenedor-tabs-tab1">
          <a href="" class="tabs-tab1">
            <p class="p3-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab1']['titulotab-beneficios-tab1-tab1']); ?> </p>
            <img src="<?php echo get_template_directory_uri(); ?>/imgs/mas.png" class="plus-tab1" alt="icono-mas">
            <img src="<?php echo get_template_directory_uri(); ?>/imgs/pico.png" class="pico-tab1" alt="pico">
          </a>

          <a href="" class="tabs-tab1">
            <p class="p3-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab2']['titulotab-beneficios-tab1-tab2']); ?> </p>
            <img src="<?php echo get_template_directory_uri(); ?>/imgs/mas.png" class="plus-tab1" alt="icono-mas">
            <img src="<?php echo get_template_directory_uri(); ?>/imgs/pico.png" class="pico-tab1" alt="pico">
          </a>

          <a href="" class="tabs-tab1">
            <p class="p3-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab3']['titulotab-beneficios-tab1-tab3']); ?> </p>
            <img src="<?php echo get_template_directory_uri(); ?>/imgs/mas.png" class="plus-tab1" alt="icono-mas">
            <img src="<?php echo get_template_directory_uri(); ?>/imgs/pico.png" class="pico-tab1" alt="pico">
          </a>
        </div>

        <div class="contenedor-contenido-tabs-tab1">
          <p class="titulo-tab1-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab1']['titulo-beneficios-tab1-tab1']); ?> </p>
          <p class="descripcion-tab1-tab1"> <?php echo nl2br(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab1']['descripcion-beneficios-tab1-tab1']); ?> </p>

          <div class="contenido-tab1-tab1">
            <?php
            if (have_rows('tabs-beneficios')) :
                while (have_rows('tabs-beneficios')) : the_row();
                    if (have_rows('tab1-beneficios')) :
                        while (have_rows('tab1-beneficios')) : the_row();
                            if (have_rows('grupo-1-beneficios-tab1')) :
                                while (have_rows('grupo-1-beneficios-tab1')) : the_row();
                                    if (have_rows('grupo-1-beneficios-tab1-tab1')) :
                                        while (have_rows('grupo-1-beneficios-tab1-tab1')) : the_row();
                                            if (have_rows('repetidor-beneficios-tab1-tab1')) :
                                                while (have_rows('repetidor-beneficios-tab1-tab1')) : the_row();
                                                    $titulo = get_sub_field('grupo-2-beneficios-tab1-tab1')['titulo-contenedor-beneficios-tab1-tab1'];
                                                    $descripcion = get_sub_field('grupo-2-beneficios-tab1-tab1')['descripcion-contenedor-beneficios-tab1-tab1'];
                                                    $icono = get_sub_field('grupo-2-beneficios-tab1-tab1')['icono-contenedor-beneficios-tab1-tab1'];
                                                    $tam_icono = get_sub_field('grupo-2-beneficios-tab1-tab1')['tam-icono-contenedor-beneficios-tab1-tab1'];
                                                    $icono_hover = get_sub_field('grupo-2-beneficios-tab1-tab1')['icono-hover-contenedor-beneficios-tab1-tab1'];
                                                    $tam_icono_hover = get_sub_field('grupo-2-beneficios-tab1-tab1')['tam-icono-hover-contenedor-beneficios-tab1-tab1'];
                                                    ?>
                                                    <div class="subcontenido-tab1-tab1">
                                                        <?php if ($icono): ?>
                                                          <img src="<?php echo esc_url($icono['url']); ?>" alt="<?php echo esc_attr($icono['alt']); ?>" class="icono-contenido-tab1-tab1" style="width: <?php echo esc_attr($tam_icono ?: '40px'); ?>;">
                                                        <?php endif; ?>
                                                        <p class="titulo-contenido-tab1-tab1"><?php echo esc_html($titulo); ?></p>
                                                        <p class="descripcion-contenido-tab1-tab1"><?php echo esc_html($descripcion); ?></p>
                                                        <?php if ($icono_hover): ?>
                                                          <img src="<?php echo esc_url($icono_hover['url']); ?>" alt="<?php echo esc_attr($icono_hover['alt']); ?>" class="icono-hover-contenido-tab1-tab1" style="width: <?php echo esc_attr($tam_icono_hover ?: '80px'); ?>;">
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php
                                                endwhile;
                                            endif;
                                        endwhile;
                                    endif;
                                endwhile;
                            endif;
                        endwhile;
                    endif;
                endwhile;
            endif;
            ?>
          </div>

        </div>

        <div class="contenedor-contenido-tabs-tab1">
          <p class="titulo-tab1-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab2']['titulo-beneficios-tab1-tab2']); ?> </p>
          <p class="descripcion-tab1-tab1"> <?php echo nl2br(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab2']['descripcion-beneficios-tab1-tab2']); ?> </p>

          <div class="contenido-tab1-tab1">
            <?php
            if (have_rows('tabs-beneficios')) :
                while (have_rows('tabs-beneficios')) : the_row();
                    if (have_rows('tab1-beneficios')) :
                        while (have_rows('tab1-beneficios')) : the_row();
                            if (have_rows('grupo-1-beneficios-tab1')) :
                                while (have_rows('grupo-1-beneficios-tab1')) : the_row();
                                    if (have_rows('grupo-1-beneficios-tab1-tab2')) :
                                        while (have_rows('grupo-1-beneficios-tab1-tab2')) : the_row();
                                            if (have_rows('repetidor-beneficios-tab1-tab2')) :
                                                while (have_rows('repetidor-beneficios-tab1-tab2')) : the_row();
                                                    $titulo = get_sub_field('grupo-2-beneficios-tab1-tab2')['titulo-contenedor-beneficios-tab1-tab2'];
                                                    $descripcion = get_sub_field('grupo-2-beneficios-tab1-tab2')['descripcion-contenedor-beneficios-tab1-tab2'];
                                                    $icono = get_sub_field('grupo-2-beneficios-tab1-tab2')['icono-contenedor-beneficios-tab1-tab2'];
                                                    $tam_icono = get_sub_field('grupo-2-beneficios-tab1-tab2')['tam-icono-contenedor-beneficios-tab1-tab2'];
                                                    $icono_hover = get_sub_field('grupo-2-beneficios-tab1-tab2')['icono-hover-contenedor-beneficios-tab1-tab2'];
                                                    $tam_icono_hover = get_sub_field('grupo-2-beneficios-tab1-tab2')['tam-icono-hover-contenedor-beneficios-tab1-tab2'];
                                                    ?>
                                                    <div class="subcontenido-tab1-tab1">
                                                        <?php if ($icono): ?>
                                                          <img src="<?php echo esc_url($icono['url']); ?>" alt="<?php echo esc_attr($icono['alt']); ?>" class="icono-contenido-tab1-tab1" style="width: <?php echo esc_attr($tam_icono ?: '40px'); ?>;">
                                                        <?php endif; ?>
                                                        <p class="titulo-contenido-tab1-tab1"><?php echo esc_html($titulo); ?></p>
                                                        <p class="descripcion-contenido-tab1-tab1"><?php echo esc_html($descripcion); ?></p>
                                                        <?php if ($icono_hover): ?>
                                                          <img src="<?php echo esc_url($icono_hover['url']); ?>" alt="<?php echo esc_attr($icono_hover['alt']); ?>" class="icono-hover-contenido-tab1-tab1" style="width: <?php echo esc_attr($tam_icono_hover ?: '80px'); ?>;">
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php
                                                endwhile;
                                            endif;
                                        endwhile;
                                    endif;
                                endwhile;
                            endif;
                        endwhile;
                    endif;
                endwhile;
            endif;
            ?>
          </div>
        </div>

        <div class="contenedor-contenido-tabs-tab1">
          <p class="titulo-tab1-tab1"> <?php echo wp_kses_post(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab3']['titulo-beneficios-tab1-tab3']); ?> </p>
          <p class="descripcion-tab1-tab1"> <?php echo nl2br(get_field('tabs-beneficios')['tab1-beneficios']['grupo-1-beneficios-tab1']['grupo-1-beneficios-tab1-tab3']['descripcion-beneficios-tab1-tab3']); ?> </p>

          <div class="contenido-tab1-tab1">
            <?php
            if (have_rows('tabs-beneficios')) :
                while (have_rows('tabs-beneficios')) : the_row();
                    if (have_rows('tab1-beneficios')) :
                        while (have_rows('tab1-beneficios')) : the_row();
                            if (have_rows('grupo-1-beneficios-tab1')) :
                                while (have_rows('grupo-1-beneficios-tab1')) : the_row();
                                    if (have_rows('grupo-1-beneficios-tab1-tab3')) :
                                        while (have_rows('grupo-1-beneficios-tab1-tab3')) : the_row();
                                            if (have_rows('repetidor-beneficios-tab1-tab3')) :
                                                while (have_rows('repetidor-beneficios-tab1-tab3')) : the_row();
                                                    $titulo = get_sub_field('grupo-2-beneficios-tab1-tab3')['titulo-contenedor-beneficios-tab1-tab3'];
                                                    $descripcion = get_sub_field('grupo-2-beneficios-tab1-tab3')['descripcion-contenedor-beneficios-tab1-tab3'];
                                                    $icono = get_sub_field('grupo-2-beneficios-tab1-tab3')['icono-contenedor-beneficios-tab1-tab3'];
                                                    $tam_icono = get_sub_field('grupo-2-beneficios-tab1-tab3')['tam-icono-contenedor-beneficios-tab1-tab3'];
                                                    $icono_hover = get_sub_field('grupo-2-beneficios-tab1-tab3')['icono-hover-contenedor-beneficios-tab1-tab3'];
                                                    $tam_icono_hover = get_sub_field('grupo-2-beneficios-tab1-tab3')['tam-icono-hover-contenedor-beneficios-tab1-tab3'];
                                                    ?>
                                                    <div class="subcontenido-tab1-tab1">
                                                        <?php if ($icono): ?>
                                                          <img src="<?php echo esc_url($icono['url']); ?>" alt="<?php echo esc_attr($icono['alt']); ?>" class="icono-contenido-tab1-tab1" style="width: <?php echo esc_attr($tam_icono ?: '40px'); ?>;">
                                                        <?php endif; ?>
                                                        <p class="titulo-contenido-tab1-tab1"><?php echo esc_html($titulo); ?></p>
                                                        <p class="descripcion-contenido-tab1-tab1"><?php echo esc_html($descripcion); ?></p>
                                                        <?php if ($icono_hover): ?>
                                                          <img src="<?php echo esc_url($icono_hover['url']); ?>" alt="<?php echo esc_attr($icono_hover['alt']); ?>" class="icono-hover-contenido-tab1-tab1" style="width: <?php echo esc_attr($tam_icono_hover ?: '80px'); ?>;">
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php
                                                endwhile;
                                            endif;
                                        endwhile;
                                    endif;
                                endwhile;
                            endif;
                        endwhile;
                    endif;
                endwhile;
            endif;
            ?>
          </div>
        </div>
      </div>

      <div class="contenido-tabs-s3">

        <p class="titulo-tab2"> <?php echo wp_kses_post($tabs_beneficios['tab2-beneficios']['titulo-beneficios-tab2']); ?> </p>

        <?php if (have_rows('tabs-beneficios')): ?>
        <div class="subcontenedor-tab2">
            <?php
            while (have_rows('tabs-beneficios')): the_row();
                if (have_rows('tab2-beneficios')):
                    while (have_rows('tab2-beneficios')): the_row();

                        if (have_rows('repetidor1-beneficios-tab2')):
                            while (have_rows('repetidor1-beneficios-tab2')): the_row();

                                if (have_rows('grupo1-beneficios-tabs2')):
                                    while (have_rows('grupo1-beneficios-tabs2')): the_row();

                                        // Crear el enlace de la tab
                                        $titulo_subtab = get_sub_field('titulo-de-subtab-beneficios-tab2');
                                        ?>
                                        <a class="tab-tab2">
                                            <p class="p1-tab2"><?php echo wp_kses_post($titulo_subtab); ?></p>
                                            <img src="<?php echo get_template_directory_uri(); ?>/imgs/mas.png" class="plus-tab2" alt="icono-mas">
                                            <img src="<?php echo get_template_directory_uri(); ?>/imgs/pico.png" class="pico-tab2" alt="pico">
                                        </a>
                                        <?php

                                    endwhile;
                                endif;

                            endwhile;
                        endif;

                    endwhile;
                endif;
            endwhile;
            ?>
        </div>

        <?php
        // Crear subcontenedores de los rectángulos
        while (have_rows('tabs-beneficios')): the_row();
            if (have_rows('tab2-beneficios')):
                while (have_rows('tab2-beneficios')): the_row();

                    if (have_rows('repetidor1-beneficios-tab2')):
                        while (have_rows('repetidor1-beneficios-tab2')): the_row();

                            if (have_rows('grupo1-beneficios-tabs2')):
                                while (have_rows('grupo1-beneficios-tabs2')): the_row();

                                    // Crear un nuevo subcontenido-tab2 por cada grupo1-beneficios-tabs2
                                    ?>
                                    <div class="subcontenido-tab2">
                                        <?php
                                        if (have_rows('rectangulos-subtab-beneficios-tab2')):
                                            while (have_rows('rectangulos-subtab-beneficios-tab2')): the_row();

                                                if (have_rows('grupo2-beneficios-tab2')):
                                                    while (have_rows('grupo2-beneficios-tab2')): the_row();

                                                        $icono_rectangulo = get_sub_field('icono-rectangulo-beneficios-tab2');
                                                        $icono_hover_rectangulo = get_sub_field('icono-hover-rectangulo-beneficios-tab2');
                                                        $tam_icono_hover_rectangulo_beneficios_tab2 = get_sub_field('tam-icono-hover-rectangulo-beneficios-tab2');
                                                        $titulo_rectangulo = get_sub_field('titulo-rectangulo-beneficios-tab2');
                                                        $descripcion_rectangulo = get_sub_field('descripcion-rectangulo-beneficios-tab2');
                                                        $tam_icono_rectangulo_beneficios_tab2 = get_sub_field('tam-icono-rectangulo-beneficios-tab2');
                                                        ?>
                                                        <div class="rectangulo-tab2">
                                                            <?php if ($icono_rectangulo): ?>
                                                                <img src="<?php echo esc_url($icono_rectangulo['url']); ?>" alt="<?php echo esc_attr($icono_rectangulo['alt']); ?>" class="img-rectangulo-tab2" style="width: <?php echo esc_attr($tam_icono_rectangulo_beneficios_tab2 ?: '40px'); ?>;">
                                                            <?php endif; ?>
                                                            <p class="titulo-rectangulo-tab2"><?php echo esc_html($titulo_rectangulo); ?></p>
                                                            <p class="descripcion-rectangulo-tab2"><?php echo esc_html($descripcion_rectangulo); ?></p>
                                                            <?php if ($icono_hover_rectangulo): ?>
                                                            <img src="<?php echo esc_url($icono_hover_rectangulo['url']); ?>" alt="<?php echo esc_attr($icono_hover_rectangulo['alt']); ?>" class="img-hover-rectangulo-tab2" style="width: <?php echo esc_attr($tam_icono_hover_rectangulo_beneficios_tab2 ?: '75px'); ?>;">
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php

                                                    endwhile;
                                                endif;

                                            endwhile;
                                        endif;
                                        ?>
                                    </div>
                                    <?php

                                endwhile;
                            endif;

                        endwhile;
                    endif;

                endwhile;
            endif;
        endwhile;
        ?>
        <?php endif; ?>

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

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Seleccionar los tabs y los contenidos
    const tabs = document.querySelectorAll(".tab-s2");
    const contents = document.querySelectorAll(".contenido-tabs-s3-movil");

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

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Seleccionar los tabs y los contenidos
    const tabs = document.querySelectorAll(".tab-tab2");
    const contents = document.querySelectorAll(".subcontenido-tab2");

    // Mostrar el contenido del Tab 1 por defecto y marcarlo como activo
    contents.forEach((content, index) => {
      content.style.display = index === 0 ? "flex" : "none";
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
        contents[index].style.display = "flex";
      });
    });
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Seleccionar los tabs y los contenidos
    const tabs = document.querySelectorAll(".tab-tab2-movil");
    const contents = document.querySelectorAll(".subcontenido-tab2-movil");

    // Mostrar el contenido del Tab 1 por defecto y marcarlo como activo
    contents.forEach((content, index) => {
      content.style.display = index === 0 ? "flex" : "none";
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
        contents[index].style.display = "flex";
      });
    });
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Seleccionar los tabs y los contenidos
    const tabs = document.querySelectorAll(".tabs-tab1");
    const contents = document.querySelectorAll(".contenedor-contenido-tabs-tab1");

    // Mostrar el contenido del Tab 1 por defecto y marcarlo como activo
    contents.forEach((content, index) => {
      content.style.display = index === 0 ? "flex" : "none";
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
        contents[index].style.display = "flex";
      });
    });
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Seleccionar los tabs y los contenidos
    const tabs = document.querySelectorAll(".tabs-tab1movil");
    const contents = document.querySelectorAll(".contenedor-contenido-tabs-tab1-movil");

    // Mostrar el contenido del Tab 1 por defecto y marcarlo como activo
    contents.forEach((content, index) => {
      content.style.display = index === 0 ? "flex" : "none";
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
        contents[index].style.display = "flex";
      });
    });
  });
</script>

<?php get_footer(); ?>