<?php

/**
 * Creación de tabla
 */
function create_notifications_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'postulation_notifications';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
       id BIGINT(20) NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) NOT NULL,
        postulation_id BIGINT(20) NOT NULL,
        vacancy_id BIGINT(20) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('seen', 'unseen') DEFAULT 'unseen' NOT NULL,  -- Campo de estado de la notificación
        postulation_status ENUM('Rechazado', 'Aceptado', 'Visto', 'Postulado') DEFAULT 'Postulado' NOT NULL,  -- Campo nuevo para el estado de la postulación
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id),
        INDEX idx_user_id (user_id),
        INDEX idx_state (status),
        INDEX idx_user_status (user_id, status),
        INDEX idx_vacancy_id (vacancy_id),
        INDEX idx_postulation_status (postulation_status)  -- Index para el nuevo campo
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_action('after_switch_theme', 'create_notifications_table');

/**
 * Verificación de Creación de tabla
 */
function ensure_notifications_table_exists() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'postulation_notifications';

    // Verifica si la tabla existe
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        create_notifications_table();
    }
}
add_action('admin_init', 'ensure_notifications_table_exists');

/**
 * Agregar notifcacion de usuario
 */
function add_postulation_notification($user_id, $postulation_id, $message) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'postulation_notifications';

     // Sanitización de los datos de entrada
     $user_id = intval($user_id);
     $postulation_id = intval($postulation_id);
     $vacancy_id = intval($vacancy_id);
     $message = sanitize_text_field($message);
     $status = sanitize_text_field($status);
     $postulation_status = sanitize_text_field($postulation_status);


    // Validar el estado de la postulación
    $valid_postulation_status = ['rejected', 'accepted', 'pending'];
    if (!in_array($postulation_status, $valid_postulation_status)) {
        $postulation_status = 'pending';  // Asignar valor por defecto si no es válido
    }

    // Validar el estado de la notificación
    $valid_statuses = ['rejected', 'accepted', 'seen', 'unseen'];
    if (!in_array($status, $valid_statuses)) {
        $status = 'unseen';  // Asignar valor por defecto si no es válido
    }

    // Inserción en la tabla
    $wpdb->insert(
        $table_name,
        [
            'user_id' => $user_id,
            'postulation_id' => $postulation_id,
            'vacancy_id' => $vacancy_id,
            'message' => $message,
            'status' => $status,  // Estado de la notificación
            'postulation_status' => $postulation_status,  // Estado de la postulación
        ],
        ['%d', '%d', '%d', '%s', '%s', '%s']
    );
}

/**
 * Manejador AJAX para marcar una notificación como vista.
 */
function mark_notification_as_seen_ajax() {
    // Verificar si el usuario está logueado
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Usuario no autenticado'));
        return;
    }

    // Verificar que se pasó el ID de la notificación
    if (isset($_POST['notif_id']) && is_numeric($_POST['notif_id'])) {
        $notif_id = intval($_POST['notif_id']);
        $user_id = get_current_user_id();

        // Llamar a la función para actualizar el estado de la notificación
        $result = mark_single_notification_as_seen($notif_id, $user_id);

        if ($result) {
            wp_send_json_success(array('message' => 'Notificación marcada como vista'));
        } else {
            wp_send_json_error(array('message' => 'Error al marcar la notificación'));
        }
    } else {
        wp_send_json_error(array('message' => 'No se especificó el ID de la notificación'));
    }

    wp_die(  );
}

add_action('wp_ajax_mark_notification_as_seen', 'mark_notification_as_seen_ajax');

/**
 * Función para marcar una notificación como vista
 */
function mark_single_notification_as_seen($notif_id, $user_id) {
    global $wpdb;

    // Nombre de la tabla de notificaciones
    $table_name = $wpdb->prefix . 'postulation_notifications';

    // Actualizar la notificación a 'visto'
    $result = $wpdb->update(
        $table_name,
        ['status' => 'seen'],  // Nuevo estado
        [
            'id' => $notif_id,
            'user_id' => $user_id  // Asegurarse de que solo se actualicen las notificaciones del usuario
        ],
        ['%s'], // Tipo de datos para el campo 'state'
        ['%d', '%d'] // Tipo de datos para 'id' y 'user_id'
    );

    return $result !== false;
}

/**
 * Obtener notifcaciones de usuario
 */
function get_user_notifications($user_id, $page = 1, $per_page = 10) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'postulation_notifications';

    $offset = ($page - 1) * $per_page;

    // Recupera notificaciones filtradas por el estado de la postulación con paginación
    return $wpdb->get_results(
        $wpdb->prepare(
            "SELECT id,vacancy_id, message, postulation_status, status  FROM $table_name WHERE user_id = %d  ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $user_id,
            $per_page,
            $offset
        )
    );
}

/**
 * Limpiar notificaciones de mas de 90 dias
 */
function cleanup_old_notifications($days = 90) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'postulation_notifications';

    // Borra notificaciones más antiguas que el límite de días
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM $table_name WHERE created_at < NOW() - INTERVAL %d DAY",
            $days
        )
    );
}
add_action('wp_scheduled_cleanup', 'cleanup_old_notifications');

// Programar la limpieza automática (una vez al día)
if (!wp_next_scheduled('wp_scheduled_cleanup')) {
    wp_schedule_event(time(), 'daily', 'wp_scheduled_cleanup');
}

/**
 * Obtener número de notificaciones sin leer
 */
function get_unseen_notifications_count($user_id) {
    global $wpdb;

    // Intenta recuperar el conteo del cache
    $cache_key = "unseen_notifications_count_$user_id";
    $unseen_count = get_transient($cache_key);

    if ($unseen_count === false) {
        // Si no está en cache, consulta la base de datos
        $table_name = $wpdb->prefix . 'postulation_notifications';

        $unseen_count = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND state = 'unseen'",
                $user_id
            )
        );

        // Almacena el resultado en cache por 10 minutos
        set_transient($cache_key, $unseen_count, 10 * MINUTE_IN_SECONDS);
    }

    return $unseen_count;
}

