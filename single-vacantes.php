<?php
get_header();

$current_post_id = get_the_ID();
if ($current_post_id) {
    $current_post_type = get_post_type($current_post_id);
    $taxonomies = get_object_taxonomies($current_post_type, 'names');
    if (!empty($taxonomies)) {
        $taxonomy = $taxonomies[0];
        $terms = wp_get_post_terms($current_post_id, $taxonomy);
        $term = $terms[0];
        $term_name = $term->name;
        $term_url_real = get_term_link($term);
        $aux = strpos($term_url_real, "categorias_vacantes/");
        $term_url = substr($term_url_real, $aux + strlen("categorias_vacantes/"));
    }
}

$IsEMI = get_field('emi', $current_post_id);
$qr = get_field('imagen_qr');
$qr = wp_get_attachment_url($qr);
$url_de_la_vacante = get_field('url_de_la_vacante');

$is_logged_in = is_user_logged_in();

?>

<!-- PopUp -->
<div class="popup-cont" id="popup-emi">
    <div class="container">
        <div class="close" id="close">+</div>
        <div class="title">¡Completa tu proceso <br> <span>de forma fácil y rápida!</span></div>
        <div class="desc">
            Conéctate con nuestro reclutador virtual a través de WhatsApp y avanza en tu proceso. <br><br>
            <span>Escanea el QR con tu móvil o haz clic en el botón inferior para acceder a la versión web.</span>
        </div>
        <div class="img-cont">
            <img src="<?php echo $qr; ?>" alt="">
        </div>
        <a href="<?php echo $url_de_la_vacante; ?>" rel="noopener noreferrer" target="_blank" class="button">Haz clic
            aquí</a>
    </div>
</div><!-- PopUp -->


<div class="popup-cont" id="mensajeExito">
    <div class="container">
        <div class="close" id="close-mensaje">+</div>
        <div class="title">Tu postulación para la vacante se ha <br> enviado con <span> éxito</span></div>
    </div>
</div><!-- PopUp -->



<!-- PopUp -->

<div class="popup-cont" id="popup-login">
    <div class="container">
        <div class="close" id="close-login">+</div>
        <div class="title">Inicia sesión o <span>Regístrate</span></div>
        <div class="desc">
            Para poder postularte, es necesario que inicies sesión. Si aún no cuentas con una cuenta, puedes registrarte
            de manera rápida y sencilla. <br><br>
        </div>

        <?php
        // const SAML_TOKEN_QA   = 'e2cfc6d3517de87577eaa735b870490966faf04a4e2e96b1d51ca0b5b6919b2f';
        // const SAML_TOKEN_PROD = '719652f1df11814efaad458e9aa79d6f10fd2bcc81acf2b620a1063fe5537b65';
        ?>
        <div class="login-form">
            <!-- Formulario de login -->
            <form
                id="popup-login-form"
                method="post"
                action="<?php echo esc_url(
                    add_query_arg(
                        'saml_sso',
                        'e2cfc6d3517de87577eaa735b870490966faf04a4e2e96b1d51ca0b5b6919b2f',
                        wp_login_url()
                    )
                ); ?>"
                >



                <input type="text" name="log" placeholder="Nombre de usuario o correo" required autocomplete="off"
                    pattern="^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$">

                <input type="password" name="pwd" autocomplete="off" placeholder="Contraseña" required>

                <input type="hidden" name="redirect_to" value="<?php echo esc_url( get_permalink() ); ?>" />

                <!-- Flag visual -->
                <input type="hidden" name="from_vacante" value="1">

                <!-- Contenedor para mensajes de error del login (AJAX) -->
                <div id="popup-login-error"
                    class="error-message"
                    style="color:red; margin-top:8px; display:none;"></div>

                <br>
                <button type="submit" class="button_sub" name="custom_login" id="popup-login-submit">Iniciar sesión</button>
            </form>
        </div>
        <hr>
        <div class="register-link">
            ¿No tienes cuenta? <a href="<?php echo site_url('/login/'); ?>" class="button_sub">Regístrate aquí</a>
        </div>

    </div>
</div><!-- PopUp -->

<!-- Banner con el titulo de la página -->
<div class="banner" id="vacante-banner"
    style="background-color: <?php echo getColorCat($term_name);?>; background-image: url(<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>)">
    <div class="container">
        <div class="title-cont">
            <div class="term"> <a href="<?php echo $term_url; ?>"> <?php echo $term_name; ?> </a> </div>
            <div class="title"> <?php the_title(); ?> </div>
        </div>
        <?php if ($is_logged_in): ?>
        <?php if ($IsEMI == 'Si'): ?>
        <div class="button" id="open-emi-form">
            Postulate aquí
        </div>
        <?php else: ?>
        <div class="button" id="open-form">
            Postulate aquí
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="button" id="login-prompt">
            Postulate aquí
        </div>
        <?php endif; ?>
    </div>
</div><!-- Banner con el titulo de la página -->


