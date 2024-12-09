<?php
/**
 * Template Name: Vacantes
 */

get_header();
$tax_name = "categorias_vacantes";
$term = get_field($tax_name);
$term_name = $term->name;

$ubicaciones = get_unique_locations_with_values($term->slug);
$unique_titles = get_unique_vacantes_titles_by_taxonomy($term->slug);
?>


<!-- Banner con el titulo de la página -->
<div class="header" style="background-color: <?php echo getColorCat($term_name);?>; background-image: url(<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>)">
    <div class="container">
        <?php the_title();?>
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
                    <input type="text" placeholder="Ingresa palabras clave del puesto"  class="search-input">
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
                    <input type="text" placeholder="Ingresa tu ubicación"  class="search-input">
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
                    $args = array(
                        'post_type' => 'vacantes',
                        'order' => 'ASC',
                        'orderby' => 'title',
                        'tax_query' => array(
                            array(
                                'taxonomy' => $tax_name,
                                'field' => 'slug',
                                'terms' => $term->slug,
                            ),
                        ),
                    );
                    $query = new WP_Query($args);

                    if ($query->have_posts()):
                    ?>
                    <ul class="list">
                    <?php
                        while ($query->have_posts()):
                            $query->the_post();
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
                                            <?php echo file_get_contents(get_template_directory_uri() . '/imgs/pin-de-ubicacion.svg'); ?>
                                        </div>
                                        <div class="text"><?php echo $ubicacion_formateada; ?></div>
                                    </div>

                                    <div class="icon-cont">
                                        <div class="img">
                                            <?php echo file_get_contents(get_template_directory_uri() . '/imgs/Hora.svg'); ?>
                                        </div>
                                        <div class="text">Lorem ipsum dolor sit, amet</div>
                                    </div>
                                </div>
                                <div class="fav">
                                    <div class="img">
                                        <?php echo file_get_contents(get_template_directory_uri() . '/imgs/me-gusta.svg'); ?>
                                    </div>
                                </div>
                            </li>
                        <?php
                        endwhile;
                    endif;
                    ?>
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


<?php get_footer();?>