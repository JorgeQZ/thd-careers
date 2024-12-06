<?php
/**
 * Requires
 */

/**
 * Vacantes
 */
require_once "inc/vacantes/conf.php";
require_once "inc/vacantes/load_stores_values.php";
require_once "inc/vacantes/catalogo_vacantes.php";
require_once "inc/vacantes/mis_vacantes.php";

/**
 * Users
 */
require_once "inc/users/fields.php";
require_once "inc/users/roles.php";
require_once get_template_directory() . '/inc/users/miperfil.php';

/**
 * Postulaciones
 */
require_once get_template_directory() . '/inc/postulaciones/conf.php';

/**
 * Map
 */
require_once "inc/map/conf.php";

/**
 * CSV Uploader
 */
require_once "inc/csvuploader/csv_uploader.php";


/**
 * CSV Uploader
 */
require_once "patterns/block-paterrns.php";

/**
 * General Setup
 */

if (!function_exists('careers_setup')):
    function careers_setup()
{
        add_theme_support('post-thumbnails');
        add_theme_support('page-attributes');
        add_theme_support('custom-logo');
        add_post_type_support('post', 'page-attributes');
        register_nav_menus(
            array(
                'primary_menu' => __('Menú Principal', 'text_domain'),
                'footer_menu' => __('Menú de pie de página', 'text_domain'),
            )
        );
    }
endif;
add_action('after_setup_theme', 'careers_setup');

function careers_styles()
{
    wp_enqueue_style('generals', get_template_directory_uri() . '/css/generals.css');
    wp_enqueue_script('generals', get_template_directory_uri() . '/js/generals.js');

    if (is_page('Mi Perfil')) {
        wp_enqueue_style('miperfil', get_template_directory_uri() . '/css/miperfil.css');
    }

    if (is_page('Inicio')) {
        wp_enqueue_style('home', get_template_directory_uri() . '/css/home.css');
        wp_enqueue_style('frontpage', get_template_directory_uri() . '/css/frontpage.css');
    }

    if (is_page('Beneficios')) {
        wp_enqueue_style('beneficios', get_template_directory_uri() . '/css/beneficios.css');
    }

    if (is_page_template('templates/page-vacantes.php')) {
        wp_enqueue_style('vacantes', get_template_directory_uri() . '/css/vacantes.css');
    }

    if (is_singular('vacantes')) {
        wp_enqueue_style('postulaciones', get_template_directory_uri() . '/css/postulaciones.css');
    }
}
add_action('wp_enqueue_scripts', 'careers_styles');

function my_custom_widgets_init()
{
    register_sidebar(array(
        'name' => 'Sidebar Principal',
        'id' => 'sidebar-principal',
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'my_custom_widgets_init');

function filtrar_postulaciones_en_admin($query)
{
    // Verificar que estamos en el área de administración, en la consulta principal y en el post-type 'postulaciones'
    if (is_admin() && $query->is_main_query() && $query->get('post_type') === 'postulaciones') {

        // Obtener el usuario actualmente logueado en el wp-admin
        $current_user = wp_get_current_user();

        // Verificar si el usuario tiene el campo 'tienda' con el valor '01'
        $tienda = get_field('tienda', 'user_' . $current_user->ID);

        if ($tienda === '01') {
            // Modificar la consulta para mostrar solo los posts con el estado 'Visto'
            $meta_query = [
                [
                    'key' => 'estado', // Nombre del campo ACF
                    'value' => 'Visto',
                    'compare' => '=',
                ],
            ];

            $query->set('meta_query', $meta_query);
        }
    }
}
// add_action('pre_get_posts', 'filtrar_postulaciones_en_admin');

function highlight_and_break_title($title)
{
    // Evitar afectar los títulos en el administrador
    if (is_admin()) {
        return $title;
    }

    // Lista de palabras clave a resaltar
    $words_to_highlight = array(
        'de' => '<span class="highlight-black">de</span><br>', // Incluye salto de línea después de "de"
        'Centros' => '<span class="highlight-orange">Centros</span>',
        'Logísticos' => '<span class="highlight-orange">Logísticos</span>',
    );

    // Reemplazar las palabras clave en el título
    foreach ($words_to_highlight as $word => $replacement) {
        $title = preg_replace('/\b' . preg_quote($word, '/') . '\b/i', $replacement, $title);
    }

    return $title;
}
// add_filter('the_title', 'highlight_and_break_title');
