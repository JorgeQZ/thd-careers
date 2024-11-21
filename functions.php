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

require get_template_directory() . '/patterns/custom-pattern.php';

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



function careers_styles() {
  wp_enqueue_style( 'generals', get_template_directory_uri(  ).'/css/generals.css');

	// if (is_page_template('page-miperfil.php')) {
		wp_enqueue_style( 'miperfil', get_template_directory_uri() . '/css/miperfil.css' );
	// }

  if (is_page('Inicio')) {
		wp_enqueue_style( 'home', get_template_directory_uri() . '/css/home.css' );
    wp_enqueue_style( 'frontpage', get_template_directory_uri() . '/css/frontpage.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'careers_styles' );


function my_custom_widgets_init() {
    register_sidebar(array(
        'name'          => 'Sidebar Principal',
        'id'            => 'sidebar-principal',
        'before_widget' => '<div class="widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'my_custom_widgets_init');
?>