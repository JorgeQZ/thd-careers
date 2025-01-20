<?php
function create_favorites_table() {
    global $wpdb;

    // Obtener el prefijo de la base de datos del sitio actual
    $prefix = $wpdb->get_blog_prefix();

    // Nombre de la tabla de favoritos
    $table_name = $prefix . 'favorites';

    // Crear la tabla solo si no existe
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();

        // Definir la consulta para crear la tabla
        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,   -- Campo ID único para cada fila
            user_id BIGINT(20) UNSIGNED NOT NULL,                -- ID del usuario
            job_id BIGINT(20) UNSIGNED NOT NULL,                 -- ID del trabajo (vacante)
            UNIQUE(user_id, job_id),                             -- Clave única combinada para evitar duplicados
            INDEX(user_id)                                      -- Añadir índice en user_id para optimizar las consultas por usuario
        ) $charset_collate;";  // Definir el conjunto de caracteres y la intercalación
        // Ejecutar la consulta
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}
add_action('after_switch_theme', 'create_favorites_table');  // O utilizar un hook adecuado para tu plugin

/**
 * Verificación de Creación de tabla
 */
function ensure_favorites_table_exists() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'favorites';

    // Verifica si la tabla existe
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        create_favorites_table();
    }
}
add_action('admin_init', 'ensure_favorites_table_exists');


// Función para agregar un favorito
function add_favorite() {
    // Verificamos si el usuario está logueado
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $job_id = $_POST['jobId'];

        global $wpdb;
        $table_name = $wpdb->prefix . 'favorites'; // Prefijo de tabla adecuado

        // Insertamos el favorito en la base de datos
        $wpdb->insert($table_name, array(
            'user_id' => $user_id,
            'job_id' => $job_id,
        ));

        // Enviamos la respuesta de éxito
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_add_favorite', 'add_favorite');

// Función para eliminar un favorito
function remove_favorite() {
    // Verificamos si el usuario está logueado
    if (is_user_logged_in()) {
        $user_id = $_POST['user_id'];
        $job_id = $_POST['job_id'];

        global $wpdb;
        $table_name = $wpdb->prefix . 'favorites'; // Prefijo de tabla adecuado

        // Eliminamos el favorito de la base de datos
        $wpdb->delete($table_name, array(
            'user_id' => $user_id,
            'job_id' => $job_id,
        ));

        // Enviamos la respuesta de éxito
        wp_send_json_success();
    } else {
        wp_send_json_error();
    }
}
add_action('wp_ajax_remove_favorite', 'remove_favorite');


add_action('wp_ajax_get_user_favorites', 'ajax_get_user_favorites');

function ajax_get_user_favorites($user_id) {
    // Verificar si el usuario está autenticado
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Usuario no autenticado'], 401);
    }

    // Obtener el ID del usuario actual
    $user_id = get_current_user_id();

    // Verificar si hay un error al obtener el ID del usuario
    if (!$user_id) {
        wp_send_json_error(['message' => 'Error al obtener el usuario actual'], 400);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'favorites'; // Asegúrate de usar el prefijo correcto

    // Consultar los favoritos del usuario
    $query = $wpdb->prepare("SELECT job_id FROM $table_name WHERE user_id = %d", $user_id);
    $favorites = $wpdb->get_results($query);

    // Verificar si hay resultados
    if (!$favorites) {
        wp_send_json_success(['favorites' => []], 200); // Responder con una lista vacía
    }

    // Devolver los favoritos en formato JSON
    wp_send_json_success(['favorites' => $favorites], 200);
}