<main>
    <div class="container">
        <div class="desc-video">
            <div class="column">
                <div class="desc">
                    <?php echo get_field('descripcion'); ?>
                </div>
            </div>
            <div class="column">
                <?php echo get_field('video'); ?>

                <div class="title_b">
                    Beneficios
                </div>

                <div class="desc">
                    <?php
                    $benefits = get_field('beneficios');
                    // Asociación entre los valores y su contenido HTML
                    $icons = array(
                        'Prestaciones superiores a la ley' => '<img src="'.get_template_directory_uri().'/imgs/prestaciones_superiores.png" >Prestaciones superiores a la ley',
                        'Bono por objetivos' => '<img src="'.get_template_directory_uri().'/imgs/bono_por_objetivos.png" >Bono por objetivos',
                        'Fondo de Ahorro' => '<img src="'.get_template_directory_uri().'/imgs/fondo_de_ahorro.png" >Fondo de Ahorro',
                        'Flexibilidad Laboral' => '<img src="'.get_template_directory_uri().'/imgs/flexibilidad_laboral.png" >Flexibilidad Laboral',
                        'Caja de Ahorro' => '<img src="'.get_template_directory_uri().'/imgs/caja_de_ahorro.png" >Caja de Ahorro',
                        'Seguros y Apoyos económicos' => '<img src="'.get_template_directory_uri().'/imgs/seguro_y_apoyos_econonicos.png" >Seguros y Apoyos económicos',
                        'Compra de acciones' => '<img src="'.get_template_directory_uri().'/imgs/compra_de_acciones.png" >Compra de acciones',
                        'Vales de despensa' => '<img src="'.get_template_directory_uri().'/imgs/vales_de_despensa.png" >Vales de despensa',
                    );
                    // Recorrer el array y generar HTML para los valores existentes
                    foreach ($benefits as $benefit) {
                        if (array_key_exists($benefit, $icons)) {
                            echo '<div class="benefit-item">' . $icons[$benefit] . '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-post">
        <?php include get_template_directory() . '/templates/page-postulaciones.php'; ?>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ===============================
     * 1. RESTORE UI POST-LOGIN
     * =============================== */
    if (document.body.classList.contains('logged-in')) {

        if (document.cookie.indexOf('thd_from_vacante=1') !== -1) {

            // borrar cookie
            document.cookie = "thd_from_vacante=; path=/; max-age=0";

            const openFormBtn = document.getElementById("open-form");
            if (openFormBtn) {
                openFormBtn.click();
            }
        }
    }

    /* ===============================
     * 2. MENSAJE DE ÉXITO POSTULACIÓN
     * =============================== */
    const mensaje       = document.getElementById("mensajeExito");
    const closeMensaje  = document.getElementById("close-mensaje");
    const formPostulacion = document.getElementById("formularioPostulacion");

    if (closeMensaje && mensaje) {
        closeMensaje.addEventListener("click", function () {
            mensaje.style.display = "none";
        });
    }

    if (localStorage.getItem("formSubmitted") === "true") {
        if (mensaje) {
            mensaje.style.display = "flex";
        }
        localStorage.removeItem("formSubmitted");
    }

    if (formPostulacion) {
        formPostulacion.addEventListener("submit", function () {
            localStorage.setItem("formSubmitted", "true");
        });
    }

    /* ===============================
     * 3. ABRIR FORMULARIO POSTULACIÓN
     * =============================== */
    const openFormBtn = document.getElementById("open-form");
    const formDiv     = document.querySelector(".form-post");

    if (openFormBtn && formDiv) {
        openFormBtn.addEventListener("click", function () {

            formDiv.style.display = "block";
            formDiv.classList.add("fade-in");

            const calculateOffset = () => {
                const navHeight    = document.querySelector("#header")?.offsetHeight || 0;
                const bannerHeight = document.querySelector(".banner")?.offsetHeight || 0;
                return navHeight + bannerHeight;
            };

            const scrollToForm = () => {
                const offset = calculateOffset();
                window.scrollTo({
                    top: formDiv.offsetTop - offset,
                    behavior: "smooth"
                });
            };

            scrollToForm();
            setTimeout(scrollToForm, 300);
        });
    }

    /* ===============================
     * 4. POPUP EMI
     * =============================== */
    const openEmiBtn = document.getElementById("open-emi-form");
    const emiPopup   = document.getElementById("popup-emi");
    const closeEmi   = document.getElementById("close");

    if (openEmiBtn && emiPopup) {
        openEmiBtn.addEventListener("click", function () {
            emiPopup.style.display = "flex";
        });
    }

    if (closeEmi && emiPopup) {
        closeEmi.addEventListener("click", function () {
            emiPopup.style.display = "none";
        });
    }

    /* ===============================
     * 5. POPUP LOGIN
     * =============================== */
    const loginPrompt = document.getElementById("login-prompt");
    const loginPopup  = document.getElementById("popup-login");
    const closeLogin  = document.getElementById("close-login");

    if (loginPrompt && loginPopup) {
        loginPrompt.addEventListener("click", function () {
            // guardar intención (cookie corta)
            document.cookie = "thd_from_vacante=1; path=/; max-age=300";
            loginPopup.style.display = "flex";
        });
    }

    if (closeLogin && loginPopup) {
        closeLogin.addEventListener("click", function () {
            loginPopup.style.display = "none";
        });
    }

});
</script>
<?php
get_footer();
?>