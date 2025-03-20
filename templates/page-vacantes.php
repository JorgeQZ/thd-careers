<?php
/**
 * Template Name: Vacantes
 */

get_header();

$tax_name = "categorias_vacantes";
$term = get_field($tax_name);
$term_name = '';
if($term != ''){
    $term_name = $term->name;
    $ubicaciones = get_unique_locations_with_values($term->slug);
    $unique_titles = get_unique_vacantes_titles_by_taxonomy($term->slug);
}else{
    $unique_titles = get_unique_vacantes_titles();
    $ubicaciones = get_unique_locations();
}
$is_logged_in = is_user_logged_in();


?>
<!-- PopUp -->
<div class="popup-cont" id="popup-login">
    <div class="container">
        <div class="close" id="close-login">+</div>
        <div class="title">Inicia sesión o <span>Regístrate</span></div>
        <div class="desc">
            Para poder postularte, es necesario que inicies sesión. Si aún no cuentas con una cuenta, puedes registrarte de manera rápida y sencilla. <br><br>
        </div>

        <!-- Mensajes de error -->
        <?php if (isset($_GET['login']) && $_GET['login'] == 'failed') : ?>
            <div class="error-message" style="color: red; margin-bottom: 10px;">
                Usuario o contraseña incorrectos. Intenta de nuevo.
            </div>
        <?php endif; ?>

        <div class="login-form">
            <form action="<?php echo esc_url(site_url('wp-login.php')); ?>" method="post">
                <input type="text" name="log" placeholder="Nombre de usuario o correo" required autocomplete="off">
                <input type="password" name="pwd" autocomplete="off" placeholder="Contraseña" required>
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" />

                <br>
                <button type="submit" class="button_sub">Iniciar sesión</button>
            </form>
        </div>
        <hr>
        <div class="register-link">
            ¿No tienes cuenta? <a href="<?php echo site_url('/login/'); ?>" class="button_sub">Regístrate aquí</a>
        </div>
    </div>
</div>

<!-- PopUp -->


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
                Nuestras <span>vacantes</span>
            </div>
            <p>Comienza realizando una búsqueda mediante palabras clave o ubicación para mostrar resultados</p>
            <div class="row <?php if(!is_user_logged_in(  )){ echo 'wide-column'; }?>" >

                <!-- Search Input de vacantes -->
                <div class="input-search wide">
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
                <div class="input-search wide">
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
                <div class="column <?php if(!is_user_logged_in(  )){ echo 'wide-column'; }?>">
                    <?php
                    $args = array(
                        'post_type' => 'vacantes',
                        'post_status' => 'publish',
                        'order' => 'ASC',
                        'orderby' => 'title',
                    );

                    // Verificar si el slug está disponible en $term
                    if ( isset( $term->slug ) && !empty( $term->slug ) ) {
                        $args['tax_query'] = array(
                            array(
                                'taxonomy' => $tax_name,
                                'field' => 'slug',
                                'terms' => $term->slug,
                            ),
                        );
                    }

                    $query = new WP_Query($args);

                    if ($query->have_posts()):
                    ?>
                    <ul class="list job-list">
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
                                <a href="<?php the_permalink();?>">
                                    <div class="img">
                                        <img src="<?php echo get_template_directory_uri() . '/imgs/logo-thd.jpg' ?>" alt="">
                                    </div>
                                    <div class="desc">
                                        <div class="job-title"><?php echo get_the_title(); ?></div>
                                        <div class="icon-cont">
                                            <div class="img">
                                                <?php
                                                    $file_path = get_template_directory() . '/imgs/pin-de-ubicacion.svg';


                                                    if (file_exists($file_path)) {
                                                        $content = file_get_contents($file_path);

                                                        // Permitir solo etiquetas básicas de SVG
                                                        $safe_content = strip_tags($content, '<svg><path><circle><rect><g><use>');

                                                        echo $safe_content;
                                                    } else {
                                                        echo 'Archivo no encontrado.';
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
                                                    $content = file_get_contents($file_path);

                                                    // Permitir solo etiquetas básicas de SVG
                                                    $safe_content = strip_tags($content, '<svg><path><circle><rect><g><use>');

                                                    echo $safe_content;
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
                    endif;
                    ?>
                    </ul>
                </div>

                <?php if (is_user_logged_in()): ?>
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
                <?php endif; ?>
            </div>
        </div>
    </div><!-- Contenido de la página -->
</main>


<?php get_footer();?>