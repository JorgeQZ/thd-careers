<?php get_header(); ?>

<!-- Banner con el titulo de la página -->
<div class="header" style="background-color: #f96302; background-image: url(<?php echo get_template_directory_uri() ?>/imgs/banner-resultadoss.jpg);">
    <div class="container">
        Resultados
    </div>
</div><!-- Banner con el titulo de la página -->

<!-- Contenido de la página -->
<main>
    <div class="vacantes-cont">
        <div class="container">
            <?php the_content(); ?>
            <div class="title">
                Nuestra <span>vacantes</span>
            </div>
            <div class="row">

                <!-- Search Input de vacantes -->
                <div class="input-search">
                    <input type="text" placeholder="Ingresa palabras clave del puesto" class="search-input" value="<?php echo get_search_query(); ?>">
                    <ul class="suggestions-list hidden">
                        <?php
                         foreach ($unique_titles as $title) {
                            echo '<li><label>';
                            echo '<input type="checkbox" name="title[]" id="'. esc_html($title).'" value="'. esc_html($title).'">';
                            echo '<span class="checkbox"></span>';
                            echo '<span class="text">' . esc_html($title) . '</span>';
                            echo '</label></li>';
                         }
                        ?>
                    </ul>
                </div><!-- Search Input de vacantes -->

                <!-- Search Input de ubicaciones -->
                <div class="input-search">
                    <input type="text" placeholder="Ingresa tu ubicación" class="search-input" value="<?php echo esc_html( $_GET['ubicacion'] ); ?>">
                    <ul class="suggestions-list hidden">
                        <?php
                        $processed_values = array(); // Para almacenar valores únicos
                        foreach ($ubicaciones as $ubicacion) {
                            if (!in_array($ubicacion['value'], $processed_values)) {
                                echo '<li><label>';
                                echo '<input type="checkbox" name="ubicacion[]" value="' . esc_attr($ubicacion['value']) . '" id="ubicacion-' . esc_attr($ubicacion['value']) . '">';
                                echo '<span class="checkbox"></span>';
                                echo '<span class="text">' . esc_html($ubicacion['label']) . '</span>';
                                echo '</label></li>';
                                $processed_values[] = $ubicacion['value'];
                            }
                        }
                       ?>
                    </ul>
                </div> <!-- Search Input de ubicaciones -->
            </div>

            <div class="columns">
                <div class="column">

                    <?php
                    if ( have_posts() ) :
                    ?>
                    <ul class="list">

                    <?php
                        while ( have_posts() ) : the_post();
                            $ubicacion_label = get_field('ubicacion')['label'];

                            if ($ubicacion_label) {
                                // Convertir el texto a formato más formal
                                $ubicacion_formateada = ucwords(strtolower($ubicacion_label));
                            }
                            ?>

                            <li class="item" data-id="<?php echo get_the_id(); ?>" data-tienda="<?php echo get_field('extra_data_data_tienda'); ?>" data-title="<?php echo get_the_title()?>">
                                <div class="img">
                                    <img src="<?php echo get_template_directory_uri() . '/imgs/logo-thd.jpg' ?>" alt="">
                                </div>
                                <div class="desc">
                                    <a href="<?php the_permalink();?>"><?php echo get_the_title(); ?></a>

                                    <div class="icon-cont">
                                        <div class="img">
                                            <?php
                                                $svg_path = get_template_directory() . '/imgs/pin-de-ubicacion.svg';
                                                if (file_exists($svg_path)) {
                                                    $svg_content = file_get_contents($svg_path);
                                                    echo $svg_content;
                                                }
                                            ?>
                                        </div>
                                        <div class="text"><?php echo $ubicacion_formateada; ?></div>
                                    </div>

                                    <div class="icon-cont">
                                        <div class="img">
                                            <?php
                                                $svg_path = get_template_directory() . '/imgs/Hora.svg';
                                                if (file_exists($svg_path)) {
                                                    $svg_content = file_get_contents($svg_path);
                                                    echo $svg_content;
                                                }
                                            ?>
                                        </div>
                                        <div class="text"><?php  echo get_post_meta( get_the_ID(), 'codigo_de_vacante', true ); ?></div>
                                    </div>
                                </div>
                            </li>
                        <?php
                        endwhile;
                    ?>
                    <?php else : ?>
                        <p><?php _e( 'No se encontraron resultados para tu búsqueda.'); ?></p>
                    <?php endif; ?>
                    </ul>
                </div>

                <div class="column">
                    <div class="saved-jobs">
                        <div class="title">
                            Vacantes de interes
                        </div>
                        <div class="desc">
                            Lorem ipsum, dolor sit amet
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Contenido de la página -->
</main>

<?php get_footer(); ?>