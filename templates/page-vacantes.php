<?php
/**
 * Template Name: Vacantes
 */

get_header();

$tax_name = "categorias_vacantes";
$term = get_field($tax_name);
$term_name = '';
if ($term != '') {
    $term_name    = $term->name;
    $ubicaciones  = get_unique_locations_with_values($term->slug);
    $unique_titles = get_unique_vacantes_titles_by_taxonomy($term->slug);
} else {
    $unique_titles = get_unique_vacantes_titles();
    $ubicaciones   = get_unique_locations();
}

/** --- mapa código -> label humano desde $ubicaciones (normalizado) --- */
$loc_map = array();
foreach ((array) $ubicaciones as $u) {
    if (is_array($u) && isset($u['value'], $u['label'])) {
        $raw_val = (string) $u['value'];
        // Si el value trae más texto, extrae el patrón NNNN-DD
        if (preg_match('/\b(\d{3,5}-\d{1,3})\b/u', $raw_val, $mm)) {
            $raw_val = $mm[1];
        }
        // Normaliza clave: trim + minúsculas
        $k = strtolower(trim($raw_val));
        $loc_map[$k] = trim((string) $u['label']);
    }
}

$is_logged_in = is_user_logged_in();
?>
<!-- PopUp -->
<div class="popup-cont" id="popup-login">
    <div class="container">
        <div class="close" id="close-login">+</div>
        <div class="title">Inicia sesión o <span>Regístrate</span></div>
        <div class="desc">
            Para poder postularte, es necesario que inicies sesión. Si aún no cuentas con una cuenta, puedes registrarte
            de manera rápida y sencilla. <br><br>
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
<div class="header"
    style="background-color: <?php echo getColorCat($term_name);?>; background-image: url(<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>)">
    <div class="container">
        <?php the_title();?>
    </div>
</div><!-- Banner con el titulo de la página -->

<!-- Contenido de la página -->
<main>
    <div class="vacantes-cont">
        <div class="container">
            <?php the_content(); ?>

            <?php $slug = get_post_field('post_name', get_post());
if ($slug !== 'nuestras-vacantes' && $slug !== 'ver-todo') : ?>
            <div class="title">
                Nuestras <span>vacantes</span>
            </div>
            <?php endif; ?>
            <p>Comienza realizando una búsqueda mediante palabras clave o ubicación para mostrar resultados</p>

            <div class="row <?php if (!is_user_logged_in()) {
                echo 'wide-column';
            }?>">

                <!-- Search Input de vacantes -->
                <div class="input-search wide">
                    <input type="text" placeholder="Ingresa palabras clave del puesto" class="search-input">
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
                    <input type="text" placeholder="Ingresa tu ubicación" class="search-input">
                    <?php if ($term != ''): ?>
                    <ul class="suggestions-list hidden">
                        <?php
$processed_values = array(); // Para almacenar valores únicos (normalizados)
                        foreach ($ubicaciones as $ubicacion) {
                            if (!is_array($ubicacion) || !isset($ubicacion['value'], $ubicacion['label'])) {
                                continue;
                            }
                            // Normaliza clave para dedupe
                            $k = strtolower(trim((string)$ubicacion['value']));
                            if ($k === '' || in_array($k, $processed_values, true)) {
                                continue;
                            }

                            echo '<li><label>';
                            echo '<input type="checkbox" name="ubicacion[]" value="' . esc_attr($ubicacion['value']) . '" id="ubicacion-' . esc_attr($ubicacion['value']) . '">';
                            echo '<span class="checkbox"></span>';
                            echo '<span class="text">' . esc_html($ubicacion['label']) . '</span>';
                            echo '</label></li>';

                            $processed_values[] = $k;
                        }
                        ?>
                    </ul>
                    <?php else: ?>
                    <ul class="suggestions-list hidden">
                        <?php
                        $processed_values = array();

                        foreach ((array) $ubicaciones as $ubicacion) {
                            // Normalizar a label/value
                            if (is_array($ubicacion)) {
                                $label_raw = isset($ubicacion['label']) ? (string) $ubicacion['label'] : '';
                                $value_raw = isset($ubicacion['value']) ? (string) $ubicacion['value'] : '';
                            } elseif (is_string($ubicacion) || is_numeric($ubicacion)) {
                                $label_raw = (string) $ubicacion;
                                $value_raw = (string) $ubicacion;
                            } else {
                                continue; // formato no soportado
                            }

                            $label_raw = trim(wp_strip_all_tags($label_raw));
                            $value_raw = trim((string) $value_raw);

                            // Valor en minúsculas para consistencia y comparación
                            $ubicacion_value = strtolower($value_raw);
                            if ($ubicacion_value === '') {
                                continue;
                            }

                            // Label humano (si no hay label, usa el value)
                            $human = $label_raw !== '' ? $label_raw : $value_raw;

                            // Si el value es NNNN-DD y existe en $loc_map, forzar label humano del catálogo
                            if (preg_match('/^\d{3,5}-\d{1,3}$/', $value_raw)) {
                                $key = strtolower(trim($value_raw));
                                if (isset($loc_map[$key]) && $loc_map[$key] !== '') {
                                    $human = $loc_map[$key];
                                }
                            }

                            // Title Case (UTF-8 si está disponible)
                            if (function_exists('mb_convert_case')) {
                                $ubicacion_label = mb_convert_case($human, MB_CASE_TITLE, 'UTF-8');
                            } else {
                                $ubicacion_label = ucwords(strtolower($human));
                            }

                            // Evitar duplicados (comparación estricta)
                            if (in_array($ubicacion_value, $processed_values, true)) {
                                continue;
                            }

                            echo '<li class="ubicacion_values" data-value="' . esc_attr($ubicacion_value) . '"><label>';
                            echo '<span class="text">' . esc_html($ubicacion_label) . '</span>';
                            echo '</label></li>';

                            $processed_values[] = $ubicacion_value;
                        }
                        ?>
                    </ul>
                    <?php endif; ?>
                </div> <!-- Search Input de ubicaciones -->
            </div>

            <div class="columns">
                <div class="column <?php if (!is_user_logged_in()) {
                    echo 'wide-column';
                }?>">
                    <?php
                    $args = array(
                        'post_type'   => 'vacantes',
                        'post_status' => 'publish',
                        'order'       => 'ASC',
                        'orderby'     => 'title',
                    );

