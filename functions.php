<?php
/**
 * Requires
 */
require_once("inc/vacantes/conf.php");
require_once("inc/catalogo.php");
require_once("inc/users/fields.php");
require_once("inc/users/roles.php");

/**
 * General Setup
 */

 if ( ! function_exists( 'careers_setup' ) ) :
	function careers_setup() {
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'page-attributes' );
        add_theme_support( 'custom-logo' );
        add_post_type_support( 'post', 'page-attributes' );
	}
endif;
add_action( 'after_setup_theme', 'careers_setup' );
?>