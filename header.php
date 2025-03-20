
<?php
// Encabezados de seguridad
header("X-Frame-Options: SAMEORIGIN");
header("Content-Security-Policy: frame-ancestors 'self';");
?>
<!DOCTYPE html>
<html lang="es-MX">


<head>
    <script>
        if (window.top !== window.self) {
            document.body.innerHTML = "";
            window.top.location = window.self.location;
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo();?></title>
    <?php wp_head();?>
</head>

<script>
document.addEventListener("DOMContentLoaded", function() {
    if (localStorage.getItem("registro_exitoso") === "true") {

        const pop_reg = document.getElementById("popup-registro");
        pop_reg.style.display = "flex";
        const close_reg = document.getElementById("close-reg");
        const close_reg_button = document.getElementById("close-reg-button");


        var closeRegistro = document.getElementsByClassName("closebtnpopupRegistro");

        var cerrarPopupRegistro = function() {
            //localStorage.removeItem("registro_exitoso");
            pop_reg.style.display = "none";
        };

        for (var i = 0; i < closeRegistro.length; i++) {
            closeRegistro[i].addEventListener('click', cerrarPopupRegistro, false);
        }


        if(close_reg){
            close_reg.addEventListener("click", function() {
                localStorage.removeItem("registro_exitoso");
                pop_reg.style.display = "none";
            });
        }

        if(close_reg_button){
            close_reg_button.addEventListener("click", function() {
                localStorage.removeItem("registro_exitoso");
                pop_reg.style.display = "none";
            });
        }
    }
});



</script>
<body <?php body_class( );?>>
    <!-- PopUp -->
    <div class="popup-cont" id="popup-registro">
        <div class="container">
            <div class="close closebtnpopupRegistro" id="close-reg">+</div>
            <div class="title">¡Tu registro ha sido <br> <span>exitoso!</span></div>
            <button id="close-reg-button" class="button closebtnpopupRegistro">Cerrar</button>
        </div>
    </div><!-- PopUp -->


    <header id="header">
        <div>
            <div class="container">
                <?php
                    $custom_logo_id = get_theme_mod('custom_logo');
                    $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                    if (has_custom_logo()):
                ?>
                    <div class="site-info">
                        <a href="<?php echo home_url(); ?>">
                            <img class="main-logo" src="<?php echo esc_url($logo[0]) ?>"
                                alt="<?php echo get_bloginfo('name') ?>'">
                            <div class="site-title">
                                Construyendo <br>
                                <strong>Carreras</strong>
                            </div>
                        </a>

                        <div id="top__search-input" class="top__search-input-cont">
                            <?php echo get_search_form(); ?>
                        </div>
                    </div>

                    <!-- Ícono de hamburguesa -->
                    <div class="hamburger-menu" onclick="toggleMenu()">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                <?php endif;?>
                    <nav class="menu-web">
                        <?php wp_nav_menu(array('menu' => 'Header'));?>
                    </nav>
                </div>
                <nav class="menu-wrapper">
                    <?php wp_nav_menu(array('menu' => 'Header'));?>
                </nav>
            </div>
        </div>
    </header>

    <div class="main-content">

        <script>
        function toggleMenu() {
            document.querySelector('.menu-wrapper').classList.toggle('active');
        }
        </script>