if (!empty($term) && !empty($term->slug)) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => $tax_name,
            'field'    => 'slug',
            'terms'    => $term->slug,
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

            $raw   = get_field('ubicacion', get_the_ID());
            $label = '';
            $value = '';

            if (is_array($raw)) {
                $label = (string) ($raw['label'] ?? '');
                $value = (string) ($raw['value'] ?? '');
            } elseif (is_string($raw) || is_numeric($raw)) {
                $value = (string) $raw;
            }

            /** --- normalización con $loc_map: si hay código NNNN-DD, usa label humano --- */
            $source = $value !== '' ? $value : $label;
            if (preg_match('/\b(\d{3,5}-\d{1,3})\b/u', (string)$source, $m)) {
                $code_key = strtolower(trim($m[1]));
                if (isset($loc_map[$code_key]) && $loc_map[$code_key] !== '') {
                    $label = $loc_map[$code_key];
                }
            }

            /** --- fallbacks a choices y limpieza como ya tenías --- */
            $fobj    = function_exists('get_field_object') ? get_field_object('ubicacion', get_the_ID()) : null;
            $choices = (is_array($fobj) && isset($fobj['choices']) && is_array($fobj['choices'])) ? $fobj['choices'] : array();

            if ($label === '' || preg_match('/^\d+(?:-\d+)?$/', $label)) {
                if ($value !== '' && isset($choices[$value]) && is_string($choices[$value])) {
                    $label = trim((string) $choices[$value]);
                }
            }

            if ($label === '' || preg_match('/^\d+(?:-\d+)?$/', $label)) {
                $guess = preg_replace('/^\s*\d+(?:-\d+)?\s*(?:[:\-\|\x{2013}\x{2014}])?\s*/u', '', (string) $value);
                $guess = is_string($guess) ? trim($guess) : (string)$value; // fallback defensivo
                if ($guess !== '') {
                    $label = $guess;
                }
            }

            if ($label === '') {
                $label = (string) $value;
            }

            $display = function_exists('mb_convert_case')
                ? mb_convert_case($label, MB_CASE_TITLE, 'UTF-8')
                : ucwords(strtolower($label));
            ?>
                            <li class="item" data-id="<?php echo esc_attr(get_the_ID()); ?>"
                                data-tienda="<?php echo esc_attr(get_field('extra_data_data_tienda')); ?>"
                                data-title="<?php echo esc_attr(get_the_title()); ?>">
                                <a href="<?php the_permalink(); ?>">
                                    <div class="img">
                                        <img src="<?php echo esc_url(get_template_directory_uri() . '/imgs/logo-thd.jpg'); ?>" alt="">
                                    </div>
                                    <div class="desc">
                                        <div class="job-title"><?php echo get_the_title(); ?></div>
                                        <div class="icon-cont">
                                            <img src="<?php echo esc_url(get_theme_file_uri('imgs/pin-de-ubicacion-2.png')); ?>" alt="Location">
                                            <div class="text"><?php echo esc_html($display); ?></div>
                                        </div>
                                    </div>
                                    <div class="fav">
                                        <div class="img">
                                            <img src="<?php echo esc_url(get_theme_file_uri('imgs/me-gusta-2.png')); ?>" alt="Like">
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                    <?php
                    endif;
wp_reset_postdata();
?>
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

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Delegación: funciona para todas las tarjetas
    const list = document.querySelector(".list.job-list");
    if (!list) return;

    list.addEventListener("click", function(e) {
        const img = e.target.closest(".fav img");
        if (!img) return;
        e.preventDefault();
        e.stopPropagation();
        img.classList.toggle("active");
    });
});
</script>

<?php get_footer(); ?>
