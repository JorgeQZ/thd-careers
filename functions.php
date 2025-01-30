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
require_once "inc/users/config-login.php";
require_once "inc/users/favs.php";
require_once get_template_directory() . '/inc/users/miperfil.php';

/**
 * Postulaciones
 */
require_once get_template_directory() . '/inc/postulaciones/conf.php';

/**
 * Buscador
 */
require_once("inc/ajax_search.php");

/**
 * Map
 */
require_once "inc/map/conf.php";
require_once "inc/map/conf.php";

/**
 * CSV Uploader
 */
require_once "inc/csvuploader/csv_uploader.php";


/**
 * GCP Bucket
 */
require_once "inc/gcpbucket/gcpbucket.php";


/**
 * CSV Uploader
 */
require_once "patterns/block-paterrns.php";

/**
 * Notificaciones
 */
require_once "inc/rueda_valores.php";

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
    wp_enqueue_script('favs', get_template_directory_uri() . '/js/favs.js');
    wp_enqueue_script('search', get_stylesheet_directory_uri(). '/js/search.js', array('jquery'), '2', true );


    wp_localize_script('generals', 'ajax_query_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'logoutUrl' => wp_logout_url(home_url() ),
        'isUserLoggedIn' => is_user_logged_in(),
        'currentUserId' => get_current_user_id()
    ));


    wp_localize_script('favs', 'favs_query_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'isUserLoggedIn' => is_user_logged_in(),
        'currentUserId' => get_current_user_id()
    ));

    if (is_page('Mi Perfil')) {
        wp_enqueue_style('miperfil', get_template_directory_uri() . '/css/miperfil.css');
    }

    if (is_page('Mi')) {
        wp_enqueue_style('miperfil', get_template_directory_uri() . '/css/mi.css');
    }

    if (is_page('Inicio')) {
        wp_enqueue_style('home', get_template_directory_uri() . '/css/home.css');
        wp_enqueue_style('frontpage', get_template_directory_uri() . '/css/frontpage.css');
    }

    if (is_page('Beneficios')) {
        wp_enqueue_style('beneficios', get_template_directory_uri() . '/css/beneficios.css');
    }

    if (is_search()) {
        wp_enqueue_style('vacantes', get_template_directory_uri() . '/css/vacantes.css');
    }

    if (is_page('Cultura')) {
        wp_enqueue_style('cultura', get_template_directory_uri() . '/css/cultura.css');
    }

    if (is_page_template('templates/page-vacantes.php')) {
        wp_enqueue_style('vacantes', get_template_directory_uri() . '/css/vacantes.css');
    }

    if (is_singular('vacantes')) {
        wp_enqueue_style('postulaciones', get_template_directory_uri() . '/css/postulaciones.css');
    }


    if (is_page_template('templates/saved-jobs.php')) {
        wp_enqueue_style('saved-jobs', get_template_directory_uri() . '/css/saved-jobs.css');
    }


    if (is_page_template('templates/notificaciones.php')) {
        wp_enqueue_style('notificaciones', get_template_directory_uri() . '/css/notificaciones.css');
    }

    if (is_page_template('templates/login.php')) {
        wp_enqueue_style('custom-login', get_template_directory_uri() . '/css/login.css');
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


function get_favorites_handler() {
    // Verifica que se hayan enviado los datos
    if (!isset($_POST['favorites'])) {
        wp_send_json_error(['message' => 'No se enviaron datos.']);
        wp_die();
    }

    try {
        if (!isset($_POST['favorites']) || empty($_POST['favorites'])) {
            throw new Exception('El parámetro "favorites" no está definido o está vacío.');
        }

        $favorites_data = stripslashes($_POST['favorites']);
        $favorite_ids = json_decode($favorites_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Error al decodificar el JSON: ' . json_last_error_msg());
        }

        // Aquí puedes continuar procesando $favorite_ids si es válido
    } catch (Exception $e) {
        error_log('Error en get_favorites_handler: ' . $e->getMessage());
        wp_send_json_error(['message' => 'Hubo un error procesando la solicitud. Por favor, inténtelo más tarde.'], 400);
    }

    // Validar errores de JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error(['message' => 'Error al decodificar la lista de favoritos: ' . json_last_error_msg()]);
        wp_die();
    }

    // Verificar si la lista está vacía
    if (empty($favorite_ids)) {
        wp_send_json_error(['message' => 'La lista de favoritos está vacía.']);
        wp_die();
    }

    // Cargar íconos estáticos una vez
    $location_icon = file_get_contents(get_template_directory() . '/imgs/pin-de-ubicacion.svg');
    $time_icon = file_get_contents(get_template_directory() . '/imgs/Hora.svg');
    $like_icon = file_get_contents(get_template_directory() . '/imgs/me-gusta.svg');

    $posts = [];
    foreach ($favorite_ids as $id) {
        $post = get_post($id);

        // Validar que el post exista y sea del tipo esperado
        if ($post && $post->post_type === 'vacantes') { // Slug del CPT

            // Obtener y formatear ubicación
            $ubicacion_field = get_field('ubicacion', $post->ID);
            $ubicacion_label = $ubicacion_field['label'] ?? null;
            $ubicacion_formateada = $ubicacion_label ? ucwords(strtolower($ubicacion_label)) : 'Ubicación no disponible';

            // Añadir datos al array de posts
            $posts[] = [
                'id'           => $post->ID,
                'title'        => get_the_title($post),
                'permalink'    => get_permalink($post),
                'image'        => get_template_directory_uri() . '/imgs/logo-thd.jpg',
                'location'     => $ubicacion_formateada,
                'location_icon'=> $location_icon,
                'time_text'    => 'Lorem ipsum dolor sit, amet', // Texto genérico, personalízalo
                'time_icon'    => $time_icon,
                'like_icon'    => $like_icon,
                'tienda'       => get_field('extra_data_data_tienda', $post->ID) ?: 'Sin tienda', // Campo ACF o personalizado
            ];
        }
    }

    // Responder con los datos procesados
    wp_send_json($posts);
    wp_die();
}

