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
    }
}

$IsEMI = get_field('emi');
$qr = get_field('imagen_qr');
$url_de_la_vacante = get_field('url_de_la_vacante');
$is_logged_in = is_user_logged_in();
?>

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
                ¿No tienes cuenta? <a href="<?php echo wp_registration_url(); ?>" class="button_sub">Regístrate aquí</a>
            </div>
        </div>
</div><!-- PopUp -->

<!-- Banner con el titulo de la página -->
<div class="banner" id="vacante-banner" style="background-color: <?php echo getColorCat($term_name);?>; background-image: url(<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>)">
    <div class="container">
        <div class="title-cont">
            <div class="term"><?php echo $term_name; ?> </div>
            <div class="title"> <?php the_title(); ?> </div>
        </div>
        <?php if ($is_logged_in): ?>
            <?php if($IsEMI == 'Si'): ?>
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
                <?php
                echo get_field('video');
                ?>

                <div class="title_b">
                    Beneficios
                </div>

                <div class="desc">
                    <?php
                    $benefits = get_field('beneficios');
                    // Asociación entre los valores y su contenido HTML
                    $icons = array(
                        'Sueldo aprox.' => '<img src="'.get_template_directory_uri(  ).'/imgs/icon-money.png" >Sueldo Aproximado',
                        'Vales de despensa' => '<img src="'.get_template_directory_uri(  ).'/imgs/icon-bono.png" >Vales de Despensa',
                        'Bono Variable' => '<img src="'.get_template_directory_uri(  ).'/imgs/icon-coins.png" >Bono Variable',
                        'Fondo de ahorro' => '<img src="'.get_template_directory_uri(  ).'/imgs/icon-savings.png" >Fondo de Ahorro',
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
        <?php
        include get_template_directory() . '/templates/page-postulaciones.php';
        ?>
    </div>
</main>


<script>
// Esperar a que el DOM esté cargado
document.addEventListener("DOMContentLoaded", function() {


    const close_mensaje = document.getElementById("close-mensaje");
    const mensaje = document.getElementById('mensajeExito');
    const form = document.getElementById('formularioPostulacion');

    if (close_mensaje) {
        close_mensaje.addEventListener("click", function() {
            mensaje.style.display = "none";
        });
    }

    if (localStorage.getItem('formSubmitted') === 'true') {
        if (mensaje) {
            mensaje.style.display = 'flex';
        }
        localStorage.removeItem('formSubmitted');
    }

    if (form) {
        form.addEventListener("submit", function() {
            localStorage.setItem('formSubmitted', 'true');
        });
    }

    const button = document.getElementById("open-form");
    const formDiv = document.querySelector(".form-post");

    if(button){
        button.addEventListener("click", function() {
            // Asegurarse de que el div sea visible
            formDiv.style.display = "block";
            // Agregar la clase para la animación
            formDiv.classList.add("fade-in");
            const formPost = document.querySelector('.form-post');
            if (formPost) {
                // Función para calcular dinámicamente el offset
                const calculateOffset = () => {
                    const navHeight = document.querySelector('#header') ? document.querySelector('#header').offsetHeight : 0;
                    const bannerHeight = document.querySelector('.banner') ? document.querySelector('.banner').offsetHeight : 0;
                    return navHeight + bannerHeight;
                };

                // Calcular el offset inicial
                const initialOffset = calculateOffset();

                // Calcular la posición de la sección .form-post con el offset
                const scrollToPosition = formPost.offsetTop - initialOffset;

                // Desplazamiento con scroll
                window.scrollTo({
                    top: scrollToPosition,
                    behavior: 'smooth'  // Desplazamiento suave
                });

                // Opcional: Recalcular el offset después de un pequeño retraso si el tamaño cambia de nuevo
                setTimeout(() => {
                    const recalculatedOffset = calculateOffset();
                    const recalculatedScrollPosition = formPost.offsetTop - recalculatedOffset;

                    window.scrollTo({
                        top: recalculatedScrollPosition,
                        behavior: 'smooth'  // Asegurarse de que el desplazamiento se ajusta correctamente
                    });
                }, 300);  // Ajusta este tiempo si es necesario para la transición de tamaño
            }
        });
    }

    const button_emi = document.getElementById("open-emi-form");
    const emi = document.getElementById("popup-emi");
    const butto_login = document.getElementById("login-prompt");
    const login = document.getElementById("popup-login");


    if(button_emi){

        button_emi.addEventListener("click", function() {
            emi.style.display = "flex";
        });
    }

    if(butto_login){



        butto_login.addEventListener("click", function() {
            login.style.display = "flex";
        });
    }

    const close = document.getElementById("close");
    if(close){


        close.addEventListener("click", function() {
            emi.style.display = "none";
        });
    }

    const close_login = document.getElementById("close-login");
    if(close_login){
        close_login.addEventListener("click", function() {
            login.style.display = "none";
        });
    }


});

document.addEventListener("DOMContentLoaded", function() {
    const formDiv = document.querySelector("div.form-post");

    // Verifica si el formulario debe estar visible
    if (localStorage.getItem("formVisible") === "true") {
        formDiv.style.display = "block";
        formDiv.classList.add("fade-in");
    }

    // Manejar el evento submit del formulario
    const form = document.querySelector("form");
    form.addEventListener("submit", function(e) {
        // Aquí se podría validar o enviar datos antes
        localStorage.setItem("formVisible", "true");
        formDiv.style.display = "block"; // Asegurarse de que sea visible
        formDiv.classList.add("fade-in");
    });
});
</script>

<?php
get_footer();
?>