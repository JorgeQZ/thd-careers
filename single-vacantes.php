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
?>

<!-- PopUp -->
<div class="popup-cont" id="popup-emi">
    <div class="container">
        <div class="close" id="close">+</div>
        <div class="title">¡Completa tu proceso de forma fácil y rápida!</div>
        <div class="desc">
        Conéctate con nuestro reclutador virtual a través de WhatsApp y avanza en tu proceso. Escanea el QR con tu móvil o haz clic en el enlace para acceder a la versión web.
        </div>
        <div class="img-cont">
            <img src="<?php echo $qr; ?>" alt="">
        </div>
        <a href="<?php echo $url_de_la_vacante; ?>"  target="_blank" class="button">Haz clic aquí</a>
    </div>
</div><!-- PopUp -->

<!-- Banner con el titulo de la página -->
<div class="banner" id="vacante-banner" style="background-color: <?php echo getColorCat($term_name);?>; background-image: url(<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>)">
    <div class="container">
        <div class="title-cont">
            <div class="term"><?php echo $term_name; ?> </div>
            <div class="title"> <?php the_title(); ?> </div>
        </div>
        <?php if($IsEMI == 'Si'): ?>
            <div class="button" id="open-emi-form">
                Postulate aquí
            </div>
        <?php else: ?>
            <div class="button" id="open-form">
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
    const button = document.getElementById("open-form");
    const formDiv = document.querySelector("div.form-post");

    if(button){
        button.addEventListener("click", function() {
            // Asegurarse de que el div sea visible
            formDiv.style.display = "block";
            // Agregar la clase para la animación
            formDiv.classList.add("fade-in");
        });
    }

    const button_emi = document.getElementById("open-emi-form");
    const emi = document.getElementById("popup-emi");

    if(button_emi){

        // button.addEventListener("click", function() {
        //     // Asegurarse de que el div sea visible
        //     formDiv.style.display = "block";
        //     // Agregar la clase para la animación
        //     formDiv.classList.add("fade-in");
        // });

        button_emi.addEventListener("click", function() {
            emi.style.display = "flex";
        });
    }

    const close = document.getElementById("close");
    if(close){

        // button.addEventListener("click", function() {
        //     // Asegurarse de que el div sea visible
        //     formDiv.style.display = "block";
        //     // Agregar la clase para la animación
        //     formDiv.classList.add("fade-in");
        // });

        close.addEventListener("click", function() {
            emi.style.display = "none";
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