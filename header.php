<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo(); ?></title>
    <?php wp_head(); ?>
</head>
<body>

<header>
    <div class="container">
        <?php

        $custom_logo_id = get_theme_mod( 'custom_logo' );
        $logo = wp_get_attachment_image_src( $custom_logo_id, 'full' );
        if ( has_custom_logo() ): ?>
        <div class="site-info">
            <img class="main-logo" src="<?php echo esc_url( $logo[0] ) ?>" alt="<?php echo get_bloginfo( 'name' ) ?>'">
            <div class="site-title">
                Construyendo <br>
                <strong>Carreras</strong>
            </div>
        </div>
        <?php endif;


        wp_nav_menu( 'primary_menu' );
        ?>
    </div>
</header>
<div class="main-content">