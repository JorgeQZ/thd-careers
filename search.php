<?php
get_header();

$unique_titles = get_unique_vacantes_titles();
$ubicaciones   = get_unique_locations();

// --- mapa código -> label humano desde $ubicaciones (NO cambia tus variables existentes)
$loc_map = array();
foreach ((array) $ubicaciones as $u) {
    if (is_array($u) && isset($u['value'], $u['label'])) {
        $loc_map[(string)$u['value']] = (string)$u['label']; // value esperado: NNNN-DD
    }
}
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
        <div class="login-form">
            <!-- Formulario de login -->
            <form id="popup-login-form" action="<?php echo wp_login_url(); ?>" method="post">
                <input type="text" name="log" placeholder="Nombre de usuario o correo" required autocomplete="off">
                <input type="password" name="pwd" autocomplete="off" placeholder="Contraseña" required>
                <input type="hidden" name="redirect_to" value="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" />

                <!-- Contenedor de errores para el popup (AJAX) -->
                <div id="popup-login-error"
                    class="error-message"
                    style="color:red; margin-top:8px; display:none;"></div>

                <br>
                <button type="submit" class="button_sub" name="custom_login">Iniciar sesión</button>
            </form>
        </div>
        <hr>
        <div class="register-link">
            ¿No tienes cuenta? <a href="<?php echo site_url('/login/'); ?>" class="button_sub">Regístrate aquí</a>
        </div>
    </div>
</div><!-- PopUp -->

<!-- Banner con el titulo de la página -->
<div class="header"
    style="background-color: #f96302; background-image: url(<?php echo get_template_directory_uri() ?>/imgs/banner-resultadoss.jpg);">
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

            <form class="row <?php if (!is_user_logged_in()) {
                echo 'wide-column';
            }?>"
                action="<?php echo esc_url(home_url("/")); ?> " method="get">

                <!-- Search Input de vacantes -->
                <div class="input-search" id="search-vacante">
                    <input type="text" id="titulo" name="s" placeholder="Ingresa palabra(s) clave de la vacante"
                        class="search-input" placeholder="Ingresa palabras clave del puesto" class="search-input "
                        value="<?php echo get_search_query(); ?>">
                    <ul class="suggestions-list hidden">
                        <li class="li-label"><label><span class="text">
                                    <h3>Vacantes disponibles
                                </span></h3></label></li>
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
                    <input id="inp-sear" class="search-input" type="text" name="ubicacion_label"
                        placeholder="Ingresa tu ubicación" value="<?php echo esc_html($_GET['ubicacion_label']); ?>">
                    <input name="ubicacion" type="hidden" id="ubicacion">
                    <ul class="suggestions-list hidden">
                        <li class="li-label"><label><span class="text">
                                    <h3>Ubicaciones disponibles
                                </span></h3></label></li>
                        <?php
$processed_values = array(); // Para almacenar valores únicos