// Registrar AJAX
add_action('wp_ajax_get_favorites', 'get_favorites_handler');
add_action('wp_ajax_nopriv_get_favorites', 'get_favorites_handler');

function modify_menu_items($items, $args) {
    if ($args->menu === 'Header') { // Asegúrate de que el menú sea el correcto
        foreach ($items as &$item) {
            if ($item->title === 'MI PERFIL') {
                $item->classes[] = 'menu-item-mi-perfil';

                if (!is_user_logged_in()) {
                    // Obtén la URL de la imagen desde el tema activo
                    $icon_url = get_template_directory_uri() . '/imgs/icono-perfil.png';

                    // Modifica el título para incluir la imagen y el texto
                    $item->title = '<img src="' . esc_url($icon_url) . '" alt="Icono Perfil" class="menu-profile-icon" style="display: block; margin: 0 auto; width: 35px; padding-bottom: 10px;"><span>REGÍSTRATE <br> O INICIA SESIÓN</span>';
                    $item->url = home_url().'/login/';

                    // Si el usuario está en la URL de login, añade una clase personalizada
                    if (is_page() && strpos($_SERVER['REQUEST_URI'], '/thd-careers/login/') !== false) {
                        $item->classes[] = 'current-login';
                    }
                }
            }
        }
    }
    return $items;
}
add_filter('wp_nav_menu_objects', 'modify_menu_items', 10, 2);




function limitar_busqueda_a_post_types( $query ) {
    if ( ! is_admin() && $query->is_search ) {
        $query->set( 'post_type', 'vacantes' );
    }
    return $query;
}
add_filter( 'pre_get_posts', 'limitar_busqueda_a_post_types' );

function buscar_por_titulo_y_custom_field( $query ) {
    if ( $query->is_search && !is_admin() ) {

        $custom_field_value = isset( $_GET['ubicacion_key'] ) ? sanitize_text_field( $_GET['ubicacion_key'] ) : '';

        if ( ! empty( $custom_field_value ) ) {
            $meta_query = array(
                array(
                    'key'   => 'ubicacion',
                    'value' => $custom_field_value,
                    'compare' => 'LIKE',
                ),
            );
            $query->set( 'meta_query', $meta_query );
        }

        if ( ! empty( $_GET['s'] ) ) {
        }
    }
}
add_action( 'pre_get_posts', 'buscar_por_titulo_y_custom_field' );

add_action('send_headers', function() {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
});


//  Page Options
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title'    => 'Rueda de valores',
        'menu_title'    => 'Rueda de Valores',
        'menu_slug'     => 'rueda-de-valores-acf',
        'capability'    => 'edit_posts', // Define quién tiene acceso.
        'redirect'      => false,
        'position'      => 2, // Posición del menú en el panel de administración
        'icon_url'      => 'dashicons-admin-generic', // Ícono personalizado (opcional)'
    ));
}

//  Page Options
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title'    => 'Catálogo de tiendas',
        'menu_title'    => 'Catálogo de tiendas',
        'menu_slug'     => 'catalogo-tiendas-acf',
        'capability'    => 'edit_posts', // Define quién tiene acceso.
        'redirect'      => false,
        'position'      => 25, // Posición del menú en el panel de administración
        'icon_url'      => 'dashicons-admin-generic', // Ícono personalizado (opcional)
    ));
}