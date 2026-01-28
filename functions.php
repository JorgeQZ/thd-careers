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
require_once "inc/users/helpers.php";
require_once "inc/users/fields.php";
require_once "inc/users/roles.php";
require_once "inc/users/config-login.php";
require_once "inc/users/favs.php";

// require_once get_template_directory() . '/inc/users/miperfil.php';

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
    wp_enqueue_script('search', get_stylesheet_directory_uri(). '/js/search.js', array('jquery'), '2', true);


    wp_localize_script('generals', 'ajax_query_vars', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'logoutUrl' => wp_logout_url(get_permalink( get_page_by_path('login') ) ),
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

    if (is_page_template('templates/page-adp.php')) {
        wp_enqueue_style('adp', get_template_directory_uri() . '/css/adp.css');
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

    if (is_404()) {
        wp_enqueue_style('404', get_template_directory_uri() . '/css/404.css');
    }

    if (is_page_template('templates/lost-password.php')) {
        wp_enqueue_style('lost_password', get_template_directory_uri() . '/css/lost_password.css');
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


function get_favorites_handler()
{
    try {
        // Verificar si el POST contiene el parámetro necesario
        if (!isset($_POST['favorites']) || empty($_POST['favorites'])) {
            throw new Exception('La lista de favoritos está vacía o no se recibió.');
        }

        $favorites_json = stripslashes($_POST['favorites']);
        $favorite_ids = json_decode($favorites_json, true, 512, JSON_THROW_ON_ERROR);
        // Verificar que el JSON decodificado sea un array válido
        if (!is_array($favorite_ids) || empty($favorite_ids)) {
            throw new Exception('La lista de favoritos no es válida.');
        }

        $posts = [];
        foreach ($favorite_ids as $id) {
            $post = get_post($id);
            if ($post && $post->post_type === 'vacantes') { // Slug del CPT

                $ubicacion_label = get_field('ubicacion', $post->ID)['label'];

                if ($ubicacion_label) {
                    // Convertir el texto a formato más formal (capitalización correcta)
                    $ubicacion_formateada = ucwords(strtolower($ubicacion_label));
                } else {
                    $ubicacion_formateada = 'Ubicación no disponible';
                }
                $posts[] = [
                    'id'           => $post->ID,
                    'title'        => get_the_title($post),
                    'permalink'    => get_permalink($post),
                    'image'        => get_template_directory_uri() . '/imgs/logo-thd.jpg',
                    'location'     => $ubicacion_formateada, // Usamos la ubicación formateada
                    'location_icon' => file_get_contents(get_template_directory_uri() . '/imgs/pin-de-ubicacion.svg'),
                    'time_text'    => 'Lorem ipsum dolor sit, amet', // Texto genérico, personalízalo
                    'time_icon'    => file_get_contents(get_template_directory_uri() . '/imgs/Hora.svg'),
                    'like_icon'    => file_get_contents(get_template_directory_uri() . '/imgs/me-gusta.svg'),
                    'tienda'       => get_field('extra_data_data_tienda', $post->ID) ?: 'Sin tienda', // ACF o campo personalizado
                ];
            }
        }

        wp_send_json($posts);
        wp_die();
    } catch (JsonException $e) {
        error_log('Error al decodificar JSON en get_favorites_handler: ' . $e->getMessage());
        wp_send_json_error('Hubo un problema al procesar los datos. Inténtalo de nuevo.');

    } catch (Exception $e) {
        error_log('Error en get_favorites_handler: ' . $e->getMessage());
        wp_send_json_error($e->getMessage());
    }
}

// Registrar AJAX
add_action('wp_ajax_get_favorites', 'get_favorites_handler');
add_action('wp_ajax_nopriv_get_favorites', 'get_favorites_handler');

function modify_menu_items($items, $args)
{
    if ($args->menu === 'Header') { // Asegúrate de que el menú sea el correcto
        foreach ($items as &$item) {
            if ($item->title === 'MI PERFIL') {
                $item->classes[] = 'menu-item-mi-perfil';

                if (!is_user_logged_in()) {
                    // Obtén la URL de la imagen desde el tema activo
                    $icon_url = get_template_directory_uri() . '/imgs/icono-perfil.png';

                    // Modifica el título para incluir la imagen y el texto
                    $item->title = '<img src="' . esc_url($icon_url) . '" alt="Icono Perfil" class="menu-profile-icon" style="display: block; margin: 0 auto; width: 35px; padding-bottom: 10px; padding-right: 10px;"><span>INICIA SESIÓN / REGÍSTRATE</span>';
                    $item->url = home_url().'/login';

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




function limitar_busqueda_a_post_types($query)
{
    if (! is_admin() && $query->is_search) {
        $query->set('post_type', 'vacantes');
    }
    return $query;
}
add_filter('pre_get_posts', 'limitar_busqueda_a_post_types');

function buscar_por_titulo_y_custom_field($query)
{
    if ($query->is_search && !is_admin()) {

        $custom_field_value = isset($_GET['ubicacion']) ? sanitize_text_field($_GET['ubicacion']) : '';

        if (! empty($custom_field_value)) {
            $meta_query = array(
                array(
                    'key'   => 'ubicacion',
                    'value' => $custom_field_value,
                    'compare' => 'LIKE',
                ),
            );
            $query->set('meta_query', $meta_query);
        }

        if (! empty($_GET['s'])) {
        }
    }
}
add_action('pre_get_posts', 'buscar_por_titulo_y_custom_field');

add_action('send_headers', function () {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
});


//  Page Options
if (function_exists('acf_add_options_page')) {
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
if (function_exists('acf_add_options_page')) {
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

function replace_mark_with_span($content)
{
    // Reemplazar la apertura de <mark> con <span>
    $content = preg_replace('/<mark([^>]*)>/', '<span$1>', $content);
    // Reemplazar el cierre de </mark> con </span>
    $content = str_replace('</mark>', '</span>', $content);
    return $content;
}
add_filter('the_content', 'replace_mark_with_span', 20);

function search_form_home_banner()
{
    // Obtener títulos únicos y ubicaciones
    $unique_titles = get_unique_vacantes_titles();
    $ubicaciones = get_unique_locations();

    // Pasar datos a JavaScript
    wp_localize_script('map', 'map_vars', array(
        'theme_uri' => get_template_directory_uri(),
        'ajax_url' => admin_url('admin-ajax.php'),
        'stores' => get_vacancies_by_store()
    ));

    // Iniciar la salida HTML
    $output = '<div class="wp-block-group search-form">
      <form class="search-form" action="'.esc_url(home_url("/")).'" method="get">
                    <div class="search-cont input-search" id="search-vacante">
                        <input type="text" id="titulo" name="s" placeholder="Ingresa palabra(s) clave de la vacante" class="search-input" value="'.get_search_query().'">
                        <ul class="suggestions-list hidden">
                         <li class="li-label"><label><span class="text"><h3>Vacantes disponibles</span></h3></label></li>';
    foreach ($unique_titles as $title) {
        $title_capitalized = ucwords(strtolower($title));
        $output .= '<li><label>';

        $output .= '<span class="text">' . esc_html($title_capitalized) . '</span>';
        $output .= '</label></li>';
    }
    $output .=        '</ul>
                    </div>';

    // Campo de búsqueda para ubicaciones
    $output .=  '<div class="search-cont input-search" id="search-ubicacion">
                <input id="inp-sear" class="search-input" type="text" name="ubicacion_label" placeholder="Ingresa tu ubicación">
                <input name="ubicacion" type="hidden" id="ubicacion">
                <ul id="suges" class="suggestions-list hidden">
                    <li class="li-label"><label><span class="text"><h3>Ubicaciones disponibles</h3></span></label></li>';
    $processed_values = array();
    foreach ($ubicaciones as $ubicacion) {
        if (!is_array($ubicacion)) {
            continue;
        }

        $label_raw = isset($ubicacion['label']) ? (string)$ubicacion['label'] : '';
        $value_raw = isset($ubicacion['value']) ? (string)$ubicacion['value'] : '';

        $ubicacion_label = ucwords(strtolower(trim($label_raw)));
        $ubicacion_value = strtolower(trim($value_raw)); // el código ya viene como 1234 o 1234-56

        if ($ubicacion_value === '') {
            continue;
        } // nada que pintar

        if (!in_array($ubicacion_value, $processed_values, true)) {
            $output .= '<li class="ubicacion_values" data-value="' . esc_attr($ubicacion_value) . '"><label>';
            $output .= '<span class="text">' . esc_html($ubicacion_label) . '</span>';
            $output .= '</label></li>';
            $processed_values[] = $ubicacion_value;
        }
    }
    $output .=        '</ul>
                </div>
                <input type="submit" value="Buscar vacante" id="boton">
            </form>
            </div>';

    return $output;
}

add_shortcode('searchFormHome', 'search_form_home_banner');

add_action('wp_login_failed', function () {
    $referrer = wp_get_referer();
    if ($referrer && !strstr($referrer, 'wp-login') && !strstr($referrer, 'wp-admin')) {
        wp_redirect(add_query_arg('login', 'failed', $referrer));
        exit;
    }
});

function manejar_errores_404($errno, $errstr, $errfile, $errline)
{
    error_log("Error en 404.php: [$errno] $errstr en $errfile línea $errline");
    return true; // Prevenir que PHP muestre el error
}
set_error_handler('manejar_errores_404');

function set_security_headers()
{
    header("X-Frame-Options: SAMEORIGIN");
    header("Content-Security-Policy: frame-ancestors 'self';");
}
add_action('send_headers', 'set_security_headers');

// // Mantener al usuario en la misma página después de iniciar sesión exitosamente
// add_filter('login_redirect', function($redirect_to, $request, $user) {
//     // Si hay un error, mantenemos la misma URL
//     if (isset($_GET['login']) && $_GET['login'] == 'failed') {
//         return wp_get_referer();
//     }

//     // Si el inicio de sesión es exitoso
//     if (isset($user->roles) && is_array($user->roles)) {
//         // Redireccionar a la misma página desde la que se envió el formulario
//         return wp_get_referer() ? wp_get_referer() : home_url();
//     }

//     // Redirección por defecto
//     return $redirect_to;
// }, 10, 3);


add_filter('login_redirect', 'custom_login_redirect', 10, 3);
function custom_login_redirect($redirect_to, $requested_redirect_to, $user)
{
    if (!empty($requested_redirect_to)) {
        return $requested_redirect_to;
    }
    return $redirect_to;
}