foreach ((array) $ubicaciones as $ubicacion) {
    // Normalizar a label/value
    if (is_array($ubicacion)) {
        $label_raw = isset($ubicacion['label']) ? (string) $ubicacion['label'] : '';
        $value_raw = isset($ubicacion['value']) ? (string) $ubicacion['value'] : '';
    } elseif (is_string($ubicacion) || is_numeric($ubicacion)) {
        // Por si llega directo como string (ACF Return: Value/Label)
        $label_raw = (string) $ubicacion;
        $value_raw = (string) $ubicacion;
    } else {
        continue; // formato no soportado
    }

    $label_raw = trim(wp_strip_all_tags($label_raw));
    $value_raw = trim((string) $value_raw);

    // Valor en minúsculas para consistencia y comparación (conservamos tu variable)
    $ubicacion_value = strtolower($value_raw);
    if ($ubicacion_value === '') {
        continue;
    }

    // Label humano (si no hay label, usa el value)
    $human = $label_raw !== '' ? $label_raw : $value_raw;

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
                </div> <!-- Search Input de ubicaciones -->

                <input type="submit" value="Buscar vacante" id="boton">
            </form>

            <div class="columns">
                <div class="column <?php if (!is_user_logged_in()) {
                    echo 'wide-column';
                }?>">

                    <?php
                    if (have_posts()) :
                        ?>
                    <ul class="list job-list">
                        <?php
                        while (have_posts()) : the_post();

                            static $choices = null;
                            if (!is_array($choices)) {
                                $f = function_exists('get_field_object') ? get_field_object('ubicacion', get_the_ID()) : null;
                                $choices = (is_array($f) && isset($f['choices']) && is_array($f['choices'])) ? $f['choices'] : array();
                            }

                            $raw   = get_field('ubicacion', get_the_ID());
                            $label = '';
                            $value = '';

                            if (is_array($raw)) {
                                $label = (string) ($raw['label'] ?? '');
                                $value = (string) ($raw['value'] ?? '');
                            } elseif (is_string($raw) || is_numeric($raw)) {
                                $value = (string) $raw;
                            }

                            // ---- Normalización mínima sin cambiar tus variables ----
                            // Si el value/label incluye correo u otros datos, extrae el código NNNN-DD
                            $source = $value !== '' ? $value : $label;
                            if (preg_match('/\b(\d{3,5}-\d{1,3})\b/u', (string)$source, $m)) {
                                $code = $m[1];
                                if (isset($loc_map[$code])) {
                                    $label = $loc_map[$code]; // usa el label humano del catálogo
                                }
                            }
                            // --------------------------------------------------------

                            if ($label === '' || preg_match('/^\d+(?:-\d+)?$/', $label)) {
                                if ($value !== '' && isset($choices[$value]) && is_string($choices[$value])) {
                                    $label = trim((string) $choices[$value]);
                                }
                                if ($label === '' || preg_match('/^\d+(?:-\d+)?$/', $label)) {
                                    $tmp = preg_replace('/^\s*\d+(?:-\d+)?\s*(?:[:\-\|\x{2013}\x{2014}])?\s*/u', '', (string) $value);
                                    $tmp = trim((string) $tmp);
                                    if ($tmp !== '') {
                                        $label = $tmp;
                                    }
                                }
                            }

                            if ($label === '') {
                                $label = (string) $value;
                            }

                            $display = function_exists('mb_convert_case')
                                ? mb_convert_case($label, MB_CASE_TITLE, 'UTF-8')
                                : ucwords(strtolower($label));
                            ?>
                        <li class="item" data-id="<?php echo get_the_ID(); ?>"
                            data-tienda="<?php echo esc_attr(get_field('extra_data_data_tienda')); ?>"
                            data-title="<?php echo esc_attr(get_the_title()); ?>">
                            <a href="<?php the_permalink(); ?>">
                                <div class="img">
                                    <img src="<?php echo esc_url(get_template_directory_uri() . '/imgs/logo-thd.jpg'); ?>"
                                        alt="">
                                </div>
                                <div class="desc">
                                    <div class="job-title"><?php the_title(); ?></div>
                                    <div class="icon-cont">
                                        <img src="<?php echo esc_url(get_theme_file_uri('imgs/pin-de-ubicacion-2.png')); ?>"
                                            alt="Location">
                                        <div class="text"><?php echo esc_html($display); ?></div>
                                    </div>
                                </div>
                                <div class="fav">
                                    <div class="img">
                                        <img src="<?php echo esc_url(get_theme_file_uri('imgs/me-gusta-2.png')); ?>"
                                            alt="Like">
                                    </div>
                                </div>
                            </a>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                    <?php else : ?>
                    <p><?php _e('No se encontraron resultados para tu búsqueda.'); ?></p>
                    <?php endif; ?>
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
    // Toggle de favoritos (como ya lo tenías)
    const img = document.querySelector(".fav img");
    if (img) {
        img.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            img.classList.toggle("active");
        });
    }

    // ====== SAML + AJAX Login para el popup ======

    const loginForm   = document.getElementById('popup-login-form');
    const loginButton = loginForm ? loginForm.querySelector('button[name="custom_login"]') : null;
    const errorBox    = document.getElementById('popup-login-error');

    function mostrarErrorPopup(msg) {
        if (errorBox) {
            errorBox.textContent = msg;
            errorBox.style.display = 'block';
        } else {
            alert(msg);
        }
    }

    function limpiarErrorPopup() {
        if (errorBox) {
            errorBox.textContent = '';
            errorBox.style.display = 'none';
        }
    }

    // Igual estilo que tu snippet: funcion addSaml + listener en el botón
    function addSaml(form) {
        if (!form) return;
        const currentAction = form.getAttribute('action') || window.location.href;
        const url = new URL(currentAction, window.location.href);

        url.searchParams.set(
            'saml_sso',
            'e2cfc6d3517de87577eaa735b870490966faf04a4e2e96b1d51ca0b5b6919b2f'
        );
        form.setAttribute('action', url.toString());
    }

    if (loginButton && loginForm) {
        // Click en el botón: aseguramos que lleve SAML en el action
        loginButton.addEventListener('click', function() {
            addSaml(loginForm);
        });

        // Submit por AJAX: sin recargar, con mensajes de error
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault(); // evita reload

            limpiarErrorPopup();
            loginButton.disabled = true;

            // Por si envían con Enter: garantizamos SAML antes del AJAX
            addSaml(loginForm);

            const formData = new FormData(loginForm);
            formData.append('action', 'custom_ajax_login');
            formData.append('security', '<?php echo esc_js( wp_create_nonce("custom_login_nonce") ); ?>');

            // Aseguramos redirect_to si viniera vacío
            const redirInput = loginForm.querySelector('input[name="redirect_to"]');
            if (!redirInput || !redirInput.value) {
                formData.append('redirect_to', window.location.href);
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo esc_url( admin_url("admin-ajax.php") ); ?>', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    loginButton.disabled = false;

                    if (xhr.status === 200) {
                        let response;
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (err) {
                            mostrarErrorPopup('No fue posible iniciar sesión. Verifica tus datos e inténtalo de nuevo.');
                            return;
                        }

                        if (response && response.success && response.data && response.data.redirect) {
                            // Login correcto → redirige
                            window.location.href = response.data.redirect;
                        } else if (response && !response.success && response.data && response.data.message) {
                            // Mensaje de PHP (correo inválido, no registrado, contraseña incorrecta, etc.)
                            mostrarErrorPopup(response.data.message);
                        } else {
                            mostrarErrorPopup('No fue posible iniciar sesión. Verifica tus datos e inténtalo de nuevo.');
                        }
                    } else {
                        mostrarErrorPopup('Error de conexión. Inténtalo de nuevo.');
                    }
                }
            };

            xhr.send(formData);
        });
    }
});
</script>

<?php get_footer(); ?>
