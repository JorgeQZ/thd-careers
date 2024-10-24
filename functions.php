<?php
/**
 * Requires
 */

 /**
  * Vacantes
  */
require_once("inc/vacantes/conf.php");
require_once("inc/vacantes/load_stores_values.php");

/**
 * Users
 */
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
    register_nav_menus(
        array(
          'primary_menu' => __('Menú Principal', 'text_domain'),
          'footer_menu' => __('Menú de pie de página', 'text_domain')
        )
      );
	}
endif;
add_action( 'after_setup_theme', 'careers_setup' );
?>