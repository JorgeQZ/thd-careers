<!DOCTYPE html>
<html lang="es-MX">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo();?></title>
    <?php wp_head();?>
</head>


<body>

    <header>
        <div>
            <div class="container">
                <?php
$custom_logo_id = get_theme_mod('custom_logo');
$logo = wp_get_attachment_image_src($custom_logo_id, 'full');
if (has_custom_logo()): ?>
                <div class="site-info">
                    <img class="main-logo" src="<?php echo esc_url($logo[0]) ?>"
                        alt="<?php echo get_bloginfo('name') ?>'">
                    <div class="site-title">
                        Construyendo <br>
                        <strong>Carreras</strong>
                    </div>
                </div>
                <?php endif;?>

                <!-- Ícono de hamburguesa -->
                <div class="hamburger-menu" onclick="toggleMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <nav class="menu-web">
                    <?php wp_nav_menu(array('menu' => 'Header'));?>
                </nav>
            </div>
            <nav class="menu-wrapper">
                <?php wp_nav_menu(array('menu' => 'Header'));?>
            </nav>
        </div>
    </header>
    <div class="main-content">

        <script>
        function toggleMenu() {
            document.querySelector('.menu-wrapper').classList.toggle('active');
        }
        </script>