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
                Nuestras <span>vacantes</span>
            </div>
            <div class="row">

                <?php
                    if(get_search_query()){
                ?>

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

                <?php
                }
                ?>

                <?php
                    if(esc_html( $_GET['ubicacion'] )){
                ?>

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

                <?php
                }
                ?>
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
                                <a href="<?php the_permalink();?>">
                                    <div class="img">
                                        <img src="<?php echo get_template_directory_uri() . '/imgs/logo-thd.jpg' ?>" alt="">
                                    </div>
                                    <div class="desc">
                                        <div class="job-title"><?php echo get_the_title(); ?></div>
                                        <div class="icon-cont">
                                            <div class="img">
                                                <?php
                                                    $svg_path = get_template_directory() . '/imgs/pin-de-ubicacion.svg';

                                                    if (file_exists($svg_path) && pathinfo($svg_path, PATHINFO_EXTENSION) === 'svg') {
                                                        // Leer el contenido del archivo SVG
                                                        $svg_content = file_get_contents($svg_path);

                                                        // Escapar caracteres peligrosos para prevenir XSS
                                                        echo $svg_content;
                                                    } else {
                                                        echo 'Archivo SVG no encontrado o no válido.';
                                                    }
                                                ?>
                                            </div>
                                            <div class="text"><?php echo $ubicacion_formateada; ?></div>
                                        </div>
                                    </div>
                                    <div class="fav">
                                        <div class="img">
                                            <?php
                                                $file_path = get_template_directory() . '/imgs/me-gusta.svg';

                                                if (file_exists($file_path)) {
                                                    echo file_get_contents($file_path); // Asegúrate de que el archivo sea seguro
                                                } else {
                                                    echo 'Archivo no encontrado.';
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </a>
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
                            Vacantes de interés
                        </div>
                        <div class="desc">
                            Haz clic en el botón para ver las vacantes de tu interés guardadas.
                        </div>
                        <a href="<?php echo home_url().'/vacantes-de-interes/' ?>" class="button">Ir a mis vacantes</a>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Contenido de la página -->
</main>

<?php get_footer(); ?>