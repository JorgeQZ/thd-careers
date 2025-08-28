<?php get_header();

$unique_titles = get_unique_vacantes_titles();
$ubicaciones = get_unique_locations();
?>

<!-- PopUp -->

<div class="popup-cont" id="popup-login">
        <div class="container">
            <div class="close" id="close-login">+</div>
            <div class="title">Inicia sesión o <span>Regístrate</span></div>
            <div class="desc">
                Para poder postularte, es necesario que inicies sesión. Si aún no cuentas con una cuenta, puedes registrarte de manera rápida y sencilla. <br><br>
            </div>
            <div class="login-form">
                <!-- Formulario de login -->
                <form action="<?php echo wp_login_url(); ?>" method="post">
                     <input type="text" name="log" placeholder="Nombre de usuario o correo" required autocomplete="off">

                    <input type="password" name="pwd" autocomplete="off" placeholder="Contraseña" required>
                    <input type="hidden" name="redirect_to" value="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" />
                    <br>
                    <button type="submit" class="button_sub">Iniciar sesión</button>
                </form>
            </div>
            <hr>
            <div class="register-link">
                ¿No tienes cuenta?  <a href="<?php echo site_url('/login/'); ?>" class="button_sub">Regístrate aquí</a>
            </div>
        </div>
</div><!-- PopUp -->


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

            <form class="row <?php if(!is_user_logged_in(  )){ echo 'wide-column'; }?>" action="<?php echo esc_url( home_url("/")  ); ?> " method="get">

                <!-- Search Input de vacantes -->
                <div class="input-search" id="search-vacante">
                    <input type="text" id="titulo" name="s" placeholder="Ingresa palabra(s) clave de la vacante" class="search-input" placeholder="Ingresa palabras clave del puesto" class="search-input " value="<?php echo get_search_query(); ?>">
                    <ul class="suggestions-list hidden">
                        <li  class="li-label"><label><span class="text"><h3>Vacantes disponibles</span></h3></label></li>
                        <?php
                         foreach ($unique_titles as $title) {
                            echo '<li><label>';
                            echo '<span class="text">' . esc_html($title) . '</span>';
                            echo '</label></li>';
                         }
                        ?>
                    </ul>
                </div><!-- Search Input de vacantes -->

                <!-- Search Input de ubicaciones -->
                <div class="input-search" id="search-ubicacion">
                    <input id="inp-sear" class="search-input" type="text" name="ubicacion_label" placeholder="Ingresa tu ubicación" value="<?php echo esc_html( $_GET['ubicacion_label'] ); ?>">
                    <input name="ubicacion" type="hidden" id="ubicacion">
                    <ul class="suggestions-list hidden">
                        <li  class="li-label"><label><span class="text"><h3>Ubicaciones disponibles</span></h3></label></li>
                        <?php
                        $processed_values = array(); // Para almacenar valores únicos
                        foreach ($ubicaciones as $ubicacion) {
                            $ubicacion_label = ucwords(strtolower($ubicacion['label']));
                            $ubicacion_value = strtolower($ubicacion['value']); // Mantener el valor original en minúscula para evitar conflictos
                            if (!in_array($ubicacion_value, $processed_values)) {
                                echo '<li class="ubicacion_values" data-value="'. esc_attr($ubicacion_value) . '"><label>';
                                echo '<span class="text">' . esc_html($ubicacion_label) . '</span>';
                                echo '</label></li>';
                                $processed_values[] = $ubicacion_value;
                            }
                        }
                       ?>
                    </ul>
                </div> <!-- Search Input de ubicaciones -->

                <input type="submit" value="Buscar vacante" id="boton">
            </form>

            <div class="columns">
                <div class="column <?php if(!is_user_logged_in(  )){ echo 'wide-column'; }?>">

                    <?php
                    if ( have_posts() ) :
                    ?>
                    <ul class="list job-list">

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
                                            <img src="<?php echo esc_url( get_theme_file_uri('imgs/pin-de-ubicacion-2.png') ); ?>" alt="Icono de Ubicación">
                                            <div class="text"><?php echo $ubicacion_formateada; ?></div>
                                        </div>
                                    </div>
                                    <div class="fav">
                                        <div class="img">
                                            <img src="<?php echo esc_url( get_theme_file_uri('imgs/me-gusta-2.png') ); ?>" alt="Icono de Me gusta">
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

                <?php if(is_user_logged_in(  )): ?>
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

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const img = document.querySelector(".fav img");
    img.addEventListener("click", function () {
      img.classList.toggle("active");
    });
  });
</script>

<?php get_footer(); ?>